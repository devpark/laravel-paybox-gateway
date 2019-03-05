<?php

namespace Tests\Requests;

use Bnb\PayboxGateway\Currency;
use Bnb\PayboxGateway\HttpClient\GuzzleHttpClient;
use Bnb\PayboxGateway\Responses\PayboxDirect\Capture as CaptureResponse;
use Bnb\PayboxGateway\Services\Amount;
use Bnb\PayboxGateway\Services\ServerSelector;
use Carbon\Carbon;
use Exception;
use Illuminate\Contracts\Config\Repository as Config;
use Mockery as m;
use Tests\UnitTestCase;

class CaptureTest extends UnitTestCase
{

    protected $serverSelector;

    protected $config;

    protected $request;

    protected $amountService;

    protected $client;


    public function setUp(): void
    {
        parent::setUp();
        $this->serverSelector = m::mock(ServerSelector::class);
        $this->config = m::mock(Config::class);
        $this->amountService = m::mock(Amount::class);
        $this->client = m::mock(GuzzleHttpClient::class);
        $this->request = m::mock(\Bnb\PayboxGateway\Requests\PayboxDirect\Capture::class,
            [
                $this->serverSelector,
                $this->config,
                $this->amountService,
                $this->client,
            ])->makePartial()
            ->shouldAllowMockingProtectedMethods();
    }


    /** @test */
    public function getParameters_it_returns_valid_parameters()
    {
        $now = Carbon::now();
        Carbon::setTestNow($now);

        $sampleSite = 'SITE-NR';
        $sampleRank = 'SITE-RANK';

        $this->config->shouldReceive('get')->with('paybox.site')->once()->andReturn($sampleSite);
        $this->config->shouldReceive('get')->with('paybox.rank')->once()->andReturn($sampleRank);

        $parameters = $this->request->getParameters();

        $this->assertSame($sampleSite, $parameters['SITE']);
        $this->assertSame($sampleRank, $parameters['RANG']);
        $this->assertSame('00104', $parameters['VERSION']);
        $this->assertSame('00002', $parameters['TYPE']);
        $this->assertSame($now->format('dmYHis'), $parameters['DATEQ']);
        $this->assertSame(null, $parameters['NUMQUESTION']);
        $this->assertSame(null, $parameters['CLE']);
        $this->assertSame(null, $parameters['MONTANT']);
        $this->assertSame(null, $parameters['DEVISE']);
        $this->assertSame(null, $parameters['REFERENCE']);
        $this->assertSame(null, $parameters['NUMAPPEL']);
        $this->assertSame(null, $parameters['NUMTRANS']);
    }


    /** @test */
    public function setAmount_it_gets_valid_amount_and_currency_when_both_given()
    {
        $this->ignoreMissingMethods();
        $this->amountService->shouldReceive('get')->with(100.22, true)->once()
            ->andReturn('0000sample');
        $this->request->setAmount(100.22, Currency::CHF);
        $parameters = $this->request->getParameters();
        $this->assertSame('0000sample', $parameters['MONTANT']);
        $this->assertSame(Currency::CHF, $parameters['DEVISE']);
    }


    /** @test */
    public function setAmount_it_gets_valid_amount_and_currency_when_no_currency()
    {
        $this->ignoreMissingMethods();
        $this->amountService->shouldReceive('get')->with('100,4567', true)->once()
            ->andReturn('000sample2');
        $this->request->setAmount('100,4567');
        $parameters = $this->request->getParameters();
        $this->assertSame('000sample2', $parameters['MONTANT']);
        $this->assertSame(Currency::EUR, $parameters['DEVISE']);
    }


    /** @test */
    public function setTime_it_gets_valid_date_time_when_set()
    {
        $this->ignoreMissingMethods();
        $date = Carbon::now()->addDays(10);
        $this->request->setTime($date);
        $parameters = $this->request->getParameters();
        $this->assertSame($date->format('dmYHis'), $parameters['DATEQ']);
    }


    /** @test */
    public function setDayRequestNumber_it_throws_exception_when_number_is_not_integer()
    {
        $this->ignoreMissingMethods();

        $this->expectException(Exception::class);
        $this->expectExceptionMessage('Number of request should be integer');
        $this->request->setDayRequestNumber('123');
    }


    /** @test */
    public function setDayRequestNumber_it_throws_exception_when_number_is_too_low()
    {
        $this->ignoreMissingMethods();

        $this->expectException(Exception::class);
        $this->expectExceptionMessage('umber of request should in range <1,2147483647>');
        $this->request->setDayRequestNumber(0);
    }


