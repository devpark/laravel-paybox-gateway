<?php

namespace Tests\Helpers;

use Bnb\PayboxGateway\Requests\Paybox\AuthorizationWithCapture;
use Bnb\PayboxGateway\Services\Amount;
use Bnb\PayboxGateway\Services\HmacHashGenerator;
use Bnb\PayboxGateway\Services\ServerSelector;
use Illuminate\Contracts\Config\Repository as Config;
use Illuminate\Contracts\Routing\UrlGenerator;
use Illuminate\Contracts\View\Factory as ViewFactory;
use Mockery as m;

trait Authorization
{
    protected $serverSelector;
    protected $config;
    protected $hmacHashGenerator;
    protected $urlGenerator;
    protected $view;
    protected $request;
    protected $amountService;

    protected function setUpMocks($class = AuthorizationWithCapture::class)
    {
        $this->serverSelector = m::mock(ServerSelector::class);
        $this->config = m::mock(Config::class);
        $this->hmacHashGenerator = m::mock(HmacHashGenerator::class);
        $this->urlGenerator = m::mock(UrlGenerator::class);
        $this->view = m::mock(ViewFactory::class);
        $this->amountService = m::mock(Amount::class);
        $this->request = m::mock($class,
            [
                $this->serverSelector,
                $this->config,
                $this->hmacHashGenerator,
                $this->urlGenerator,
                $this->view,
                $this->amountService,
            ])->makePartial()
            ->shouldAllowMockingProtectedMethods();
    }

    protected function ignoreMissingMethods()
    {
        $this->config->shouldIgnoreMissing();
        $this->urlGenerator->shouldIgnoreMissing();
        $this->hmacHashGenerator->shouldIgnoreMissing();
        $this->amountService->shouldIgnoreMissing();
    }
}
