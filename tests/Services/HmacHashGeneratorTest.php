<?php

namespace Tests\Services;

use Devpark\PayboxGateway\Services\HmacHashGenerator;
use Illuminate\Config\Repository as Config;
use Illuminate\Contracts\Foundation\Application;
use Tests\UnitTestCase;
use Mockery as m;

class HmacHashGeneratorTest extends UnitTestCase
{
    /** @test */
    public function it_gets_valid_hmac_hash_for_multiple_params()
    {
        $app = m::mock(Application::class)->makePartial();
        $config = m::mock(Config::class);

        $app->shouldReceive('make')->with('config')->once()->andReturn($config);

        $generator = new HmacHashGenerator($app);

        $params = [
            'param1' => 'value',
            'param2' => 'value % 2',
        ];

        $secret = 'secret';

        $key = unpack('H*', $secret);
        $key = $key[1];

        $config->shouldReceive('get')->with('paybox.hmac_key')->once()
            ->andReturn($key);

        $result = $generator->get($params);

        $hmac = mb_strtoupper(hash_hmac('sha512',
            'param1=' . $params['param1'] . '&param2=' . $params['param2'], $secret));
        $this->assertSame($hmac, $result);
    }
}
