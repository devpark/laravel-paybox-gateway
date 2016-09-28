<?php

namespace Devpark\PayboxGateway\Responses;

class Capture
{
    /**
     * Response body.
     *
     * @var string
     */
    protected $body;

    /**
     * Capture constructor.
     *
     * @param $body
     */
    public function __construct($body)
    {
        $this->body = $body;
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
}
