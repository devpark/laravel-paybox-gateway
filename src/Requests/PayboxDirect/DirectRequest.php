<?php

namespace Bnb\PayboxGateway\Requests\PayboxDirect;

use Bnb\PayboxGateway\HttpClient\GuzzleHttpClient;
use Bnb\PayboxGateway\Requests\Request;
use Bnb\PayboxGateway\Services\Amount;
use Bnb\PayboxGateway\Services\HmacHashGenerator;
use Bnb\PayboxGateway\Services\ServerSelector;
use Carbon\Carbon;
use Exception;
use Illuminate\Contracts\Config\Repository as Config;

abstract class DirectRequest extends Request
{

    /**
     * {@inheritdoc}
     */
    protected $type = 'paybox_direct';

    /**
     * @var GuzzleHttpClient
     */
    protected $client;

    /**
     * {@inheritdoc}
     */
    protected $amountFill = true;

    /**
     * Number of request in current day.
     *
     * @var int|null
     */
    protected $numRequest = null;

    /**
     * @var HmacHashGenerator
     */
    protected $hmacHashGenerator;

    /**
     * @var string|null
     */
    protected $archiveReference = null;


    /**
     * Capture constructor.
     *
     * @param ServerSelector    $serverSelector
     * @param Config            $config
     * @param HmacHashGenerator $hmacHashGenerator
     * @param Amount            $amountService
     * @param GuzzleHttpClient  $client
     */
    public function __construct(
        ServerSelector $serverSelector,
        Config $config,
        HmacHashGenerator $hmacHashGenerator,
        Amount $amountService,
        GuzzleHttpClient $client
    ) {
        parent::__construct($serverSelector, $config, $amountService);
        $this->hmacHashGenerator = $hmacHashGenerator;
        $this->client = $client;
    }


    /**
     * Set url for capture based on authorization url. If other is set to false, it will find
     * matching Paybox Direct server, otherwise it will try to find other Paybox Direct url.
     *
     * @param string $authorizationUrl
     * @param bool   $other
     *
     * @return $this
     */
    public function setUrlFrom($authorizationUrl, $other = false)
    {
        $this->url = $this->serverSelector->findFrom('paybox', $this->type, $authorizationUrl, $other);

        return $this;
    }


    /**
     * Set the archive reference transmitted to the bank (may be printed on bank statement)
     *
     * @param string $archiveReference
     *
     * @return $this
     */
    public function setArchiveReference($archiveReference)
    {
        $this->archiveReference = $archiveReference;

        return $this;
    }


    /**
     * Send Paybox Direct capture request and return response.
     *
     * @param array $parameters
     *
     * @return $this
     */
    public function send(array $parameters = [])
    {
        $parameters = $parameters ?: $this->getParameters();
        $responseClass = $this->getResponseClass();

        return new $responseClass($this->client->request($this->getUrl(), $parameters));
    }


    /**
     * Get the common parameters that will be send to Paybox Direct.
     *
     * @return array
     */
    public function getDefaultParameters()
    {
        $params = [
            'HASH' => 'SHA512',
            'VERSION' => '00104',
            'TYPE' => $this->getQuestionType(),
            'SITE' => $this->config->get('paybox.site'),
            'RANG' => $this->config->get('paybox.rank'),
            'NUMQUESTION' => $this->numRequest,
            'DATEQ' => $this->getFormattedDate($this->time ?: Carbon::now()),
        ];

        if ( ! empty($this->archiveReference)) {
            $params['ARCHIVAGE'] = $this->archiveReference;
        }

        return $params;
    }


    /**
     * Get parameters that will be send to Paybox Direct.
     *
     * @return array
     */
    public function getParameters()
    {
        $params = $this->getDefaultParameters() + $this->getBasicParameters();

        $params['HMAC'] = $this->hmacHashGenerator->get($params);

        return $params;
    }


    /**
     * Set request number in current day.
     *
     * @param $dayRequestNumber
     *
     * @return $this
     * @throws Exception
     */
    public function setDayRequestNumber($dayRequestNumber)
    {
        if ( ! is_int($dayRequestNumber)) {
            throw new Exception('Number of request should be integer');
        }

        if ($dayRequestNumber < 1 || $dayRequestNumber > 2147483647) {
            throw new Exception(('Number of request should in range <1,2147483647>'));
        }

        $this->numRequest = str_pad($dayRequestNumber, 10, '0', STR_PAD_LEFT);

        return $this;
    }


    /**
     * Get formatted date in format required by Paybox Direct.
     *
     * @param Carbon $date
     *
     * @return string
     */
    protected function getFormattedDate(Carbon $date)
    {
        return $date->format('dmYHis');
    }


    /**
     * @return string
     * @see QuestionTypeCode
     */
    public abstract function getQuestionType();


    /**
     * Get parameters that will be send to Paybox Direct.
     *
     * @return array
     */
    public abstract function getBasicParameters();


    /**
     * The response class name
     *
     * @var string
     */
    public abstract function getResponseClass();
}
