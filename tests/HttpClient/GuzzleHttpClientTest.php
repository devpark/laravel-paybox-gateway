<?php

namespace Tests\HttpClient;

use Devpark\PayboxGateway\HttpClient\GuzzleHttpClient;
use GuzzleHttp\Client;
use stdClass;
use Tests\UnitTestCase;
use Mockery as m;

class GuzzleHttpClientTest extends UnitTestCase
{
    /** @test */
    public function it_runs_valid_request()
    {
        $client = m::mock(Client::class);
        $response = m::mock(stdClass::class);
        $url = 'http://example.com';
        $parameters = ['a' => 'b', 'c' => 'd'];
        $responseBody = 'foo=bar&baz=foo';

        $guzzleClient = m::mock(GuzzleHttpClient::class, [$client])->makePartial();

        $client->shouldReceive('request')->with('POST', $url, ['form_params' => $parameters])
            ->once()->andReturn($response);

        $response->shouldReceive('getBody')->once()->andReturn($responseBody);

        $response = $guzzleClient->request($url, $parameters);

        $this->assertSame($responseBody, $response);
    }
}
