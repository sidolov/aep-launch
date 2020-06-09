<?php
/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */

namespace Adobe\Launch\Model\Provision\Pipeline;

use Adobe\Launch\Model\LaunchConfigProvider;
use Adobe\Launch\Model\Provision\PipeInterface;
use Adobe\Launch\Webservice\Client\ProvisionClient;

class CreateSDIDataLayerManagerExtension implements PipeInterface
{
    /**
     * @var ProvisionClient
     */
    private $provisionClient;

    /**
     * @var LaunchConfigProvider
     */
    private $launchConfigProvider;

    /**
     * @param ProvisionClient $provisionClient
     * @param LaunchConfigProvider $launchConfigProvider
     */
    public function __construct(
        ProvisionClient $provisionClient,
        LaunchConfigProvider $launchConfigProvider
    ) {
        $this->provisionClient = $provisionClient;
        $this->launchConfigProvider = $launchConfigProvider;
    }

    public function execute(&$config): void
    {
        $request = $this->getRequest(
            $config['ADOBE_IO_ACCESS_TOKEN'],
            $config['LAUNCH_PROPERTY_ID'],
            $config['LAUNCH_EXT_PACKAGE_ID_SDI_DATALAYER_MGR']
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
            $config['LAUNCH_EXT_ID_SDI_DATALAYER_MGR'] = $response['data']['id'];
        }
    }

    /**
     * @param string $token
     * @param string $propertyId
     * @param string $sdiDataLayerPackageId
     * @return array
     */
    private function getRequest(
        string $token,
        string $propertyId,
        string $sdiDataLayerPackageId
    ): array {
        $body = [
            'data' => [
                'attributes' => [
                    'delegate_descriptor_id' => 'data-layer-manager-search-discovery::extensionConfiguration::config',
                    'settings' => json_encode(
                        [
                            'dataLayerObjectName' => $this->launchConfigProvider->getDatalayerName(),
                            'eventNames' => [
                                [
                                    'eventName' => 'Cart Viewed',
                                    'resetBefore' => false,
                                    'validationSchema' => []
                                ],
                                [
                                    'eventName' => 'Listing Viewed',
                                    'resetBefore' => false,
                                    'validationSchema' => []
                                ],
                                [
                                    'eventName' => 'Order Placed',
                                    'resetBefore' => false,
                                    'validationSchema' => []
                                ],
                                [
                                    'eventName' => 'Page Loaded',
                                    'resetBefore' => false,
                                    'validationSchema' => []
                                ],
                                [
                                    'eventName' => 'Product Added',
                                    'resetBefore' => false,
                                    'validationSchema' => []
                                ],
                                [
                                    'eventName' => 'Product Removed',
                                    'resetBefore' => false,
                                    'validationSchema' => []
                                ],
                                [
                                    'eventName' => 'Product Viewed',
                                    'resetBefore' => false,
                                    'validationSchema' => []
                                ],
                                [
                                    'eventName' => 'User Registered',
                                    'resetBefore' => false,
                                    'validationSchema' => []
                                ],
                                [
                                    'eventName' => 'User Signed In',
                                    'resetBefore' => false,
                                    'validationSchema' => []
                                ]
                            ]
                        ]
                    ),
                ],
                'relationships' => [
                    'extension_package' => [
                        'data' => [
                            'id' => $sdiDataLayerPackageId,
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
