<?php

namespace BrooksYang\ChiaApi;

use BrooksYang\ChiaApi\Exception\ChiaException;
use BrooksYang\ChiaApi\Provider\{HttpProvider, HttpProviderInterface};

class ChiaManager
{
    const FULL_NODE = 'fullNode';
    const WALLET_SERVER = 'walletServer';

    /**
     * Default Nodes
     *
     * @var array
     */
    protected $defaultNodes = [
        self::FULL_NODE     => 'https://localhost:8555',
        self::WALLET_SERVER => 'https://localhost:9256',
    ];

    /**
     * Providers
     *
     * @var array
     */
    protected $providers = [
        self::FULL_NODE     => [],
        self::WALLET_SERVER => [],
    ];

    /**
     * Status Page
     *
     * @var array
     */
    protected $statusPage = [
        'fullNode'     => 'get_blockchain_state',
        'walletServer' => 'get_sync_status',
    ];

    /**
     * @param $chia
     * @param $providers
     * @throws ChiaException
     */
    public function __construct($chia, $providers)
    {
        $this->providers = $providers;

        foreach ($providers as $key => $value) {
            // Do not skip the supplier is empty
            if ($value == null) {
                $this->providers[$key] = new HttpProvider(
                    $this->defaultNodes[$key]
                );
            }

            if (is_string($providers[$key])) {
                $this->providers[$key] = new HttpProvider($value);
            }

            $this->providers[$key]->setStatusPage($this->statusPage[$key]);
        }
    }

    /**
     * List of providers
     *
     * @return array
     */
    public function getProviders()
    {
        return $this->providers;
    }

    /**
     * Full Node
     *
     * @return HttpProviderInterface
     * @throws ChiaException
     */
    public function fullNode(): HttpProviderInterface
    {
        if (!array_key_exists('fullNode', $this->providers)) {
            throw new ChiaException('Full node is not activated.');
        }

        return $this->providers['fullNode'];
    }

    /**
     * Wallet Server
     *
     * @return HttpProviderInterface
     * @throws ChiaException
     */
    public function walletServer(): HttpProviderInterface
    {
        if (!array_key_exists(self::WALLET_SERVER, $this->providers)) {
            throw new ChiaException('Wallet server is not activated.');
        }

        return $this->providers[self::WALLET_SERVER];
    }

    /**
     * Basic query to nodes
     *
     * @param        $url
     * @param array  $params
     * @param string $server
     * @param string $method
     * @return array
     * @throws ChiaException
     */
    public function request($url, $params = [], $server = self::FULL_NODE, $method = 'post')
    {
        if ($server == self::WALLET_SERVER) {
            $response = $this->walletServer()->request($url, $params, $method);
        } else {
            $response = $this->fullNode()->request($url, $params, $method);
        }

        return $response;
    }

    /**
     * Check connections
     *
     * @return array
     */
    public function isConnected()
    {
        $array = [];
        foreach ($this->providers as $key => $value) {
            array_push($array, [
                $key => boolval($value->isConnected()),
            ]);
        }

        return $array;
    }
}
