<?php

namespace Devpark\PayboxGateway\Requests;

use Carbon\Carbon;
use Devpark\PayboxGateway\Language;
use Devpark\PayboxGateway\Services\Amount;
use Devpark\PayboxGateway\Services\HmacHashGenerator;
use Devpark\PayboxGateway\Services\ServerSelector;
use Devpark\PayboxGateway\Utils\XmlUtils;
use Illuminate\Contracts\Config\Repository as Config;
use Illuminate\Contracts\View\Factory as ViewFactory;
use Illuminate\Routing\Router;
use Illuminate\Contracts\Routing\UrlGenerator;

abstract class Authorization extends Request
{
    /**
     * {@inheritdoc}
     */
    protected $type = 'paybox';

    /**
     * Interface language.
     *
     * @var string
     */
    protected $language = Language::FRENCH;

    /**
     * @var string|null
     */
    protected $customerEmail = null;

    /**
     * @var array|null
     */
    protected $returnFields = null;

    /**
     * @var string|null
     */
    protected $customerPaymentAcceptedUrl = null;

    /**
     * @var string|null
     */
    protected $customerPaymentRefusedUrl = null;

    /**
     * @var string|null
     */
    protected $customerPaymentAbortedUrl = null;

    /**
     * @var string|null
     */
    protected $customerPaymentWaitingUrl = null;

    /**
     * @var string|null
     */
    protected $transactionVerifyUrl = null;

    /**
     * @var HmacHashGenerator
     */
    protected $hmacHashGenerator;

    /**
     * @var Router
     */
    protected $router;

    /**
     * @var UrlGenerator
     */
    protected $urlGenerator;

    /**
     * @var ViewFactory
     */
    protected $view;

    /**
     * @var array|null
     */
    protected $shoppingCart = null;

    /**
     * @var array|null
     */
    protected $billing = null;

    /**
     * Authorization constructor.
     *
     * @param ServerSelector $serverSelector
     * @param Config $config
     * @param HmacHashGenerator $hmacHashGenerator
     * @param UrlGenerator $urlGenerator
     * @param ViewFactory $view
     * @param Amount $amountService
     */
    public function __construct(
        ServerSelector $serverSelector,
        Config $config,
        HmacHashGenerator $hmacHashGenerator,
        UrlGenerator $urlGenerator,
        ViewFactory $view,
        Amount $amountService
    ) {
        parent::__construct($serverSelector, $config, $amountService);
        $this->hmacHashGenerator = $hmacHashGenerator;
        $this->urlGenerator = $urlGenerator;
        $this->view = $view;
    }

    /**
     * Get parameters that are required to make request.
     *
     * @return array
     */
    public function getParameters()
    {
        $params = $this->getBasicParameters();

        $params['PBX_HMAC'] = $this->hmacHashGenerator->get($params);

        return $params;
    }

    /**
     * Get basic parameters (all parameters except HMAC hash).
     *
     * @return array
     */
    protected function getBasicParameters()
    {
        $parameters = [
            'PBX_SITE' => $this->config->get('paybox.site'),
            'PBX_RANG' => $this->config->get('paybox.rank'),
            'PBX_IDENTIFIANT' => $this->config->get('paybox.id'),
            'PBX_TOTAL' => $this->amount,
            'PBX_DEVISE' => $this->currencyCode,
            'PBX_LANGUE' => $this->language,
            'PBX_CMD' => $this->paymentNumber,
            'PBX_HASH' => 'SHA512',
            'PBX_PORTEUR' => $this->customerEmail,
            'PBX_RETOUR' => $this->getFormattedReturnFields(),
            'PBX_TIME' => $this->getFormattedDate($this->time ?: Carbon::now()),
            'PBX_EFFECTUE' => $this->getCustomerUrl('customerPaymentAcceptedUrl', 'accepted'),
            'PBX_REFUSE' => $this->getCustomerUrl('customerPaymentRefusedUrl', 'refused'),
            'PBX_ANNULE' => $this->getCustomerUrl('customerPaymentAbortedUrl', 'aborted'),
            'PBX_ATTENTE' => $this->getCustomerUrl('customerPaymentWaitingUrl', 'waiting'),
            'PBX_REPONDRE_A' => $this->getTransactionUrl(),
        ];

        if (!is_null($this->shoppingCart)) {
            $parameters['PBX_SHOPPINGCART'] = $this->getShoppingCart();
        }
        if (!is_null($this->billing)) {
            $parameters['PBX_BILLING'] = $this->getBilling();
        }

        return $parameters;
    }

    /**
     * Set interface language.
     *
     * @param string $language
     *
     * @return $this
     */
    public function setLanguage($language)
    {
        $this->language = $language;

        return $this;
    }

