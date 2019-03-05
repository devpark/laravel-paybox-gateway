<?php

namespace Tests\Requests;

use Bnb\PayboxGateway\Requests\Paybox\AuthorizationWithoutCapture;
use Tests\Helpers\Authorization as AuthorizationHelper;
use Tests\UnitTestCase;

class AuthorizationWithoutCaptureTest extends UnitTestCase
{
    use AuthorizationHelper;

    public function setUp(): void
    {
        parent::setUp();
        $this->setUpMocks(AuthorizationWithoutCapture::class);
    }

    /** @test */
    public function getParameters_it_returns_valid_capture_parameters()
    {
        $this->ignoreMissingMethods();
        $parameters = $this->request->getParameters();
        $this->assertSame('O', $parameters['PBX_AUTOSEULE']);
    }
}
