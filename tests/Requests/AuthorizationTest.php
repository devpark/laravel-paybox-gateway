<?php

namespace Tests\Requests;

use Carbon\Carbon;
use Devpark\PayboxGateway\Currency;
use Devpark\PayboxGateway\Language;
use Tests\Helpers\Authorization as AuthorizationHelper;
use Tests\UnitTestCase;

class AuthorizationTest extends UnitTestCase
{
    use AuthorizationHelper;

    public function setUp()
    {
        parent::setUp();
        $this->setUpMocks();
    }

    /** @test */
    public function getParameters_it_returns_all_parameters()
    {
        $sampleParameters = ['a' => 'b', 'c' => 'd', 'e' => 'f'];
        $sampleHmac = 'sampleHmacHash';

        $this->request->shouldReceive('getBasicParameters')->withNoArgs()->once()
            ->andReturn($sampleParameters);

        $this->hmacHashGenerator->shouldReceive('get')->with($sampleParameters)->once()
            ->andReturn($sampleHmac);

        $parameters = $this->request->getParameters();

        $this->assertEquals($sampleParameters + ['PBX_HMAC' => $sampleHmac], $parameters);
    }

    /** @test */
    public function getParameters_it_returns_valid_parameters()
    {
        $sampleHmac = 'sampleHmacHash';

        $this->request->shouldReceive('getBasicParameters')->withNoArgs()->once()
            ->passthru();

        $sampleSite = 'SITE-NR';
        $sampleRank = 'SITE-RANK';
        $sampleId = 'SITE-ID';
        $defaultParameters = ['a' => 'b', 'c' => 'd', 'e' => 'fg'];
        $acceptedRoute = 'paybox.accepted';
        $acceptedUrl = 'http://example.com/accepted-url';
        $refusedRoute = 'paybox.refused';
        $refusedUrl = 'http://example.com/refused-url';
        $abortedRoute = 'paybox.aborted';
        $abortedUrl = 'http://example.com/aborted-url';
        $waitingRoute = 'paybox.waiting';
        $waitingUrl = 'http://example.com/waiting-url';
        $transactionRoute = 'paybox.transaction';
        $transactionUrl = 'http://example.com/transaction-url';

        $this->config->shouldReceive('get')->with('paybox.site')->once()->andReturn($sampleSite);
        $this->config->shouldReceive('get')->with('paybox.rank')->once()->andReturn($sampleRank);
        $this->config->shouldReceive('get')->with('paybox.id')->once()->andReturn($sampleId);

        $this->request->shouldReceive('getFormattedReturnFields')->withNoArgs()->once()->passthru();
        $this->config->shouldReceive('get')->with('paybox.return_fields')->once()
            ->andReturn($defaultParameters);

        $this->config->shouldReceive('get')->with('paybox.customer_return_routes_names.accepted')
            ->once()->andReturn($acceptedRoute);
        $this->urlGenerator->shouldReceive('route')->with($acceptedRoute)->once()
            ->andReturn($acceptedUrl);
        $this->config->shouldReceive('get')->with('paybox.customer_return_routes_names.refused')
            ->once()->andReturn($refusedRoute);
        $this->urlGenerator->shouldReceive('route')->with($refusedRoute)->once()
            ->andReturn($refusedUrl);
        $this->config->shouldReceive('get')->with('paybox.customer_return_routes_names.aborted')
            ->once()->andReturn($abortedRoute);
        $this->urlGenerator->shouldReceive('route')->with($abortedRoute)->once()
            ->andReturn($abortedUrl);
        $this->config->shouldReceive('get')->with('paybox.customer_return_routes_names.waiting')
            ->once()->andReturn($waitingRoute);
        $this->urlGenerator->shouldReceive('route')->with($waitingRoute)->once()
            ->andReturn($waitingUrl);

        $this->config->shouldReceive('get')->with('paybox.transaction_verify_route_name')
            ->once()->andReturn($transactionRoute);
        $this->urlGenerator->shouldReceive('route')->with($transactionRoute)->once()
            ->andReturn($transactionUrl);

        $this->hmacHashGenerator->shouldReceive('get')->once()->andReturn($sampleHmac);

        $parameters = $this->request->getParameters();

        $this->assertSame($sampleSite, $parameters['PBX_SITE']);
        $this->assertSame($sampleRank, $parameters['PBX_RANG']);
        $this->assertSame($sampleId, $parameters['PBX_IDENTIFIANT']);
        $this->assertSame(null, $parameters['PBX_TOTAL']);
        $this->assertSame(Language::FRENCH, $parameters['PBX_LANGUE']);
        $this->assertSame(null, $parameters['PBX_CMD']);
        $this->assertSame('SHA512', $parameters['PBX_HASH']);
        $this->assertSame(null, $parameters['PBX_PORTEUR']);
        $this->assertSame('a:b;c:d;e:fg', $parameters['PBX_RETOUR']);
        $this->assertArrayHasKey('PBX_TIME', $parameters);
        $this->assertSame($acceptedUrl, $parameters['PBX_EFFECTUE']);
        $this->assertSame($refusedUrl, $parameters['PBX_REFUSE']);
        $this->assertSame($abortedUrl, $parameters['PBX_ANNULE']);
        $this->assertSame($waitingUrl, $parameters['PBX_ATTENTE']);
        $this->assertSame($transactionUrl, $parameters['PBX_REPONDRE_A']);
        $this->assertSame($sampleHmac, $parameters['PBX_HMAC']);
    }

