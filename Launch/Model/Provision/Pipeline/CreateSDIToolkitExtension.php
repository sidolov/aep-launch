<?php
/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */

namespace Adobe\Launch\Model\Provision\Pipeline;

use Adobe\Launch\Model\Provision\PipeInterface;
use Adobe\Launch\Webservice\Client\ProvisionClient;

class CreateSDIToolkitExtension implements PipeInterface
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
            $config['LAUNCH_EXT_PACKAGE_ID_SDI_TOOLKIT']
        );
        $response = $this->provisionClient->request(
            $request['url'],
            $request['method'],
            $request['header'],
            $request['code'],
            $request['body'],
            $request['enctype']
        );
        if ($response && array_key_exists('data', $response)) {
            $config['LAUNCH_EXT_ID_SDI_TOOLKIT'] = $response['data']['id'];
        }
    }

    /**
     * @param string $token
     * @param string $propertyId
     * @param string $sdiToolkitPackageId
     * @return array
     */
    private function getRequest(
        string $token,
        string $propertyId,
        string $sdiToolkitPackageId
    ): array {
        $body = [
            'data' => [
                'attributes' => [
                    'delegate_descriptor_id' => 'sdi-toolkit::extensionConfiguration::config',
                    'settings' => '{}',
                    'revision' => false,
                ],
                'relationships' => [
                    'extension_package' => [
                        'data' => [
                            'id' => $sdiToolkitPackageId,
                            'type' => 'extension_packages'
                        ]
                    ]
                ],
                'type' => 'extensions'
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
            'url' => 'https://reactor.adobe.io/properties/' . $propertyId . '/extensions',
            'enctype' => 'application/vnd.api+json'
        ];
    }
}
