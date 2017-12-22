<?php

namespace Bnb\PayboxGateway\Requests\PayboxDirect;

use Bnb\PayboxGateway\DirectQuestionField;
use Bnb\PayboxGateway\HttpClient\GuzzleHttpClient;
use Bnb\PayboxGateway\Models\Question;
use Bnb\PayboxGateway\Models\Response;
use Bnb\PayboxGateway\Requests\Request;
use Bnb\PayboxGateway\Services\Amount;
use Bnb\PayboxGateway\Services\HmacHashGenerator;
use Bnb\PayboxGateway\Services\ServerSelector;
use Carbon\Carbon;
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
     * @var HmacHashGenerator
     */
    protected $hmacHashGenerator;

    /**
     * @var string|null
     */
    protected $archiveReference = null;

    /**
     * The masked fields in database
     *
     * @var array
     */
    protected $masked = [
        DirectQuestionField::CARD_OR_WALLET_NUMBER,
        DirectQuestionField::CARD_CONTROL_NUMBER,
    ];


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
     * Set the archive reference transmitted to the bank (should be unique between 1 and 12 chars)
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
     * @return \Bnb\PayboxGateway\Responses\PayboxDirect\Response
     */
    public function send(array $parameters = [])
    {
        $parameters = $parameters ?: $this->getParameters();
        $responseClass = $this->getResponseClass();

        /** @var \Bnb\PayboxGateway\Responses\PayboxDirect\Response $response */
        $response = new $responseClass($this->client->request($this->getUrl(), $parameters));
        $response->setModel(Response::create($this->buildResponseAttributes($response->getFields())));

        return $response;
    }


    /**
     * Get the common parameters that will be send to Paybox Direct.
     *
     * @return array
     */
    public function getDefaultParameters()
    {
        $params = [
            DirectQuestionField::HASH => 'SHA512',
            DirectQuestionField::PAYBOX_VERSION => '00104',
            DirectQuestionField::PAYBOX_TYPE => $this->getQuestionType(),
            DirectQuestionField::PAYBOX_SITE => $this->config->get('paybox.site'),
            DirectQuestionField::PAYBOX_RANK => $this->config->get('paybox.rank'),
            DirectQuestionField::PAYBOX_QUESTION_DATE => $this->getFormattedDate($this->time ?: Carbon::now()),
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

        foreach ($this->masked as $field) {
            $this->storeMaskedField($field, $params, $originals);
        }

        $question = Question::create($this->buildQuestionAttributes($params));
        $params = array_change_key_case($question->toArray(), CASE_UPPER);

        foreach ($this->masked as $field) {
            $this->restoreMaskedField($field, $params, $originals);
        }

        $params[DirectQuestionField::PAYBOX_QUESTION_NUMBER] = $question->numquestion;
        $params[DirectQuestionField::HMAC] = $this->hmacHashGenerator->get($params);

        return $params;
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
     * @param array $params
     *
     * @return array
     */
    protected function buildQuestionAttributes(array $params)
    {
        return array_change_key_case($params, CASE_LOWER);
    }


    /**
     * @param array $params
     *
     * @return array
     */
    protected function buildResponseAttributes(array $params)
    {
        return array_change_key_case($params, CASE_LOWER);
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


    /*
     * Internal helpers
     */

    private function storeMaskedField($key, $params, &$originals)
    {
        if ( ! empty($params[$key])) {
            $originals[$key] = $params[$key];
        }
    }


    private function restoreMaskedField($key, &$params, $originals)
    {
        if ( ! empty($originals[$key])) {
            $params[$key] = $originals[$key];
        }
    }
}
