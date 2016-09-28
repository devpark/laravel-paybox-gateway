<?php

namespace Devpark\PayboxGateway\Services;

use DOMDocument;
use Exception;
use Illuminate\Contracts\Config\Repository as Config;

class ServerSelector
{
    /**
     * @var Config
     */
    protected $config;

    /**
     * ServerSelector constructor.
     *
     * @param Config $config
     */
    public function __construct(Config $config)
    {
        $this->config = $config;
    }

    /**
     * Find working server of given type.
     *
     * @param string $type
     *
     * @return string
     * @throws Exception
     */
    public function find($type)
    {
        $urls = $this->getUrls($type);
        $servers = $this->getServers($urls);

        foreach ($servers as $key => $server) {
            $doc = $this->getDocumentLoader();
            $doc->loadHTMLFile('https://' . $server . '/load.html');
            $element = $doc->getElementById('server_status');
            if ($element && $element->textContent == 'OK') {
                return $urls[$key];
            }
        }

        throw new Exception('No servers set or all servers are down');
    }

    public function findFrom($sourceType, $targetType, $sourceUrl, $other)
    {
        $sourceUrls = $this->getUrls($sourceType);
        $targetUrls = $this->getUrls($targetType);

        $key = array_search($sourceUrl, $sourceUrls);
        if (! $other) {
            // if same and same key exists let's return url
            if (isset($targetUrls[$key])) {
                return $targetUrls[$key];
            }
        } else {
            // we look for other key
            $keys = array_values(array_diff(array_keys($targetUrls), [$key]));
            if ($keys && isset($targetUrls[$keys[0]])) {
                return $targetUrls[$keys[0]];
            }
        }

        // it was impossible to find valid target url, so let's use first target url

        return current($targetUrls);
    }

    /**
     * Get servers (hosts only) from urls.
     *
     * @param array $urls
     *
     * @return array
     * @throws Exception
     */
    protected function getServers(array $urls)
    {
        $servers = [];

        foreach ($urls as $url) {
            $result = parse_url($url);
            if ($result === false) {
                throw new Exception("Url {$url} is invalid");
            }

            $servers[] = $result['host'];
        }

        return $servers;
    }

    /**
     * Get urls of given type.
     *
     * @param string $type
     *
     * @return array
     */
    protected function getUrls($type)
    {
        $prefix = $this->config->get('paybox.test') ? 'test' : 'production';

        return (array) $this->config->get('paybox.' . $prefix . '_urls.' . $type);
    }

    /**
     * Get document loader.
     *
     * @return DOMDocument
     */
    protected function getDocumentLoader()
    {
        return new DOMDocument();
    }
}
