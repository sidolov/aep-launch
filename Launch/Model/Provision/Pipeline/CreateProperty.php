<?php
/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */

namespace Adobe\Launch\Model\Provision\Pipeline;

use Adobe\Launch\Model\Provision\PipeInterface;
use Adobe\Launch\Model\ProvisioningConfigProvider;
use Adobe\Launch\Webservice\Client\ProvisionClient;

class CreateProperty implements PipeInterface
{
    private const LAUNCH_PROPERTY_NAME_PREFIX = 'Magento Auto-Provisioned';

    /**
     * @var ProvisionClient
     */
    private $provisionClient;

    /**
     * @var ProvisioningConfigProvider
     */
    private $provisioningConfigProvider;

    /**
     * @param ProvisionClient $provisionClient
     * @param ProvisioningConfigProvider $provisioningConfigProvider
     */
    public function __construct(
        ProvisionClient $provisionClient,
        ProvisioningConfigProvider $provisioningConfigProvider
    ) {
        $this->provisionClient = $provisionClient;
        $this->provisioningConfigProvider = $provisioningConfigProvider;
    }

    public function execute(&$config): void
    {
        $request = $this->getRequest($config['ADOBE_IO_ACCESS_TOKEN'], $config['LAUNCH_COMPANY_ID']);
        $response =  $this->provisionClient->request(
            $request['url'],
            $request['method'],
            $request['header'],
            $request['code'],
            $request['body'],
            $request['enctype']
        );
        if ($response && array_key_exists('data', $response)) {
            $config['LAUNCH_PROPERTY_ID'] = $response['data']['id'];
        }
        //TODO: throw exception in case of LAUNCH_PROPERTY_ID is absent
    }

    /**
     * @param string $token
     * @return array
     */
    private function getRequest(string $token, string $companyId): array
    {
        $propertyName = $this->provisioningConfigProvider->getPropertyName();
        if ($propertyName === null) {
            $propertyName = self::LAUNCH_PROPERTY_NAME_PREFIX;
        }

        $body = [
            'data' => [
                'attributes' => [
                    'name' => $propertyName . ' ' . date("Y-m-d H:i:s"),
                    'development' => false,
                    'domains' => ['example.com'],
                    'platform' => 'web'
                ],
                'type' => 'properties'
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
            'url' => 'https://reactor.adobe.io/companies/' . $companyId . '/properties',
            'enctype' => 'application/vnd.api+json'
        ];
    }
}
