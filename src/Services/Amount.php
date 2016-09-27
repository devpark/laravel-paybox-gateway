<?php

namespace Devpark\PayboxGateway\Services;

class Amount
{
    /**
     * Get amount converted into Paybox format.
     *
     * @param float $amount
     *
     * @return string
     */
    public function get($amount)
    {
        return str_replace(['.', ','], ['', ''], $amount);
    }
}
