<?php

namespace Bnb\PayboxGateway\Requests;

use Carbon\Carbon;
use Bnb\PayboxGateway\Currency;
use Bnb\PayboxGateway\Services\ServerSelector;
use Illuminate\Contracts\Config\Repository as Config;
use Bnb\PayboxGateway\Services\Amount;

abstract class Request
{
    /**
     * Type of gateway.
     *
     * @var string|null
     */
    protected $type = null;

    /**
     * Selected server to send request.
     *
     * @var ServerSelector
     */
    protected $serverSelector;

    /**
     * @var string|null
     */
    protected $url = null;

    /**
     * @var Amount
     */
    protected $amountService;

    /**
     * @var Config
     */
    protected $config;

    /**
     * @var int|null
     */
    protected $amount = null;

    /**
     * @var string|null
     */
    protected $currencyCode = null;

    /**
     * @var string|null
     */
    protected $time = null;

    /**
     * @var string|null
     */
    protected $paymentNumber = null;

    /**
     * Whether extra filling should be done when formatting amount.
     *
     * @var bool
     */
    protected $amountFill = false;

    /**
     * Request constructor.
     *
     * @param ServerSelector $serverSelector
     * @param Config $config
     * @param Amount $amountService
     */
    public function __construct(
        ServerSelector $serverSelector,
        Config $config,
        Amount $amountService
    ) {
        $this->serverSelector = $serverSelector;
        $this->config = $config;
        $this->amountService = $amountService;
    }

    /**
     * Get url for sending request.
     *
     * @return ServerSelector|string
     */
    public function getUrl()
    {
        if ($this->url === null) {
            $this->url = $this->serverSelector->find($this->type);
        }

        return $this->url;
    }

    /**
     * Set amount and currency code.
     *
     * @param float $amount
     * @param string $currencyCode
     *
     * @return $this
     */
    public function setAmount($amount, $currencyCode = Currency::EUR)
    {
        $this->amount = $this->amountService->get($amount, $this->amountFill);
        $this->currencyCode = $currencyCode;

        return $this;
    }

    /**
     * Set payment number.
     *
     * @param string $number
     *
     * @return $this
     */
    public function setPaymentNumber($number)
    {
        $this->paymentNumber = $number;

        return $this;
    }

    /**
     * Set time.
     *
     * @param Carbon $date
     *
     * @return $this
     */
    public function setTime(Carbon $date)
    {
        $this->time = $date;

        return $this;
    }
}
