## Laravel Paybox Gateway

This module makes integration with **[Paybox](http://www1.paybox.com/?lang=en)** payment gateway much easier. It supports currently 2 ways of making payments using Paybox system.
 
 1. **Full payment via Paybox System** - this is the most common way to receive payment - client has to pay for his order and after payment you can process the order
 2. **Authorization via Paybox System followed by capture via Paybox Direct** - first client makes payment but in fact the real payment is not made, payment is only authorized so far. In maximum period of 7 days you have to confirm you want to/are able to process the order and you capture the payment. After successful payment capture you can process the order.

### Installation

1. Run

   ```php   
   composer require devpark/laravel-paybox-gateway
   ``` 
   
   in console to install this module
   
   
2. Open `config/app.php` and add
    
   ```php
   Devpark\PayboxGateway\Providers\PayboxServiceProvider::class,
   ```
        
   in section `providers`
          
3. Run

    ```php
    php artisan vendor:publish --provider="Devpark\PayboxGateway\Providers\PayboxServiceProvider"
    ```
    
    in your console to publish default configuration files and sample views
        
4. Open `config/paybox.php` and configure it according to your needs. By default you should put the following variables into your `.env` file and fill them with valid values:
 
 * `PAYBOX_TEST` - whether Paybox test system should be used (it should be set to `true` only for tests) , 
 * `PAYBOX_SITE` - Site number provided by Paybox
 * `PAYBOX_RANK` - Rank number provided by Paybox
 * `PAYBOX_ID` - Internal identifier provided by Paybox
 * `PAYBOX_BACK_OFFICE_PASSWORD` - Paybox back-office password. It's required only if you are going to make `Capture` requests. Otherwise it's not recommended to fill it (it won't be used)
 * `PAYBOX_HMAC_KEY` - This is key you should generate in your Paybox back-office 
  
5. Download [Paybox public key](http://www1.paybox.com/espace-integrateur-documentation/manuels/?lang=en) and put it in directory and name you specified in `config/paybox.php` for `public_key` key
     
6. In your routes file register routes with names defined in `customer_return_routes_names` and `transaction_verify_route_name` groups of your `config/paybox.php`

### Usage

In order to use the system, you need to do a few things:

1. You need to launch the authorization request in order to init payment

2. You need to handle customer returning routes to display message to customer. By default there are 4 different routes depending on final situation on transaction. Be aware you should never use those routes to change status of payment because in fact it's not 100% sure at this stage. You should use handle transaction routes in order to do that

3. You should handle transaction verify route. Here you should change status of payment after receiving request and make any additional actions.

4. In case you use want to capture previously authorized payments, you should also handle capturing previous payments.

#### Authorization request

This is main request you need to launch to init payment. 
        
The most basic sample code for authorization request could look like this:

```php
$authorizationRequest = \App::make(\Devpark\PayboxGateway\Requests\AuthorizationWithCapture::class);

return $authorizationRequest->setAmount(100)->setCustomerEmail('test@example.com')
            ->setPaymentNumber(1)->send('paybox.send');
```            
This code should be run in controller as it's returning view which will by default automatically redirect customer to Paybox website.

In above sample code the full payment is made. If you want to only authorize payment (which you will capture later) you should use `AuthorizationWithoutCapture` class instead of `AuthorizationWithCapture`

If you want more customization take a look at public methods of  `\Devpark\PayboxGateway\Requests\Authorization` class.

For `setAmount` default currency is Euro. If you want to use other currency, you should use currency constant from `\Devpark\PayboxGateway\Currency` class as 2nd parameter. Also please notice that amount you should give to this function is real amount (with decimal places) and not converted already to Paybox format.  

Also for `setPaymentNumber` you should make sure the number you gave here is unique for each call. That's why you should probably create payments table for each order and depending on your system, you might need to assume there are more than one payment for your order (for example someone first cancelled it, but later if you gave him such option they decided to make the payment again).

You might want in this step adjust also view for sending request because in some cases it might be seen by a client. However you shouldn't change fields you send to Paybox in this step or it won't work.

In case you use `AuthorizationWithoutCapture` you should make sure, you have `\Devpark\PayboxGateway\ResponseField::PAYBOX_CALL_NUMBER` and `\Devpark\PayboxGateway\ResponseField::TRANSACTION_NUMBER` in your return fields because those values will be needed when capturing payment later.  You should also always have `\Devpark\PayboxGateway\ResponseField::AUTHORIZATION_NUMBER` and `\Devpark\PayboxGateway\ResponseField::SIGNATURE` in your return fields and signature should be always last parameter. 

#### Define customer returning routes

By default 4 sample views were created with sample English texts. You should create routes that will display those views (those routes will be launched using `GET` HTTP method), adjust those views and in most cases it will be enough because the real status of payment will be verified using transaction verify route.

#### Handling transaction verify route

To make sure the payment was really successful you should use `\Devpark\PayboxGateway\Responses\Verify` class. The simplest code could look like this:

```php
$payment = Payment::where('number', $request->input('order_number'))->firstOrFail();
$payboxVerify = \App::make(\Devpark\PayboxGateway\Responses\Verify::class);
try {
    $success = $payboxVerify->isSuccess($payment->amount);
    if ($success) {
       // process order here after making sure it was real payment
    }
    echo "OK";
}
catch (InvalidSignature $e) {
    Log::alert('Invalid payment signature detected');
}
```

This code should be run in controller, because you should return non-empty response when receiving valid Paybox request for transaction verify route. As you see, first you need to find order by number and then you need to make sure that it was successful. If yes, you should make sure it was real payment before you process the order (if you use full payment it will be always true but in case in your application you use also authorization only payments with later capture you should make sure you won't process the order for authorization only payment).
 
#### Capturing previously authorized request

In case you use **Authorization via Paybox System followed by capture via Paybox Direct** you are going to finally capture previously authorized payment (you have up-to 7 days to do that).
 
The simplest code could look like this:

```php
$payment = PaymentModel::find($idOfAuthorizedPayment);
$captureRequest = \App::make(\Devpark\PayboxGateway\Requests\Capture::class);
$response = $captureRequest->setAmount($payment->amount)
                           ->setPayboxCallNumber($payment->call_number)
                           ->setPayboxTransactionNumber($payment->transaction_number)
                           ->setDayRequestNumber(2)
                           ->send();
                           
if ($response->isSuccess()) {
     // process order here                
}
```

In above code you should make sure value you give for `setDayRequestNumber` is unique number in current day from 1 to 2147483647. For `setPayboxCallNumber` and `setPayboxTransactionNumber` you should use values you received in handling `Handling transaction verify route` step so you should probably save them in this step to use them here.

### Licence

This package is licenced under the [MIT license](http://opensource.org/licenses/MIT)