    /**
     * Set customer e-mail.
     *
     * @param string $email
     *
     * @return $this
     */
    public function setCustomerEmail($email)
    {
        $this->customerEmail = $email;

        return $this;
    }

    /**
     * Get formatted date in format required by Paybox.
     *
     * @param Carbon $date
     *
     * @return string
     */
    protected function getFormattedDate(Carbon $date)
    {
        return $date->format('c');
    }

    /**
     * Set return fields that will be when Paybox redirects back to website.
     *
     * @param array $returnFields
     *
     * @return $this
     */
    public function setReturnFields(array $returnFields)
    {
        $this->returnFields = $returnFields;

        return $this;
    }

    /**
     * Get return fields formatted in valid way.
     *
     * @return string
     */
    protected function getFormattedReturnFields()
    {
        $returnFields = (array) ($this->returnFields ?: $this->config->get('paybox.return_fields'));

        return collect($returnFields)->map(function ($value, $key) {
            return $key . ':' . $value;
        })->implode(';');
    }

    /**
     * Set back url for customer when payment is accepted.
     *
     * @param string $url
     *
     * @return $this
     */
    public function setCustomerPaymentAcceptedUrl($url)
    {
        $this->customerPaymentAcceptedUrl = $url;

        return $this;
    }

    /**
     * Set back url for customer when payment is refused.
     *
     * @param string $url
     *
     * @return $this
     */
    public function setCustomerPaymentRefusedUrl($url)
    {
        $this->customerPaymentRefusedUrl = $url;

        return $this;
    }

    /**
     * Set back url for customer when payment is aborted.
     *
     * @param string $url
     *
     * @return $this
     */
    public function setCustomerPaymentAbortedUrl($url)
    {
        $this->customerPaymentAbortedUrl = $url;

        return $this;
    }

    /**
     * Set back url for customer when payment is waiting.
     *
     * @param string $url
     *
     * @return $this
     */
    public function setCustomerPaymentWaitingUrl($url)
    {
        $this->customerPaymentWaitingUrl = $url;

        return $this;
    }

    /**
     * Set url for transaction verification.
     *
     * @param string $url
     *
     * @return $this
     */
    public function setTransactionVerifyUrl($url)
    {
        $this->transactionVerifyUrl = $url;

        return $this;
    }

    /**
     * Set shopping cart variables
     *
     * @param string $totalQuantity
     *
     * @return $this
     */
    public function setShoppingCart($totalQuantity = 1)
    {
        $this->shoppingCart = [
            'total' => [
                'totalQuantity' => $totalQuantity
            ]
        ];

        return $this;
    }

    /**
     * Return PBX_SHOPPINGCART
     *
     * @return string
     */
    public function getShoppingCart()
    {
        return XmlUtils::arrayToXmlString($this->shoppingCart, 'shoppingcart');
    }

    /**
     * Set billing variables
     *
     * @param string $firstname
     * @param string $lastname
     * @param string $address
     * @param string $zipcode
     * @param string $city
     * @param int $countryCode
     *
     * @return $this
     */
    public function setBilling($firstname, $lastname, $address, $zipcode, $city, $countryCode = 250)
    {
        $this->billing = [
            'Address' => [
                'FirstName' => $firstname,
                'LastName' => $lastname,
                'Address1' => $address,
                'ZipCode' => $zipcode,
                'City' => $city,
                'CountryCode' => $countryCode, // ISO_3166-1
            ]
        ];

        return $this;
    }

    /**
     * Return PBX_BILLING
     *
     * @return string
     */
    public function getBilling()
    {
        return XmlUtils::arrayToXmlString($this->billing, 'Billing');
    }

    /**
     * Get customer url.
     *
     * @param string $variableName
     * @param string $configKey
     *
     * @return string
     */
    protected function getCustomerUrl($variableName, $configKey)
    {
        return $this->$variableName ?: $this->urlGenerator->route(
            $this->config->get('paybox.customer_return_routes_names.' . $configKey)
        );
    }

    /**
     * Get transaction url.
     *
     * @return string
     */
    protected function getTransactionUrl()
    {
        return $this->transactionVerifyUrl ?: $this->urlGenerator->route(
            $this->config->get('paybox.transaction_verify_route_name')
        );
    }

    /**
     * Send request with authorization.
     *
     * @param string $viewName
     * @param array $parameters
     *
     * @return \Illuminate\Contracts\View\View
     */
    public function send($viewName, array $parameters = [])
    {
        $parameters = $parameters ?: $this->getParameters();

        return $this->view->make(
            $viewName,
            ['parameters' => $parameters, 'url' => $this->getUrl()]
        );
    }
}
