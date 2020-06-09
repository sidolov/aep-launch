<?php
/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */

namespace Adobe\Launch\Model\Provision\Pipeline;

use Adobe\Launch\Model\Provision\PipeInterface;
use Adobe\Launch\Webservice\Client\ProvisionClient;

class GetCoreExtensionId implements PipeInterface
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
        $request = $this->getRequest($config['ADOBE_IO_ACCESS_TOKEN'], $config['LAUNCH_PROPERTY_ID']);
        $response = $this->provisionClient->request(
            $request['url'],
            $request['method'],
            $request['header'],
            $request['code'],
            $request['body'],
            $request['enctype']
        );
        if ($response && array_key_exists('data', $response)) {
            foreach ($response['data'] as $resp) {
                if ($resp['attributes']['name'] === 'core') {
                    $config['LAUNCH_EXT_ID_CORE'] = $resp['id'];
                }
            }
        }
    }

    /**
     * @param string $token
     * @param string $propertyId
     * @return array
     */
    private function getRequest(string $token, string $propertyId): array
    {
        return [
            'code' => 200,
            'method' => 'GET',
            'header' => [
                'Accept' => 'application/vnd.api+json;revision=1',
                'Content-Type' => 'application/vnd.api+json',
                'Authorization' => 'Bearer ' . $token,
                'x-api-key' => 'Activation-DTM',
            ],
            'body' => null,
            'url' => 'https://reactor.adobe.io/properties/' . $propertyId . '/extensions',
            'enctype' => 'application/vnd.api+json'
        ];
    }
}
