<?php
/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */

namespace Adobe\Launch\Model\Provision\Pipeline;

use Adobe\Launch\Model\Provision\PipeInterface;
use Adobe\Launch\Webservice\Client\ProvisionClient;

class CreateDataElement implements PipeInterface
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
        $elements = $this->getDataElements(
            $config['LAUNCH_EXT_ID_SDI_DATALAYER_MGR'],
            $config['LAUNCH_EXT_ID_CONSTANT_DATA_ELEMENT'],
            $config['LAUNCH_EXT_ID_CORE']
        );
        foreach ($elements as $dataElement) {
            $request = $this->getRequest(
                $config['ADOBE_IO_ACCESS_TOKEN'],
                $config['LAUNCH_PROPERTY_ID'],
                $dataElement
            );

            $response = $this->provisionClient->request(
                $request['url'],
                $request['method'],
                $request['header'],
                $request['code'],
                $request['body'],
                $request['enctype']
            );
            if ($response && array_key_exists('error', $response)) {
                break;
            }
        }
    }

    /**
     * @param string $token
     * @param string $propertyId
     * @param array $body
     * @return array
     */
    private function getRequest(
        string $token,
        string $propertyId,
        array $body
    ): array {
        return [
            'code' => 201,
            'method' => 'POST',
            'header' => [
                'Accept' => 'application/vnd.api+json;revision=1',
                'Content-Type' => 'application/vnd.api+json',
                'Authorization' => 'Bearer ' . $token,
                'x-api-key' => 'Activation-DTM',
            ],
            'body' => json_encode($body),
            'url' => 'https://reactor.adobe.io/properties/' . $propertyId . '/data_elements',
            'enctype' => 'application/vnd.api+json'
        ];
    }

    /**
     * @param string $sdiDataLayerMrg
     * @param string $constDataElementId
     * @param string $launchCoreId
     * @return array
     */
    private function getDataElements(
        string $sdiDataLayerMrg,
        string $constDataElementId,
        string $launchCoreId
    ): array {
        return [
            [
                'data' => [
                    'attributes' => [
                        'clean_text' => true,
                        'default_value' => '',
                        'delegate_descriptor_id' => 'data-layer-manager-search-discovery::dataElements::context-aware-data-layer-root',
                        'extension_id' => $sdiDataLayerMrg,
                        'force_lower_case' => false,
                        'name' => 'Data Layer Object',
                        'order' => 0,
                        'revision' => false,
                        'settings' => '{"dataLayerPath":""}',
                        'storage_duration' => null
                    ],
                    'relationships' => [
                        'extension' => [
                            'data' => [
                                'id' => $sdiDataLayerMrg,
                                'type' => 'extensions'
                            ]
                        ]
                    ],
                    'type' => 'data_elements'
                ]
            ],
            [
                'data' => [
                    'attributes' => [
                        'clean_text' => true,
                        'default_value' => '',
                        'delegate_descriptor_id' => 'constant-dataelement::dataElements::constant',
                        'extension_id' => $constDataElementId,
                        'force_lower_case' => false,
                        'name' => 'ItemPath::productInfo.sku',
                        'order' => 0,
                        'revision' => false,
                        'settings' => '{"value":"productInfo.sku"}',
                        'storage_duration' => null
                    ],
                    'relationships' => [
                        'extension' => [
                            'data' => [
                                'id' => $constDataElementId,
                                'type' => 'extensions'
                            ]
                        ]
                    ],
                    'type' => 'data_elements'
                ]
            ],
            [
                'data' => [
                    'attributes' => [
                        'clean_text' => true,
                        'default_value' => '',
                        'delegate_descriptor_id' => 'constant-dataelement::dataElements::constant',
                        'extension_id' => $constDataElementId,
                        'force_lower_case' => false,
                        'name' => 'ItemPath::productInfo.productID',
                        'order' => 0,
                        'revision' => false,
                        'settings' => '{"value":"productInfo.productID"}',
                        'storage_duration' => null
                    ],
                    'relationships' => [
                        'extension' => [
                            'data' => [
                                'id' => $constDataElementId,
                                'type' => 'extensions'
                            ]
                        ]
                    ],
                    'type' => 'data_elements'
                ]
            ],
            [
                'data' => [
                    'attributes' => [
                        'clean_text' => true,
                        'default_value' => '',
                        'delegate_descriptor_id' => 'constant-dataelement::dataElements::constant',
                        'extension_id' => $constDataElementId,
                        'force_lower_case' => false,
                        'name' => 'CollectionPath::cart.item',
                        'order' => 0,
                        'revision' => false,
                        'settings' => '{"value":"cart.item"}',
                        'storage_duration' => null
                    ],
                    'relationships' => [
                        'extension' => [
                            'data' => [
                                'id' => $constDataElementId,
                                'type' => 'extensions'
                            ]
                        ]
                    ],
                    'type' => 'data_elements'
                ]
            ],
            [
                'data' => [
                    'attributes' => [
                        'clean_text' => true,
                        'default_value' => '',
                        'delegate_descriptor_id' => 'data-layer-manager-search-discovery::dataElements::context-aware-data-layer-root',
                        'extension_id' => $sdiDataLayerMrg,
                        'force_lower_case' => false,
                        'name' => 'listing.listingParams.searchInfo.searchTermEntered',
                        'order' => 0,
                        'revision' => false,
                        'settings' => '{"dataLayerPath":"listing.listingParams.searchInfo.searchTermEntered"}',
                        'storage_duration' => null
                    ],
                    'relationships' => [
                        'extension' => [
                            'data' => [
                                'id' => $sdiDataLayerMrg,
                                'type' => 'extensions'
                            ]
                        ]
                    ],
                    'type' => 'data_elements'
                ]
            ],
            [
                'data' => [
                    'attributes' => [
                        'clean_text' => true,
                        'default_value' => '',
                        'delegate_descriptor_id' => 'constant-dataelement::dataElements::constant',
                        'extension_id' => $constDataElementId,
                        'force_lower_case' => false,
                        'name' => 'CollectionPath::listing.listingResults.item',
                        'order' => 0,
                        'revision' => false,
                        'settings' => '{"value":"listing.listingResults.item"}',
                        'storage_duration' => null
                    ],
                    'relationships' => [
                        'extension' => [
                            'data' => [
                                'id' => $constDataElementId,
                                'type' => 'extensions'
                            ]
                        ]
                    ],
                    'type' => 'data_elements'
                ]
            ],
            [
                'data' => [
                    'attributes' => [
                        'clean_text' => true,
                        'default_value' => '',
                        'delegate_descriptor_id' => 'data-layer-manager-search-discovery::dataElements::context-aware-data-layer-root',
                        'extension_id' => $sdiDataLayerMrg,
                        'force_lower_case' => false,
                        'name' => 'transaction.total.currency',
                        'order' => 0,
                        'revision' => false,
                        'settings' => '{"dataLayerPath":"transaction.total.currency"}',
                        'storage_duration' => null
                    ],
                    'relationships' => [
                        'extension' => [
                            'data' => [
                                'id' => $sdiDataLayerMrg,
                                'type' => 'extensions'
                            ]
                        ]
                    ],
                    'type' => 'data_elements'
                ]
            ],
            [
                'data' => [
                    'attributes' => [
                        'clean_text' => true,
                        'default_value' => '',
                        'delegate_descriptor_id' => 'data-layer-manager-search-discovery::dataElements::context-aware-data-layer-root',
                        'extension_id' => $sdiDataLayerMrg,
                        'force_lower_case' => false,
                        'name' => 'transaction.transactionID',
                        'order' => 0,
                        'revision' => false,
                        'settings' => '{"dataLayerPath":"transaction.transactionID"}',
                        'storage_duration' => null
                    ],
                    'relationships' => [
                        'extension' => [
                            'data' => [
                                'id' => $sdiDataLayerMrg,
                                'type' => 'extensions'
                            ]
                        ]
                    ],
                    'type' => 'data_elements'
                ]
            ],
            [
                'data' => [
                    'attributes' => [
                        'clean_text' => true,
                        'default_value' => '',
                        'delegate_descriptor_id' => 'data-layer-manager-search-discovery::dataElements::context-aware-data-layer-root',
                        'extension_id' => $sdiDataLayerMrg,
                        'force_lower_case' => false,
                        'name' => 'transaction.profile.address.stateProvince',
                        'order' => 0,
                        'revision' => false,
                        'settings' => '{"dataLayerPath":"transaction.profile.address.stateProvince"}',
                        'storage_duration' => null
                    ],
                    'relationships' => [
                        'extension' => [
                            'data' => [
                                'id' => $sdiDataLayerMrg,
                                'type' => 'extensions'
                            ]
                        ]
                    ],
                    'type' => 'data_elements'
                ]
            ],
            [
                'data' => [
                    'attributes' => [
                        'clean_text' => true,
                        'default_value' => '',
                        'delegate_descriptor_id' => 'data-layer-manager-search-discovery::dataElements::context-aware-data-layer-root',
                        'extension_id' => $sdiDataLayerMrg,
                        'force_lower_case' => false,
                        'name' => 'transaction.profile.address.postalCode',
                        'order' => 0,
                        'revision' => false,
                        'settings' => '{"dataLayerPath":"transaction.profile.address.postalCode"}',
                        'storage_duration' => null
                    ],
                    'relationships' => [
                        'extension' => [
                            'data' => [
                                'id' => $sdiDataLayerMrg,
                                'type' => 'extensions'
                            ]
                        ]
                    ],
                    'type' => 'data_elements'
                ]
            ],
            [
                'data' => [
                    'attributes' => [
                        'clean_text' => true,
                        'default_value' => '',
                        'delegate_descriptor_id' => 'constant-dataelement::dataElements::constant',
                        'extension_id' => $constDataElementId,
                        'force_lower_case' => false,
                        'name' => 'ItemPath::voucherDiscount.productLevelDiscountCode',
                        'order' => 0,
                        'revision' => false,
                        'settings' => '{"value":"voucherDiscount.productLevelDiscountCode"}',
                        'storage_duration' => null
                    ],
                    'relationships' => [
                        'extension' => [
                            'data' => [
                                'id' => $constDataElementId,
                                'type' => 'extensions'
                            ]
                        ]
                    ],
                    'type' => 'data_elements'
                ]
            ],
            [
                'data' => [
                    'attributes' => [
                        'clean_text' => true,
                        'default_value' => '',
                        'delegate_descriptor_id' => 'constant-dataelement::dataElements::constant',
                        'extension_id' => $constDataElementId,
                        'force_lower_case' => false,
                        'name' => 'ItemPath::voucherDiscount.productLevelDiscountAmount',
                        'order' => 0,
                        'revision' => false,
                        'settings' => '{"value":"voucherDiscount.productLevelDiscountAmount"}',
                        'storage_duration' => null
                    ],
                    'relationships' => [
                        'extension' => [
                            'data' => [
                                'id' => $constDataElementId,
                                'type' => 'extensions'
                            ]
                        ]
                    ],
                    'type' => 'data_elements'
                ]
            ],
            [
                'data' => [
                    'attributes' => [
                        'clean_text' => true,
                        'default_value' => '',
                        'delegate_descriptor_id' => 'constant-dataelement::dataElements::constant',
                        'extension_id' => $constDataElementId,
                        'force_lower_case' => false,
                        'name' => 'ItemPath::voucherDiscount.hasProductLevelDiscounts',
                        'order' => 0,
                        'revision' => false,
                        'settings' => '{"value":"voucherDiscount.hasProductLevelDiscounts"}',
                        'storage_duration' => null
                    ],
                    'relationships' => [
                        'extension' => [
                            'data' => [
                                'id' => $constDataElementId,
                                'type' => 'extensions'
                            ]
                        ]
                    ],
                    'type' => 'data_elements'
                ]
            ],
            [
                'data' => [
                    'attributes' => [
                        'clean_text' => true,
                        'default_value' => '',
                        'delegate_descriptor_id' => 'constant-dataelement::dataElements::constant',
                        'extension_id' => $constDataElementId,
                        'force_lower_case' => false,
                        'name' => 'ItemPath::price.sellingPrice',
                        'order' => 0,
                        'revision' => false,
                        'settings' => '{"value":"price.sellingPrice"}',
                        'storage_duration' => null
                    ],
                    'relationships' => [
                        'extension' => [
                            'data' => [
                                'id' => $constDataElementId,
                                'type' => 'extensions'
                            ]
                        ]
                    ],
                    'type' => 'data_elements'
                ]
            ],
            [
                'data' => [
                    'attributes' => [
                        'clean_text' => true,
                        'default_value' => '',
                        'delegate_descriptor_id' => 'constant-dataelement::dataElements::constant',
                        'extension_id' => $constDataElementId,
                        'force_lower_case' => false,
                        'name' => 'ItemPath::quantity',
                        'order' => 0,
                        'revision' => false,
                        'settings' => '{"value":"quantity"}',
                        'storage_duration' => null
                    ],
                    'relationships' => [
                        'extension' => [
                            'data' => [
                                'id' => $constDataElementId,
                                'type' => 'extensions'
                            ]
                        ]
                    ],
                    'type' => 'data_elements'
                ]
            ],
            [
                'data' => [
                    'attributes' => [
                        'clean_text' => true,
                        'default_value' => '',
                        'delegate_descriptor_id' => 'constant-dataelement::dataElements::constant',
                        'extension_id' => $constDataElementId,
                        'force_lower_case' => false,
                        'name' => 'CollectionPath::transaction.item',
                        'order' => 0,
                        'revision' => false,
                        'settings' => '{"value":"transaction.item"}',
                        'storage_duration' => null
                    ],
                    'relationships' => [
                        'extension' => [
                            'data' => [
                                'id' => $constDataElementId,
                                'type' => 'extensions'
                            ]
                        ]
                    ],
                    'type' => 'data_elements'
                ]
            ],
            [
                'data' => [
                    'attributes' => [
                        'clean_text' => true,
                        'default_value' => '',
                        'delegate_descriptor_id' => 'core::dataElements::query-string-parameter',
                        'extension_id' => $launchCoreId,
                        'force_lower_case' => false,
                        'name' => 'QS::cid',
                        'order' => 0,
                        'revision' => false,
                        'settings' => '{"caseInsensitive":true,"name":"cid"}',
                        'storage_duration' => null
                    ],
                    'relationships' => [
                        'extension' => [
                            'data' => [
                                'id' => $launchCoreId,
                                'type' => 'extensions'
                            ]
                        ]
                    ],
                    'type' => 'data_elements'
                ]
            ],
            [
                'data' => [
                    'attributes' => [
                        'clean_text' => true,
                        'default_value' => '',
                        'delegate_descriptor_id' => 'data-layer-manager-search-discovery::dataElements::context-aware-data-layer-root',
                        'extension_id' => $sdiDataLayerMrg,
                        'force_lower_case' => false,
                        'name' => 'page.pageCategory',
                        'order' => 0,
                        'revision' => false,
                        'settings' => '{"dataLayerPath":"page.pageCategory"}',
                        'storage_duration' => null
                    ],
                    'relationships' => [
                        'extension' => [
                            'data' => [
                                'id' => $sdiDataLayerMrg,
                                'type' => 'extensions'
                            ]
                        ]
                    ],
                    'type' => 'data_elements'
                ]
            ],
            [
                'data' => [
                    'attributes' => [
                        'clean_text' => true,
                        'default_value' => '',
                        'delegate_descriptor_id' => 'data-layer-manager-search-discovery::dataElements::context-aware-data-layer-root',
                        'extension_id' => $sdiDataLayerMrg,
                        'force_lower_case' => false,
                        'name' => 'page.pageName',
                        'order' => 0,
                        'revision' => false,
                        'settings' => '{"dataLayerPath":"page.pageName"}',
                        'storage_duration' => null
                    ],
                    'relationships' => [
                        'extension' => [
                            'data' => [
                                'id' => $sdiDataLayerMrg,
                                'type' => 'extensions'
                            ]
                        ]
                    ],
                    'type' => 'data_elements'
                ]
            ],
            [
                'data' => [
                    'attributes' => [
                        'clean_text' => true,
                        'default_value' => '',
                        'delegate_descriptor_id' => 'core::dataElements::page-info',
                        'extension_id' => $launchCoreId,
                        'force_lower_case' => true,
                        'name' => 'location.pathname',
                        'order' => 0,
                        'revision' => false,
                        'settings' => '{"attribute":"hostname"}',
                        'storage_duration' => null
                    ],
                    'relationships' => [
                        'extension' => [
                            'data' => [
                                'id' => $launchCoreId,
                                'type' => 'extensions'
                            ]
                        ]
                    ],
                    'type' => 'data_elements'
                ]
            ],
            [
                'data' => [
                    'attributes' => [
                        'clean_text' => true,
                        'default_value' => '',
                        'delegate_descriptor_id' => 'data-layer-manager-search-discovery::dataElements::context-aware-data-layer-root',
                        'extension_id' => $sdiDataLayerMrg,
                        'force_lower_case' => false,
                        'name' => 'page.pageType',
                        'order' => 0,
                        'revision' => false,
                        'settings' => '{"dataLayerPath":"page.pageType"}',
                        'storage_duration' => null
                    ],
                    'relationships' => [
                        'extension' => [
                            'data' => [
                                'id' => $sdiDataLayerMrg,
                                'type' => 'extensions'
                            ]
                        ]
                    ],
                    'type' => 'data_elements'
                ]
            ],
            [
                'data' => [
                    'attributes' => [
                        'clean_text' => true,
                        'default_value' => '',
                        'delegate_descriptor_id' => 'core::dataElements::page-info',
                        'extension_id' => $launchCoreId,
                        'force_lower_case' => true,
                        'name' => 'location.hostname',
                        'order' => 0,
                        'revision' => false,
                        'settings' => '{"attribute":"hostname"}',
                        'storage_duration' => null
                    ],
                    'relationships' => [
                        'extension' => [
                            'data' => [
                                'id' => $launchCoreId,
                                'type' => 'extensions'
                            ]
                        ]
                    ],
                    'type' => 'data_elements'
                ]
            ],
            [
                'data' => [
                    'attributes' => [
                        'clean_text' => true,
                        'default_value' => '',
                        'delegate_descriptor_id' => 'constant-dataelement::dataElements::constant',
                        'extension_id' => $constDataElementId,
                        'force_lower_case' => false,
                        'name' => 'CollectionPath::product',
                        'order' => 0,
                        'revision' => false,
                        'settings' => '{"value":"product"}',
                        'storage_duration' => null
                    ],
                    'relationships' => [
                        'extension' => [
                            'data' => [
                                'id' => $constDataElementId,
                                'type' => 'extensions'
                            ]
                        ]
                    ],
                    'type' => 'data_elements'
                ]
            ],
            [
                'data' => [
                    'attributes' => [
                        'clean_text' => true,
                        'default_value' => '',
                        'delegate_descriptor_id' => 'data-layer-manager-search-discovery::dataElements::context-aware-data-layer-root',
                        'extension_id' => $sdiDataLayerMrg,
                        'force_lower_case' => false,
                        'name' => 'user.userType',
                        'order' => 0,
                        'revision' => false,
                        'settings' => '{"dataLayerPath":"user.userType"}',
                        'storage_duration' => null
                    ],
                    'relationships' => [
                        'extension' => [
                            'data' => [
                                'id' => $sdiDataLayerMrg,
                                'type' => 'extensions'
                            ]
                        ]
                    ],
                    'type' => 'data_elements'
                ]
            ],
            [
                'data' => [
                    'attributes' => [
                        'clean_text' => true,
                        'default_value' => '',
                        'delegate_descriptor_id' => 'data-layer-manager-search-discovery::dataElements::context-aware-data-layer-root',
                        'extension_id' => $sdiDataLayerMrg,
                        'force_lower_case' => false,
                        'name' => 'user.custKey',
                        'order' => 0,
                        'revision' => false,
                        'settings' => '{"dataLayerPath":"user.custKey"}',
                        'storage_duration' => null
                    ],
                    'relationships' => [
                        'extension' => [
                            'data' => [
                                'id' => $sdiDataLayerMrg,
                                'type' => 'extensions'
                            ]
                        ]
                    ],
                    'type' => 'data_elements'
                ]
            ],
        ];
    }
}
