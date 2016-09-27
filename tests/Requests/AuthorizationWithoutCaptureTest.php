<?php

namespace Tests\Requests;

use Devpark\PayboxGateway\Requests\AuthorizationWithoutCapture;
use Tests\UnitTestCase;
use Mockery as m;
use Tests\Helpers\Authorization as AuthorizationHelper;

class AuthorizationWithoutCaptureTest extends UnitTestCase 
{
    use AuthorizationHelper;

    public function setUp()
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