    /** @test */
    public function setDayRequestNumber_it_throws_exception_when_number_is_too_high()
    {
        $this->ignoreMissingMethods();

        $this->expectException(Exception::class);
        $this->expectExceptionMessage('umber of request should in range <1,2147483647>');
        $this->request->setDayRequestNumber(2147483647 + 1);
    }


    /** @test */
    public function setDayRequestNumber_it_sets_valid_request_number()
    {
        $this->ignoreMissingMethods();
        $this->request->setDayRequestNumber(51234);
        $parameters = $this->request->getParameters();
        $this->assertSame('0000051234', $parameters['NUMQUESTION']);
    }


    /** @test */
    public function getUrl_it_fires_server_selector_once()
    {
        $validUrl = 'https://sample.com/valid/server/url';

        $this->serverSelector->shouldReceive('find')->once()->with('paybox_direct')
            ->andReturn($validUrl);

        $url = $this->request->getUrl();
        $this->assertSame($validUrl, $url);

        // now launch again - server should not be searched one more time but result should be same 
        $url = $this->request->getUrl();
        $this->assertSame($validUrl, $url);
    }


    /** @test */
    public function setPaymentNumber_it_gets_valid_payment_number()
    {
        $this->ignoreMissingMethods();
        $this->request->setPaymentNumber(123);
        $parameters = $this->request->getParameters();
        $this->assertSame(123, $parameters['REFERENCE']);
    }


    /** @test */
    public function send_it_sends_request_and_return_response_when_no_parameters_given()
    {
        $parameters = [
            'a' => 'b',
            'c' => 'd',
        ];
        $sampleUrl = 'https://example.com';
        $responseBody = 'foo=bar&x=z';

        $this->request->shouldReceive('getParameters')->withNoArgs()->once()
            ->andReturn($parameters);
        $this->request->shouldReceive('getUrl')->withNoArgs()->once()->andReturn($sampleUrl);
        $this->client->shouldReceive('request')->with($sampleUrl, $parameters)->once()
            ->andReturn($responseBody);

        $response = $this->request->send();
        $this->assertTrue($response instanceof CaptureResponse);
        $this->assertSame($responseBody, $response->getBody());
    }


    /** @test */
    public function send_it_sends_request_and_return_response_when_parameters_given()
    {
        $parameters = [
            'a' => 'b',
            'c' => 'd',
        ];
        $sampleUrl = 'https://example.com';
        $responseBody = 'foo=bar&x=z';

        $this->request->shouldNotReceive('getParameters');
        $this->request->shouldReceive('getUrl')->withNoArgs()->once()->andReturn($sampleUrl);
        $this->client->shouldReceive('request')->with($sampleUrl, $parameters)->once()
            ->andReturn($responseBody);

        $response = $this->request->send($parameters);
        $this->assertTrue($response instanceof CaptureResponse);
        $this->assertSame($responseBody, $response->getBody());
    }


    /** @test */
    public function setPayboxCallNumber_it_gets_valid_paybox_call_number()
    {
        $this->ignoreMissingMethods();
        $this->request->setPayboxCallNumber(671234);
        $parameters = $this->request->getParameters();
        $this->assertSame(671234, $parameters['NUMAPPEL']);
    }


    /** @test */
    public function setPayboxTransactionNumber_it_gets_valid_paybox_transaction_number()
    {
        $this->ignoreMissingMethods();
        $this->request->setPayboxTransactionNumber(54123123);
        $parameters = $this->request->getParameters();
        $this->assertSame(54123123, $parameters['NUMTRANS']);
    }


    /** @test */
    public function setUrlFrom_it_sets_valid_url_when_looking_for_matching_url()
    {
        $authorizationUrl = 'http://authorization.example.com';
        $finalUrl = 'http://final.example.com';

        $this->ignoreMissingMethods();
        $this->serverSelector->shouldReceive('findFrom')
            ->with('paybox', 'paybox_direct', $authorizationUrl, false)->once()
            ->andReturn($finalUrl);

        $this->request->setUrlFrom($authorizationUrl, false);
        $this->assertSame($finalUrl, $this->request->getUrl());
    }


    /** @test */
    public function setUrlFrom_it_sets_valid_url_when_looking_for_other_url()
    {
        $authorizationUrl = 'http://authorization.example.com';
        $finalUrl = 'http://final.example.com';

        $this->ignoreMissingMethods();
        $this->serverSelector->shouldReceive('findFrom')
            ->with('paybox', 'paybox_direct', $authorizationUrl, true)->once()
            ->andReturn($finalUrl);

        $this->request->setUrlFrom($authorizationUrl, true);
        $this->assertSame($finalUrl, $this->request->getUrl());
    }


    protected function ignoreMissingMethods()
    {
        $this->config->shouldIgnoreMissing();
    }
}
