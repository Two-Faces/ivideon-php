<?php

namespace IVideon\Flows;

use GuzzleHttp\Client;

abstract class AbstractFlow implements LoginFlowInterface
{
    /**
     * @var Client|null
     */
    protected ?Client $httpClient = null;

    public function __construct(array $httpClientConfig = [])
    {
        $this->configureHttpClient($httpClientConfig);
    }

    abstract protected function configureHttpClient(array $httpClientConfig = []);
}
