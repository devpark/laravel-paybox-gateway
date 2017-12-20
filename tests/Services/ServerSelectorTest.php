<?php

namespace Tests\Services;

use Bnb\PayboxGateway\Services\ServerSelector;
use DOMDocument;
use Exception;
use Illuminate\Config\Repository as Config;
use stdClass;
use Tests\UnitTestCase;
use Mockery as m;

class ServerSelectorTest extends UnitTestCase
{
    /** @test */
    public function find_it_returns_valid_server_for_paybox_when_test_is_on_and_1st_server_is_fine()
    {
        $config = m::mock(Config::class);
        $serverSelector = m::mock(ServerSelector::class, [$config])->makePartial()
            ->shouldAllowMockingProtectedMethods();

        $dom = m::mock(DOMDocument::class)->makePartial();
        $domElement = m::mock(stdClass::class);

        $urls = [
            'https://example.com/paybox-payment-url',
            'https://example.net/paybox-payment-url-2',
        ];

        $config->shouldReceive('get')->with('paybox.test')->once()->andReturn(true);
        $config->shouldReceive('get')->with('paybox.test_urls.paybox')->once()->andReturn($urls);

        $serverSelector->shouldReceive('getDocumentLoader')->once()->andReturn($dom);
        $dom->shouldReceive('loadHTMLFile')->with('https://example.com/load.html')->once();
        $domElement->textContent = 'OK';
        $dom->shouldReceive('getElementById')->andReturn($domElement);

        $result = $serverSelector->find('paybox');

        $this->assertSame($urls[0], $result);
    }

    /** @test */
    public function find_it_returns_valid_server_for_paybox_when_test_is_off_and_1st_server_is_fine()
    {
        $config = m::mock(Config::class);
        $serverSelector = m::mock(ServerSelector::class, [$config])->makePartial()
            ->shouldAllowMockingProtectedMethods();

        $dom = m::mock(DOMDocument::class)->makePartial();
        $domElement = m::mock(stdClass::class);

        $urls = [
            'https://example.com/paybox-payment-url',
            'https://example.net/paybox-payment-url-2',
        ];

        $config->shouldReceive('get')->with('paybox.test')->once()->andReturn(false);
        $config->shouldReceive('get')->with('paybox.production_urls.paybox')->once()
            ->andReturn($urls);

        $serverSelector->shouldReceive('getDocumentLoader')->once()->andReturn($dom);
        $dom->shouldReceive('loadHTMLFile')->with('https://example.com/load.html')->once();
        $domElement->textContent = 'OK';
        $dom->shouldReceive('getElementById')->andReturn($domElement);

        $result = $serverSelector->find('paybox');

        $this->assertSame($urls[0], $result);
    }

    /** @test */
    public function find_it_returns_valid_server_for_paybox_when_test_is_off_and_1st_server_is_down()
    {
        $config = m::mock(Config::class);
        $serverSelector = m::mock(ServerSelector::class, [$config])->makePartial()
            ->shouldAllowMockingProtectedMethods();

        $dom = m::mock(DOMDocument::class)->makePartial();
        $domElement = m::mock(stdClass::class);

        $dom2 = m::mock(DOMDocument::class)->makePartial();
        $domElement2 = m::mock(stdClass::class);

        $urls = [
            'https://example.com/paybox-payment-url',
            'https://example.net/paybox-payment-url-2',
        ];

        $config->shouldReceive('get')->with('paybox.test')->once()->andReturn(false);
        $config->shouldReceive('get')->with('paybox.production_urls.paybox')->once()
            ->andReturn($urls);

        $serverSelector->shouldReceive('getDocumentLoader')->once()->andReturn($dom);
        $dom->shouldReceive('loadHTMLFile')->with('https://example.com/load.html')->once();
        $domElement->textContent = 'ERROR';
        $dom->shouldReceive('getElementById')->andReturn($domElement);

        $serverSelector->shouldReceive('getDocumentLoader')->once()->andReturn($dom2);
        $dom2->shouldReceive('loadHTMLFile')->with('https://example.net/load.html')->once();
        $domElement2->textContent = 'OK';
        $dom2->shouldReceive('getElementById')->andReturn($domElement2);

        $result = $serverSelector->find('paybox');

        $this->assertSame($urls[1], $result);
    }

    /** @test */
    public function find_it_throws_exception_when_test_is_off_and_all_servers_are_down()
    {
        $config = m::mock(Config::class);
        $serverSelector = m::mock(ServerSelector::class, [$config])->makePartial()
            ->shouldAllowMockingProtectedMethods();

        $dom = m::mock(DOMDocument::class)->makePartial();
        $domElement = m::mock(stdClass::class);

        $dom2 = m::mock(DOMDocument::class)->makePartial();
        $domElement2 = m::mock(stdClass::class);

        $urls = [
            'https://example.com/paybox-payment-url',
            'https://example.net/paybox-payment-url-2',
        ];

        $config->shouldReceive('get')->with('paybox.test')->once()->andReturn(false);
        $config->shouldReceive('get')->with('paybox.production_urls.paybox')->once()
            ->andReturn($urls);

        $serverSelector->shouldReceive('getDocumentLoader')->once()->andReturn($dom);
        $dom->shouldReceive('loadHTMLFile')->with('https://example.com/load.html')->once();
        $domElement->textContent = 'ERROR';
        $dom->shouldReceive('getElementById')->andReturn($domElement);

        $serverSelector->shouldReceive('getDocumentLoader')->once()->andReturn($dom2);
        $dom2->shouldReceive('loadHTMLFile')->with('https://example.net/load.html')->once();
        $domElement2->textContent = 'ERROR';
        $dom2->shouldReceive('getElementById')->andReturn($domElement2);

        $this->expectException(Exception::class);
        $this->expectExceptionMessage('No servers set or all servers are down');

        $serverSelector->find('paybox');
    }

