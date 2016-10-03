<?php

namespace Devpark\PayboxGateway\Responses;

use Devpark\PayboxGateway\DirectResponseCode;

class Capture
{
    /**
     * Response body.
     *
     * @var string
     */
    protected $body;

    /**
     * Response fields.
     *
     * @var array
     */
    protected $fields = [];

    /**
     * Capture constructor.
     *
     * @param $body
     */
    public function __construct($body)
    {
        $this->body = $body;
        $this->setFields();
    }

    /**
     * Get response body.
     *
     * @return string
     */
    public function getBody()
    {
        return $this->body;
    }

    /**
     * Get fields from body.
     *
     * @return array
     */
    public function getFields()
    {
        return $this->fields;
    }

    /**
     * Verify whether request was successful.
     *
     * @return bool
     */
    public function isSuccess()
    {
        return $this->fields['CODEREPONSE'] == DirectResponseCode::SUCCESS;
    }

    /**
     * Get Paybox response code.
     *
     * @return string
     */
    public function getResponseCode()
    {
        return $this->fields['CODEREPONSE'];
    }

    /**
     * Verify whether request should be repeated to secondary server.
     *
     * @return bool
     */
    public function shouldBeRepeated()
    {
        return collect([
            DirectResponseCode::CONNECTION_FAILED,
            DirectResponseCode::TIMEOUT,
            DirectResponseCode::INTERNAL_TIMEOUT,
        ])->contains($this->fields['CODEREPONSE']);
    }

    /**
     * Set fields from response body.
     */
    protected function setFields()
    {
        $fields = explode('&', $this->body);

        array_walk($fields, function (&$item, &$key) {
            list($key, $item) = explode('=', $item);
            $this->fields[$key] = $item;
        });
    }
}
