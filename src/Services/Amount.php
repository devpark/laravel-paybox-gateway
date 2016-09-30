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
        // if given amount is string without commas or float or int we format it as string with 
        // 2 decimal points, otherwise we won't get desired result for integers
        if (mb_strpos($amount, ',') === false) {
            $amount = number_format(round($amount, 2), 2);
        }
        $amount = str_replace(['.', ','], ['', ''], $amount);
        if ($fill) {
            $amount = str_pad($amount, 10, '0', STR_PAD_LEFT);
        } else {
            // here we remove any leading zeros (it might happen for numbers < 1)
            $amount = (string) ((int) $amount);
        }

        return $amount;
    }
}
