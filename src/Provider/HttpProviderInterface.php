<?php

namespace BrooksYang\ChiaApi\Provider;

interface HttpProviderInterface
{
    /**
     * Enter a new page
     *
     * @param string $page
     */
    public function setStatusPage(string $page = '/'): void;

    /**
     * Check connection
     *
     * @return bool
     */
    public function isConnected(): bool;

    /**
     * We send requests to the server
     *
     * @param string $url
     * @param array  $payload
     * @param string $method
     * @return array
     */
    public function request(string $url, array $payload = [], string $method = 'get'): array;
}
