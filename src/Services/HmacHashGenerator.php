<?php

namespace Devpark\PayboxGateway\Services;

use Illuminate\Contracts\Config\Repository as Config;
use Illuminate\Contracts\Filesystem\Filesystem;

class HmacHashGenerator
{
    /**
     * @var Config
     */
    protected $config;

    /**
     * @var Filesystem
     */
    protected $files;

    /**
     * HmacGenerator constructor.
     *
     * @param Config $config
     * @param Filesystem $files
     */
    public function __construct(Config $config, Filesystem $files)
    {
        $this->config = $config;
        $this->files = $files;
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
        return $this->files->get($this->config->get('paybox.hmac_key'));
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
