<?php

namespace Devpark\PayboxGateway\Services;

use Illuminate\Contracts\Config\Repository as Config;
use Illuminate\Contracts\Foundation\Application;

class HmacHashGenerator
{
    /**
     * @var Config
     */
    protected $config;

    /**
     * HmacGenerator constructor.
     *
     * @param Application $app
     */
    public function __construct(Application $app)
    {
        $this->config = $app->make('config');
    }

    /**
     * Get HMAC hash for given params.
     *
     * @param array $params
     *
     * @return string
     */
    public function get(array $params)
    {
        return mb_strtoupper(hash_hmac('sha512', $this->getParamsString($params),
            pack('H*', $this->getKey())));
    }

    /**
     * Get key from file.
     *
     * @return string
     */
    protected function getKey()
    {
        return $this->config->get('paybox.hmac_key');
    }

    /**
     * Get params as string.
     *
     * @param array $params
     *
     * @return string
     */
    protected function getParamsString(array $params)
    {
        return collect($params)->map(function ($value, $key) {
            return $key . '=' . $value;
        })->implode('&');
    }
}