    /** @test */
    public function setAmount_it_gets_valid_amount_and_currency_when_both_given()
    {
        $this->ignoreMissingMethods();

        $this->request->setAmount(100.22, Currency::CHF);

        $parameters = $this->request->getParameters();

        $this->assertSame('10022', $parameters['PBX_TOTAL']);
        $this->assertSame(Currency::CHF, $parameters['PBX_DEVISE']);
    }

    /** @test */
    public function setAmount_it_gets_valid_amount_and_currency_when_no_currency()
    {
        $this->ignoreMissingMethods();
        $this->request->setAmount('100,4567');
        $parameters = $this->request->getParameters();
        $this->assertSame('1004567', $parameters['PBX_TOTAL']);
        $this->assertSame(Currency::EUR, $parameters['PBX_DEVISE']);
    }

    /** @test */
    public function setLanguage_it_gets_valid_language_when_language_was_set()
    {
        $this->ignoreMissingMethods();
        $this->request->setLanguage(Language::DUTCH);
        $parameters = $this->request->getParameters();
        $this->assertSame(Language::DUTCH, $parameters['PBX_LANGUE']);
    }

    /** @test */
    public function setLanguage_it_gets_valid_language_when_language_was_not_set()
    {
        $this->ignoreMissingMethods();
        $parameters = $this->request->getParameters();
        $this->assertSame(Language::FRENCH, $parameters['PBX_LANGUE']);
    }

    /** @test */
    public function setCustomerEmail_it_gets_valid_customer_email_when_set()
    {
        $this->ignoreMissingMethods();
        $this->request->setCustomerEmail('foo-bar@example.com');
        $parameters = $this->request->getParameters();
        $this->assertSame('foo-bar@example.com', $parameters['PBX_PORTEUR']);
    }

    /** @test */
    public function setTime_it_gets_valid_date_time_when_set()
    {
        $this->ignoreMissingMethods();
        $date = Carbon::now()->addDays(10);
        $this->request->setTime($date);
        $parameters = $this->request->getParameters();
        $this->assertSame($date->format('c'), $parameters['PBX_TIME']);
    }

    /** @test */
    public function setTime_it_gets_valid_date_time_when_not_set()
    {
        $this->ignoreMissingMethods();
        $parameters = $this->request->getParameters();
        $now = Carbon::now();
        $this->assertTrue(in_array($parameters['PBX_TIME'], [
            $now->format('c'),
            $now->subSecond(1)->format('c'),
        ], true));
    }

