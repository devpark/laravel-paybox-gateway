<?php

namespace Devpark\PayboxGateway\Services;

use Illuminate\Contracts\Config\Repository as Config;
use Illuminate\Filesystem\Filesystem;

class SignatureVerifier
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
     * SignatureVerifier constructor.
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
     * Verify whether given signature is correct for given parameters.
     *
     * @param string $signature
     * @param array $parameters
     *
     * @return int
     */
    public function isCorrect($signature, array $parameters)
    {
        $signature = base64_decode($signature);

        $data = $this->getSignedData($parameters);

        return openssl_verify($data, $signature, $this->getKey());
    }

    /**
     * Load public key.
     *
     * @return resource
     */
    protected function getKey()
    {
        return openssl_pkey_get_public($this->files->get($this->config->get('paybox.public_key')));
    }

    /**
     * Get parameters as string.
     *
     * @param array $parameters
     *
     * @return string
     */
    private function getSignedData(array $parameters)
    {
        return collect($parameters)->map(function ($value, $key) {
            return $key . '=' . $value;
        })->implode('&');
    }
}