    /** @test */
    public function find_it_throws_exception_when_server_contains_only_protocol()
    {
        $config = m::mock(Config::class);
        $serverSelector = m::mock(ServerSelector::class, [$config])->makePartial()
            ->shouldAllowMockingProtectedMethods();

        $urls = [
            'http://',
        ];

        $config->shouldReceive('get')->with('paybox.test')->once()->andReturn(true);
        $config->shouldReceive('get')->with('paybox.test_urls.paybox')->once()->andReturn($urls);

        $this->expectException(Exception::class);
        $this->expectExceptionMessage('Url http:// is invalid');

        $serverSelector->find('paybox');
    }

    /** @test */
    public function find_it_throws_exception_when_server_contains_only_path_without_host()
    {
        $config = m::mock(Config::class);
        $serverSelector = m::mock(ServerSelector::class, [$config])->makePartial()
            ->shouldAllowMockingProtectedMethods();

        $urls = [
            'sample/path',
        ];

        $config->shouldReceive('get')->with('paybox.test')->once()->andReturn(true);
        $config->shouldReceive('get')->with('paybox.test_urls.paybox')->once()->andReturn($urls);

        $this->expectException(Exception::class);
        $this->expectExceptionMessage('Url sample/path is invalid');

        $serverSelector->find('paybox');
    }

    /** @test */
    public function findFrom_it_finds_valid_server_from_given_when_same()
    {
        $config = m::mock(Config::class);
        $serverSelector = m::mock(ServerSelector::class, [$config])->makePartial()
            ->shouldAllowMockingProtectedMethods();

        $payboxUrls = [
            'https://example.com/paybox-payment-url',
            'https://example.net/paybox-payment-url-2',
        ];

        $payboxDirectUrls = [
            'https://example-direct.com/paybox-payment-url',
            'https://example-direct.net/paybox-payment-url-2',
        ];

        $config->shouldReceive('get')->with('paybox.test')->times(2)->andReturn(false);
        $config->shouldReceive('get')->with('paybox.production_urls.paybox')->once()
            ->andReturn($payboxUrls);
        $config->shouldReceive('get')->with('paybox.production_urls.paybox_direct')->once()
            ->andReturn($payboxDirectUrls);

        $url = $serverSelector->findFrom('paybox', 'paybox_direct', $payboxUrls[0], false);
        $this->assertSame($payboxDirectUrls[0], $url);
    }

    /** @test */
    public function findFrom_it_finds_valid_server_from_given_when_other()
    {
        $config = m::mock(Config::class);
        $serverSelector = m::mock(ServerSelector::class, [$config])->makePartial()
            ->shouldAllowMockingProtectedMethods();

        $payboxUrls = [
            'https://example.com/paybox-payment-url',
            'https://example.net/paybox-payment-url-2',
        ];

        $payboxDirectUrls = [
            'https://example-direct.com/paybox-payment-url',
            'https://example-direct.net/paybox-payment-url-2',
        ];

        $config->shouldReceive('get')->with('paybox.test')->times(2)->andReturn(false);
        $config->shouldReceive('get')->with('paybox.production_urls.paybox')->once()
            ->andReturn($payboxUrls);
        $config->shouldReceive('get')->with('paybox.production_urls.paybox_direct')->once()
            ->andReturn($payboxDirectUrls);

        $url = $serverSelector->findFrom('paybox', 'paybox_direct', $payboxUrls[0], true);
        $this->assertSame($payboxDirectUrls[1], $url);
    }

    /** @test */
    public function findFrom_it_return_current_url_when_other_url_cannot_be_found()
    {
        $config = m::mock(Config::class);
        $serverSelector = m::mock(ServerSelector::class, [$config])->makePartial()
            ->shouldAllowMockingProtectedMethods();

        $payboxUrls = [
            'https://example.com/paybox-payment-url',
            'https://example.net/paybox-payment-url-2',
        ];

        $payboxDirectUrls = [
            'https://example-direct.com/paybox-payment-url',
        ];

        $config->shouldReceive('get')->with('paybox.test')->times(2)->andReturn(false);
        $config->shouldReceive('get')->with('paybox.production_urls.paybox')->once()
            ->andReturn($payboxUrls);
        $config->shouldReceive('get')->with('paybox.production_urls.paybox_direct')->once()
            ->andReturn($payboxDirectUrls);

        $url = $serverSelector->findFrom('paybox', 'paybox_direct', $payboxUrls[0], true);
        $this->assertSame($payboxDirectUrls[0], $url);
    }
}