    /** @test */
    public function setPaymentNumber_it_gets_valid_payment_number()
    {
        $this->ignoreMissingMethods();
        $this->request->setPaymentNumber(123);
        $parameters = $this->request->getParameters();
        $this->assertSame(123, $parameters['PBX_CMD']);
    }

    /** @test */
    public function setReturnFields_it_gets_valid_return_fields()
    {
        $this->ignoreMissingMethods();
        $fields = ['a' => 'b', 'c' => 'de', 'f' => 'g'];
        $this->request->setReturnFields($fields);
        $parameters = $this->request->getParameters();
        $this->assertSame('a:b;c:de;f:g', $parameters['PBX_RETOUR']);
    }

    /** @test */
    public function setCustomerPaymentAcceptedUrl_it_gets_valid_accepted_url()
    {
        $this->ignoreMissingMethods();
        $sampleUrl = 'https://example.com/accepted-url';
        $this->request->setCustomerPaymentAcceptedUrl($sampleUrl);
        $parameters = $this->request->getParameters();
        $this->assertSame($sampleUrl, $parameters['PBX_EFFECTUE']);
    }

    /** @test */
    public function setCustomerPaymentRefusedUrl_it_gets_valid_refused_url()
    {
        $this->ignoreMissingMethods();
        $sampleUrl = 'https://example.com/refused-url';
        $this->request->setCustomerPaymentRefusedUrl($sampleUrl);
        $parameters = $this->request->getParameters();
        $this->assertSame($sampleUrl, $parameters['PBX_REFUSE']);
    }

    /** @test */
    public function setCustomerPaymentAbortedUrl_it_gets_valid_aborted_url()
    {
        $this->ignoreMissingMethods();
        $sampleUrl = 'https://example.com/aborted-url';
        $this->request->setCustomerPaymentAbortedUrl($sampleUrl);
        $parameters = $this->request->getParameters();
        $this->assertSame($sampleUrl, $parameters['PBX_ANNULE']);
    }

    /** @test */
    public function setCustomerPaymentWaitingUrl_it_gets_valid_waiting_url()
    {
        $this->ignoreMissingMethods();
        $sampleUrl = 'https://example.com/waiting-url';
        $this->request->setCustomerPaymentWaitingUrl($sampleUrl);
        $parameters = $this->request->getParameters();
        $this->assertSame($sampleUrl, $parameters['PBX_ATTENTE']);
    }

    /** @test */
    public function setTransactionVerifyUrl_it_gets_valid_transaction_url_when_set()
    {
        $this->ignoreMissingMethods();
        $sampleUrl = 'https://example.com/transaction-url';
        $this->request->setTransactionVerifyUrl($sampleUrl);
        $parameters = $this->request->getParameters();
        $this->assertSame($sampleUrl, $parameters['PBX_REPONDRE_A']);
    }

    /** @test */
    public function getUrl_it_fires_server_selector_once()
    {
        $validUrl = 'https://sample.com/valid/server/url';

        $this->serverSelector->shouldReceive('find')->once()->with('paybox')->andReturn($validUrl);

        $url = $this->request->getUrl();
        $this->assertSame($validUrl, $url);

        // now launch again - server should not be searched one more time but result should be same 
        $url = $this->request->getUrl();
        $this->assertSame($validUrl, $url);
    }

    /** @test */
    public function send_it_generates_view_with_valid_parameters()
    {
        $parameters = [
            'a' => 'b',
            'c' => 'd',
        ];
        $sampleUrl = 'https://example.com';

        $viewName = 'sample.view';

        $this->request->shouldReceive('getParameters')->withNoArgs()->andReturn($parameters);
        $this->request->shouldReceive('getUrl')->withNoArgs()->andReturn($sampleUrl);

        $this->view->shouldReceive('make')
            ->with($viewName, ['parameters' => $parameters, 'url' => $sampleUrl]);

        $this->request->send($viewName);
    }
}
