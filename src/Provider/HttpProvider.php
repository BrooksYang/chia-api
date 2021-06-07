<?php

namespace BrooksYang\ChiaApi\Provider;

use GuzzleHttp\Client;
use GuzzleHttp\ClientInterface;
use GuzzleHttp\Exception\GuzzleException;
use GuzzleHttp\Psr7\Request;
use Psr\Http\Message\StreamInterface;
use BrooksYang\ChiaApi\Exception\ChiaException;
use BrooksYang\ChiaApi\Exception\NotFoundException;

class HttpProvider implements HttpProviderInterface
{
    /**
     * HTTP Client Handler
     *
     * @var ClientInterface.
     */
    protected $httpClient;

    /**
     * Server or RPC URL
     *
     * @var string
     */
    protected $host;

    /**
     * Cert
     *
     * @var string
     */
    protected $crt;

    /**
     * Private key
     *
     * @var string
     */
    protected $sslKey;

    /**
     * Waiting time
     *
     * @var int
     */
    protected $timeout = 30000;

    /**
     * Get custom headers
     *
     * @var array
     */
    protected $headers = [];

    /**
     * Get the pages
     *
     * @var string
     */
    protected $statusPage = '/';

    /**
     * HttpProvider constructor.
     *
     * @param string      $host
     * @param string|null $crt
     * @param string|null $sslKey
     * @param int         $timeout
     * @param array       $headers
     * @param string      $statusPage
     * @throws ChiaException
     */
    public function __construct(string $host, string $crt = null, string $sslKey = null, int $timeout = 30000,
                                array $headers = [], string $statusPage = '/')
    {
        if (!parse_url($host)) {
            throw new ChiaException('Invalid URL provided to HttpProvider');
        }

        if (is_nan($timeout) || $timeout < 0) {
            throw new ChiaException('Invalid timeout duration provided');
        }

        if (!is_array($headers)) {
            throw new ChiaException('Invalid headers array provided');
        }

        $this->host = $host;
        $this->crt = $crt;
        $this->sslKey = $sslKey;
        $this->timeout = $timeout;
        $this->statusPage = $statusPage;
        $this->headers = $headers;

        $this->httpClient = new Client([
            'base_uri' => $host,
            'timeout'  => $timeout,
            'verify'   => false,
        ]);
    }

    /**
     * Enter a new page
     *
     * @param string $page
     */
    public function setStatusPage(string $page = '/'): void
    {
        $this->statusPage = $page;
    }

    /**
     * Check connection
     *
     * @return bool
     * @throws ChiaException
     * @throws GuzzleException
     */
    public function isConnected(): bool
    {
        $response = $this->request($this->statusPage);

        if (array_key_exists('blockID', $response)) {
            return true;
        } elseif (array_key_exists('status', $response)) {
            return true;
        }
        return false;
    }

    /**
     * Getting a host
     *
     * @return string
     */
    public function getHost(): string
    {
        return $this->host;
    }

    /**
     * Getting timeout
     *
     * @return int
     */
    public function getTimeout(): int
    {
        return $this->timeout;
    }

    /**
     * Send request
     *
     * @param string $url
     * @param array  $payload
     * @param string $method
     * @return array|int[]
     * @throws ChiaException
     * @throws GuzzleException
     */
    public function request(string $url, array $payload = [], string $method = 'get'): array
    {
        $method = strtoupper($method);

        if (!in_array($method, ['GET', 'POST'])) {
            throw new ChiaException('The method is not defined');
        }

        $options = [
            'headers' => $this->headers,
            'body'    => json_encode($payload),
            'cert'    => $this->crt,
            'ssl_key' => $this->sslKey,
        ];

        $request = new Request($method, $url, $options['headers'], $options['body']);
        $rawResponse = $this->httpClient->send($request, $options);

        return $this->decodeBody(
            $rawResponse->getBody(),
            $rawResponse->getStatusCode()
        );
    }

    /**
     * Convert the original answer to an array
     *
     * @param StreamInterface $stream
     * @param int             $status
     * @return array|mixed
     */
    protected function decodeBody(StreamInterface $stream, int $status): array
    {
        $decodedBody = json_decode($stream->getContents(), true);

        if ((string)$stream == 'OK') {
            $decodedBody = [
                'status' => 1,
            ];
        } elseif ($decodedBody == null or !is_array($decodedBody)) {
            $decodedBody = [];
        }

        if ($status == 404) {
            throw new NotFoundException('Page not found');
        }

        return $decodedBody;
    }
}
