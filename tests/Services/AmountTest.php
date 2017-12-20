<?php

namespace Tests\Services;

use Bnb\PayboxGateway\Services\Amount;
use Tests\UnitTestCase;

class AmountTest extends UnitTestCase
{
    /** @test */
    public function it_gets_valid_amount_with_dots()
    {
        $service = new Amount();
        $this->assertSame('10022', $service->get(100.22, false));
    }

    /** @test */
    public function it_gets_valid_amount_with_commas()
    {
        $service = new Amount();
        $this->assertSame('210045', $service->get('2100,45', false));
    }

    /** @test */
    public function it_gets_valid_amount_with_dots_and_fill()
    {
        $service = new Amount();
        $this->assertSame('0000010022', $service->get(100.22, true));
    }

    /** @test */
    public function it_gets_valid_amount_with_commas_and_fill()
    {
        $service = new Amount();
        $this->assertSame('0000210045', $service->get('2100,45', true));
    }

    /** @test */
    public function it_gets_valid_amount_with_integer_number()
    {
        $service = new Amount();
        $this->assertSame('210000', $service->get(2100, false));
    }

    /** @test */
    public function it_gets_valid_amount_with_float_integer_number()
    {
        $service = new Amount();
        $this->assertSame('210000', $service->get(2100.0, false));
    }

    /** @test */
    public function it_gets_valid_amount_with_very_small_number()
    {
        $service = new Amount();
        $this->assertSame('1', $service->get(0.01, false));
    }

    /** @test */
    public function it_gets_valid_amount_with_very_small_number_when_fill()
    {
        $service = new Amount();
        $this->assertSame('0000000001', $service->get(0.01, true));
    }
}
