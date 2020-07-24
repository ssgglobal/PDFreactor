<?php

namespace StepStone\PDFreactor;

use GuzzleHttp\HandlerStack;
use GuzzleHttp\Handler\MockHandler;
use stdClass;

/**
 * Get information from the PDFreactor service monitor.
 * 
 * @link https://www.pdfreactor.com/product/doc_html/index.html#Monitoring
 */
class Monitor
{
    /** @var Api */
    protected $api;

    /** @var array */
    protected $headers  = [];

    /**
     * Creates a new instance of Api to use.
     *
     * @param string $url
     * @param string $adminKey
     * @param integer $port
     * @param MockHandler|null $mock
     */
    public function __construct(string $url, string $adminKey, int $port = 9423, ?MockHandler $mock = null)
    {
        $options    = [
            'allow_redirects'   => false,
            'base_uri'          => "{$url}:{$port}/service/monitor/",
            'http_errors'       => true,
            'query'             => [
                'adminKey'  => $adminKey,
            ],
        ];

        if ($mock) {
            $options['handler'] = HandlerStack::create($mock);
        }

        $this->api  = new Api($options);
    }

    /**
     * Provides an overview of conversions.
     *
     * @return stdClass
     */
    public function getConversions(): stdClass
    {
        $result = $this->api->send('GET', 'conversions.json');

        return json_decode($result->body);
    }

    /**
     * Get information about the server environment. CPU Cores, available memory
     * environment variables, etc.
     *
     * @return stdClass
     */
    public function getServer(): stdClass
    {
        $result = $this->api->send('GET', 'server.json');

        return json_decode($result->body);
    }
}