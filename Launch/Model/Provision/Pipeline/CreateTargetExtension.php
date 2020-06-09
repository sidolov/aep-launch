<?php
/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */

namespace Adobe\Launch\Model\Provision\Pipeline;

use Adobe\Launch\Model\Provision\PipeInterface;
use Adobe\Launch\Model\ProvisioningConfigProvider;
use Adobe\Launch\Webservice\Client\ProvisionClient;

class CreateTargetExtension implements PipeInterface
{
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
        $request = $this->getRequest(
            $config['ADOBE_IO_ACCESS_TOKEN'],
            $config['LAUNCH_PROPERTY_ID'],
            $config['TARGET_CLIENT_CODE'],
            $config['TARGET_GLOBAL_MBOX'],
            $config['LAUNCH_EXT_PACKAGE_ID_ADOBE_TARGET']
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
            $config['LAUNCH_EXT_ID_ADOBE_TARGET'] = $response['data']['id'];
        }
    }

    /**
     * @param string $token
     * @param string $propertyId
     * @param string $targetClientCode
     * @param string $targetBlobalMbox
     * @param string $targetPackageId
     * @return array
     */
    private function getRequest(
        string $token,
        string $propertyId,
        string $targetClientCode,
        string $targetBlobalMbox,
        string $targetPackageId
    ): array {
        $body = [
            'data' => [
                'attributes' => [
                    'delegate_descriptor_id' => 'adobe-target::extensionConfiguration::config',
                    'revision' => false,
                    'settings' => json_encode(
                        [
                            'targetSettings' => [
                                'clientCode' => $targetClientCode,
                                'imsOrgId' => $this->provisioningConfigProvider->getOrgID(),
                                'serverDomain' => $targetClientCode . '.tt.omtrdc.net',
                                'crossDomain' => 'disabled',
                                'timeout' => 3000,
                                'globalMboxName' => $targetBlobalMbox,
                                'version' => '1.6.0',
                                'enabled' => true,
                                'defaultContentHiddenStyle' => 'visibility: hidden;',
                                'defaultContentVisibleStyle' => 'visibility: visible;',
                                'bodyHiddenStyle' => 'body {opacity: 0}',
                                'bodyHidingEnabled' => true,
                                'deviceIdLifetime' => 63244800000,
                                'sessionIdLifetime' => 1860000,
                                'selectorsPollingTimeout' => 5000,
                                'visitorApiTimeout' => 2000,
                                'overrideMboxEdgeServer' => false,
                                'overrideMboxEdgeServerTimeout' => 1860000,
                                'optoutEnabled' => true,
                                'secureOnly' => false,
                                'supplementalDataIdParamTimeout' => 30,
                                'authoringScriptUrl' => 'cdn.tt.omtrdc.net/cdn/target-vec.js',
                                'urlSizeLimit' => 2048
                            ]
                        ]
                    ),
                ],
                'relationships' => [
                    'extension_package' => [
                        'data' => [
                            'id' => $targetPackageId,
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
