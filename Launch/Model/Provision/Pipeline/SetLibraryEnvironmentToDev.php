<?php
/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */

namespace Adobe\Launch\Model\Provision\Pipeline;

use Adobe\Launch\Model\Provision\PipeInterface;
use Adobe\Launch\Webservice\Client\ProvisionClient;

class SetLibraryEnvironmentToDev implements PipeInterface
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
            $config['LAUNCH_ENV_ID_DEV'],
            $config['LAUNCH_LIB_ID_CONFIG']
        );
        $this->provisionClient->request(
            $request['url'],
            $request['method'],
            $request['header'],
            $request['code'],
            $request['body'],
            $request['enctype']
        );
    }

    /**
     * @param string $token
     * @param string $devEnvId
     * @param string $libId
     * @return array
     */
    private function getRequest(string $token, string $devEnvId, string $libId): array
    {
        $body = [
            'data' => [
                'id' => $devEnvId,
                'type' => 'environments'
            ]
        ];
        return [
            'code' => 200,
            'method' => 'POST',
            'header' => [
                'Accept' => 'application/vnd.api+json;revision=1',
                'Content-Type' => 'application/vnd.api+json',
                'Authorization' => 'Bearer ' . $token,
                'x-api-key' => 'Activation-DTM',
            ],
            'body' => json_encode($body), //TODO: replace with serializer
            'url' => 'https://reactor.adobe.io/libraries/' . $libId . '/relationships/environment',
            'enctype' => 'application/vnd.api+json'
        ];
    }
}
