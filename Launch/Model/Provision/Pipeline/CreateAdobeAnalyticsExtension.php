<?php
/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */

namespace Adobe\Launch\Model\Provision\Pipeline;

use Adobe\Launch\Model\Provision\PipeInterface;
use Adobe\Launch\Model\ProvisioningConfigProvider;
use Adobe\Launch\Webservice\Client\ProvisionClient;

class CreateAdobeAnalyticsExtension implements PipeInterface
{
    private const AA_RS_PROD = 'rs_prod';

    private const AA_RS_STAGE = 'rs_stage';

    private const AA_RS_DEV = 'rs_dev';

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
            $config['LAUNCH_EXT_PACKAGE_ID_ADOBE_ANALYTICS']
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
            $config['LAUNCH_EXT_ID_ADOBE_ANALYTICS'] = $response['data']['id'];
        }
    }

    /**
     * @param string $token
     * @param string $propertyId
     * @param string $analyticsId
     * @return array
     */
    private function getRequest(string $token, string $propertyId, string $analyticsId): array
    {
        $analyticsProdSuite = $this->provisioningConfigProvider->getProdSuite();
        if ($analyticsProdSuite === null) {
            $analyticsProdSuite = self::AA_RS_PROD; //TODO: move to config by default
        }
        $analyticsStageSuite = $this->provisioningConfigProvider->getStageSuite();
        if ($analyticsStageSuite === null) {
            $analyticsStageSuite = self::AA_RS_STAGE; //TODO: move to config by default
        }
        $analyticsDevSuite = $this->provisioningConfigProvider->getDevSuite();
        if ($analyticsDevSuite === null) {
            $analyticsDevSuite = self::AA_RS_DEV; //TODO: move to config by default
        }
        $body = [
            'data' => [
                'attributes' => [
                    'delegate_descriptor_id' => 'adobe-analytics::extensionConfiguration::config',
                    'revision' => false,
                    'settings' => json_encode(
                        [
                            'orgId' => $this->provisioningConfigProvider->getOrgID(),
                            'libraryCode' => [
                                'type' => 'managed',
                                'accounts' => [
                                    'production' => [$analyticsProdSuite],
                                    'staging' => [$analyticsStageSuite],
                                    'development' => [$analyticsDevSuite]
                                ]
                            ],
                            'trackerProperties' => [
                                'charSet' => 'UTF-8',
                                'trackingServer' => 'change_me.sc.omtrdc.net',
                                'trackInlineStats' => true,
                                'trackDownloadLinks' => false,
                                'trackExternalLinks' => true,
                                'currencyCode' => 'USD',
                                'linkInternalFilters' => ['%location.hostname%']
                            ]
                        ]
                    ),
                ],
                'relationships' => [
                    'extension_package' => [
                        'data' => [
                            'id' => $analyticsId,
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
