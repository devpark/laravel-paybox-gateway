<?php

namespace Devpark\PayboxGateway\HttpClient;

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
     * @param array $parameters
     *
     * @return string
     */
    public function request($url, array $parameters)
    {
        $response = $this->client->request('POST', $url, [
            'form_params' => $parameters,
        ]);

        return (string) $response->getBody();
    }
}
