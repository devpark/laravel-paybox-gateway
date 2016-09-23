<?php

namespace Tests\Providers;

use Devpark\PayboxGateway\Providers\PayboxServiceProvider;
use Illuminate\Foundation\Application;
use Tests\UnitTestCase;
use Mockery as m;

class PayboxServiceProviderTest extends UnitTestCase
{
    /** @test */
    public function it_does_all_required_actions_when_registering()
    {
        $app = m::mock(Application::class);

        $moduleConfigFile = realpath(__DIR__ . '/../../config/paybox.php');
        $configPath = 'dummy/config/path';
        $basePath = 'dummy/base/path';

        $payboxProvider = m::mock(PayboxServiceProvider::class, [$app])->makePartial()
            ->shouldAllowMockingProtectedMethods();

        // merge config        
        $payboxProvider->shouldReceive('mergeConfigFrom')
            ->with($moduleConfigFile, 'paybox')->once();

        // publishing configuration files
        $app->shouldReceive('offsetGet')->with('path.config')->once()->andReturn($configPath);
        $payboxProvider->shouldReceive('publishes')->once()->with([
            $moduleConfigFile => $configPath . DIRECTORY_SEPARATOR . 'paybox.php',
        ], 'config');

        $app->shouldReceive('offsetGet')->with('path.base')->once()->andReturn($basePath);
        $payboxProvider->shouldReceive('publishes')->once()->with([
            realpath(__DIR__ . '/../../views') => $basePath . DIRECTORY_SEPARATOR . 'resources' .
                DIRECTORY_SEPARATOR . 'views' . DIRECTORY_SEPARATOR . 'paybox',
        ], 'views');

        $payboxProvider->register();
    }
}
