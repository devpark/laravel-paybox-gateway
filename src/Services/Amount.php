<?php

namespace Devpark\PayboxGateway\Services;

class Amount
{
    /**
     * Get amount converted into Paybox format.
     *
     * @param float $amount
     * @param bool $fill
     *
     * @return string
     */
    public function get($amount, $fill)
    {
        $amount = str_replace(['.', ','], ['', ''], $amount);
        if ($fill) {
            $amount = str_pad($amount, 10, '0', STR_PAD_LEFT);
        }

        return $amount;
    }
}
