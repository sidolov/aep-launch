<?php
/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */

namespace Adobe\Launch\Model\Provision\Pipeline;

use Adobe\Launch\Model\Provision\PipeInterface;
use Adobe\Launch\Webservice\Client\ProvisionClient;

class CreateProdEnvironment implements PipeInterface
{
    /**
     * @var ProvisionClient
     */
    private $provisionClient;

    /**
     * @param ProvisionClient $provisionClient
     */
    public function __construct(
        ProvisionClient $provisionClient
    ) {
        $this->provisionClient = $provisionClient;
    }

    public function execute(&$config): void
    {
        $request = $this->getRequest(
            $config['ADOBE_IO_ACCESS_TOKEN'],
            $config['LAUNCH_PROPERTY_ID'],
            $config['LAUNCH_ADAPTER_ID']
        );
        $response =  $this->provisionClient->request(
            $request['url'],
            $request['method'],
            $request['header'],
            $request['code'],
            $request['body'],
            $request['enctype']
        );
        if ($response && array_key_exists('data', $response)) {
            $config['LAUNCH_ENV_ID_PROD'] = $response['data']['id'];
        }
    }

    /**
     * @param string $token
     * @param string $property
     * @param string $adapterId
     * @return array
     */
    private function getRequest(string $token, string $property, string $adapterId): array
    {
        $body = [
            'data' => [
                'attributes' => [
                    'name' => 'Production',
                    'stage' => 'production',
                    'archive' => false,
                    'path' => ''
                ],
                'relationships' => [
                    'host' => [
                        'data' => [
                            'id' => $adapterId,
                            'type' => 'hosts'
                        ]
                    ]
                ],
                'type' => 'environments'
            ]
        ];
        return [
            'code' => 201,
            'method' => 'POST',
            'header' => [
                'Accept' => 'application/vnd.api+json;revision=1',
                'Content-Type' => 'application/vnd.api+json',
                'Authorization' => 'Bearer ' . $token,
                'x-api-key' => 'Activation-DTM',
            ],
            'body' => json_encode($body), //TODO: replace with serializer
            'url' => 'https://reactor.adobe.io/properties/' . $property . '/environments',
            'enctype' => 'application/vnd.api+json'
        ];
    }
}
