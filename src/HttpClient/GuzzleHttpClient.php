<?php

namespace Bnb\PayboxGateway\HttpClient;

use GuzzleHttp\Client;

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
     */
    public function request($url, array $parameters)
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

        return (string)$response->getBody();
    }
}
