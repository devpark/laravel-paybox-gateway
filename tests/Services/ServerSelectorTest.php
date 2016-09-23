<?php

namespace Tests\Services;

use Devpark\PayboxGateway\Services\ServerSelector;
use DOMDocument;
use Exception;
use Illuminate\Config\Repository as Config;
use stdClass;
use Tests\UnitTestCase;
use Mockery as m;

class ServerSelectorTest extends UnitTestCase
{
    /** @test */
    public function it_returns_valid_server_for_paybox_when_test_is_on_and_1st_server_is_fine()
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
        $config->shouldReceive('get')->with('test_urls.paybox')->once()->andReturn($urls);

        $serverSelector->shouldReceive('getDocumentLoader')->once()->andReturn($dom);
        $dom->shouldReceive('loadHTMLFile')->with('https://example.com/load.html')->once();
        $domElement->textContent = 'OK';
        $dom->shouldReceive('getElementById')->andReturn($domElement);

        $result = $serverSelector->find('paybox');

        $this->assertSame($urls[0], $result);
    }

    /** @test */
    public function it_returns_valid_server_for_paybox_when_test_is_off_and_1st_server_is_fine()
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
        $config->shouldReceive('get')->with('production_urls.paybox')->once()->andReturn($urls);

        $serverSelector->shouldReceive('getDocumentLoader')->once()->andReturn($dom);
        $dom->shouldReceive('loadHTMLFile')->with('https://example.com/load.html')->once();
        $domElement->textContent = 'OK';
        $dom->shouldReceive('getElementById')->andReturn($domElement);

        $result = $serverSelector->find('paybox');

        $this->assertSame($urls[0], $result);
    }

    /** @test */
    public function it_returns_valid_server_for_paybox_when_test_is_off_and_1st_server_is_down()
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
        $config->shouldReceive('get')->with('production_urls.paybox')->once()->andReturn($urls);

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
    public function it_throws_exception_when_test_is_off_and_all_servers_are_down()
    {
        $urls = [
            'https://example.com/paybox-payment-url',
            'https://example.net/paybox-payment-url-2',
        ];

        $config = m::spy(Config::class);
        $serverSelector = m::mock(ServerSelector::class, [$config])->makePartial()
            ->shouldAllowMockingProtectedMethods();

        $dom = m::spy(DOMDocument::class)->makePartial();
        $domElement = m::spy(stdClass::class);

        $dom2 = m::spy(DOMDocument::class)->makePartial();
        $domElement2 = m::spy(stdClass::class);

        $this->expectException(Exception::class);
        $this->expectExceptionMessage('No servers set or all servers are down');

        $serverSelector->find('paybox');

        $config->shouldHaveReceived('get')->with('paybox.test')->once()->andReturn(false);
        $config->shouldHaveReceived('get')->with('production_urls.paybox')->once()->andReturn($urls);

        $serverSelector->shouldHaveReceived('getDocumentLoader')->once()->andReturn($dom);
        $dom->shouldHaveReceived('loadHTMLFile')->with('https://example.com/load.html')->once();
        $domElement->textContent = 'ERROR';
        $dom->shouldHaveReceived('getElementById')->andReturn($domElement);

        $serverSelector->shouldHaveReceived('getDocumentLoader')->once()->andReturn($dom2);
        $dom2->shouldHaveReceived('loadHTMLFile')->with('https://example.net/load.html')->once();
        $domElement2->textContent = 'ERROR';
        $dom2->shouldHaveReceived('getElementById')->andReturn($domElement2);

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
        $config->shouldReceive('get')->with('production_urls.paybox')->once()->andReturn($urls);

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
}
