## Laravel Paybox Gateway

**This module is currently under development, please check later.** 


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
        
4. Open `config/paybox.php` and configure it according to your needs. By default you should put `PAYBOX_TEST`, `PAYBOX_SITE`, `PAYBOX_RANK`, `PAYBOX_ID` and  `PAYBOX_HMAC_KEY` into your `.env` file and fill them with valid values. For testing you should set `PAYBOX_TEST` to true, otherwise it should be set to false.  
     
5. In your routes file register routes with names defined in `customer_return_routes_names` and `transaction_verify_route_name` groups of your `config/paybox.php`

### Usage

In order to use the system, you need to do a few things:

1. You need to launch the authorization request in order to init payment

2. You need to handle customer returning routes to display message to customer. By default there are 4 different routes depending on final situation on transaction. Be aware you should never use those routes to change status of payment because in fact it's not 100% sure at this stage. You should use handle transaction routes in order to do that

3. You should handle transaction verify route. Here you should change status of payment after receiving request and make any additional actions.

#### Authorization request
        
This module uses Paybox system in order to make payment so it means, customer is redirected to Paybox website in order to make payment. However you can do it using 2 strategies - you can either make customer to be charged as soon as possible (what should be used in most cases) or you might want to charge customer only in some cases (for example when shop's staff will accept client's order). 
        
The most basic sample code for authorization request looks like this:

```php
$authorizationRequest = \App::make(\Devpark\PayboxGateway\Requests\AuthorizationWithCapture::class);

return $authorizationRequest->setAmount(100)->setCustomerEmail('test@example.com')
            ->setPaymentNumber(1)->send('paybox.send');
```            
This code should be run in controller as it's returning view which will by default automatically redirect customer to Paybox website.

Above sample if with capture (customer will be charged as soon as possible). If you want to charge customer later use `AuthorizationWithoutCapture` instead. 

If you want more customization take a look at public methods of  `\Devpark\PayboxGateway\Requests\Authorization` class.

For `setAmount` default currency is Euro. If you want to use other currency, you should use currency constant from `\Devpark\PayboxGateway\Currency` class as 2nd parameter. Also please notice that amount you should give to this function is real amount (with decimal places) and not converted already to Paybox format.  

Also for `setPaymentNumber` you should make sure the number you gave here is unique for each call. That's why you should probably create payments table for each order and depending on your system, you might need to assume there are more than one payment for your order (for example someone first cancelled it, but later if you gave him such option they decided to make the payment again).

You might want in this step adjust also view for sending request because in some cases it might be seen by a client. However you shouldn't change fields you send to Paybox in this step or it won't work.

In case you use `AuthorizationWithoutCapture` you should make sure, you have `\Devpark\PayboxGateway\ResponseField::SUBSCRIPTION_CARD_OR_PAYPAL_AUTHORIZATION` in your return fields because this value will be needed when capturing payment later.  You should also always have `\Devpark\PayboxGateway\ResponseField::AUTHORIZATION_NUMBER` and `\Devpark\PayboxGateway\ResponseField::SIGNATURE` in your return fields and signature should be always last parameter. 

#### Define customer returning routes

By default 4 sample views were created with sample English texts. You should create routes that will display those views (those routes will be launched using `GET` HTTP method), adjust those views and in most cases it will be enough because you should not rely on data you receive from Paybox here.

#### Handling transaction verify route