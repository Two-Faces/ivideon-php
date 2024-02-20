<?php

namespace IVideon\Api;

use GuzzleHttp\Exception\GuzzleException;
use IVideon\Responses\Server;

class Servers
{
    /**
     * Servers constructor.
     *
     * @param Api $api
     */
    public function __construct(public Api $api) {}

    /**
     * @param   int  $limit
     * @param   int  $skip
     *
     * @return Server[]
     * @throws GuzzleException
     */
    public function getServers(int $limit = 100, int $skip = 0): array
    {
        $response = $this->api->request('POST', 'servers', [
            'query' => [
                'op' => 'FIND',
            ],
            'json' => [
                'user'       => $this->api->getAccount()->getUserId(),
                'limit'      => $limit,
                'skip'       => $skip,
                'projection' => [
                    'cameras' => [
                        'id' => 1,
                    ],
                ],
            ],
        ]);

        return (new \IVideon\Responses\Servers($response))->getResult()->getItems();
    }
}
