<?php

namespace Tests\Services;

use Devpark\PayboxGateway\Services\SignatureVerifier;
use Tests\UnitTestCase;
use Mockery as m;
use Illuminate\Config\Repository as Config;
use Illuminate\Filesystem\Filesystem;

class SignatureVerifierTest extends UnitTestCase
{
    /** @test */
    public function it_returns_1_when_signature_is_correct()
    {
        $config = m::mock(Config::class);
        $files = new Filesystem();

        $parameters = ['a' => 'b', 'c' => 'd', 'e' => 'fg'];
        $data = 'a=b&c=d&e=fg';

        $key = openssl_pkey_get_private(
            file_get_contents(realpath(__DIR__ . '/../keys/prvkey.pem')));
        openssl_sign($data, $signature, $key);
        openssl_free_key($key);

        $signatureVerifier = m::mock(SignatureVerifier::class, [$config, $files])->makePartial();

        $config->shouldReceive('get')->with('paybox.public_key')->once()
            ->andReturn(realpath(__DIR__ . '/../keys/pubkey.pem'));

        $result = $signatureVerifier->isCorrect(base64_encode($signature), $parameters);

        $this->assertSame(1, $result);
    }

    /** @test */
    public function it_returns_0_when_signature_is_incorrect()
    {
        $config = m::mock(Config::class);
        $files = new Filesystem();

        $parameters = ['a' => 'b', 'c' => 'd', 'e' => 'fg'];
        $data = 'a=b&c=d&e=fg';

        $signature = 'sample invalid signature';

        $signatureVerifier = m::mock(SignatureVerifier::class, [$config, $files])->makePartial();

        $config->shouldReceive('get')->with('paybox.public_key')->once()
            ->andReturn(realpath(__DIR__ . '/../keys/pubkey.pem'));

        $result = $signatureVerifier->isCorrect(base64_encode($signature), $parameters);

        $this->assertSame(0, $result);
    }
}
