<?php

namespace Bnb\PayboxGateway\HttpClient;

use GuzzleHttp\Client;
use Psr\Http\Message\ResponseInterface;

class GuzzleHttpClient
{

    /**
     * GuzzleHttpClient constructor.
     *
     * @param Client $client
     */
    public function __construct(Client $client)
    {
        $this->client = $client;
    }


    /**
     * Make POST Http request to given url.
     *
     * @param string $url
     * @param array  $parameters
     *
     * @return string
     * @throws \GuzzleHttp\Exception\GuzzleException
     */
    public function request($url, array $parameters)
    {
        return (string)$this->requestRaw($url, $parameters)->getBody();
    }


    /**
     * Make POST Http request to given url.
     *
     * @param string $url
     * @param array  $parameters
     *
     * @return ResponseInterface
     * @throws \GuzzleHttp\Exception\GuzzleException
     */
    public function requestRaw($url, array $parameters)
    {
        if (method_exists($this->client, 'request')) {
            $response = $this->client->request('POST', $url, [
                'form_params' => $parameters,
            ]);
        } else {
            $response = $this->client->post($url, [
                'body' => $parameters,
            ]);
        }

        return $response;
    }
}
