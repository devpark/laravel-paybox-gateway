<?php

namespace Tests\Services;

use Devpark\PayboxGateway\Services\Amount;
use Tests\UnitTestCase;

class AmountTest extends UnitTestCase
{
    /** @test */
    public function it_gets_valid_amount_with_dots()
    {
        $service = new Amount();
        $this->assertSame('10022', $service->get(100.22));
    }

    /** @test */
    public function it_gets_valid_amount_with_commas()
    {
        $service = new Amount();
        $this->assertSame('10045', $service->get('100,45'));
    }
}
