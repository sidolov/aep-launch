<?php
/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */

namespace Adobe\Launch\Model\Provision\Pipeline;

use Adobe\Launch\Model\Provision\PipeInterface;
use Adobe\Launch\Webservice\Client\ProvisionClient;

class GetExtensionPackageIds implements PipeInterface
{
    private const EXTENSION_MAP = [
        ['src' => 'adobe-analytics', 'target' => 'LAUNCH_EXT_PACKAGE_ID_ADOBE_ANALYTICS'],
        ['src' => 'adobe-target', 'target' => 'LAUNCH_EXT_PACKAGE_ID_ADOBE_TARGET'],
        ['src' => 'adobe-mcid', 'target' => 'LAUNCH_EXT_PACKAGE_ID_ADOBE_EXPERIENCE_CLOUD'],
        ['src' => 'constant-dataelement', 'target' => 'LAUNCH_EXT_PACKAGE_ID_CONSTANT_DATA_ELEMENT'],
        ['src' => 'aa-product-string-search-discovery', 'target' => 'LAUNCH_EXT_PACKAGE_ID_SDI_PRODUCT_STR'],
        ['src' => 'sdi-toolkit', 'target' => 'LAUNCH_EXT_PACKAGE_ID_SDI_TOOLKIT'],
        ['src' => 'data-layer-manager-search-discovery', 'target' => 'LAUNCH_EXT_PACKAGE_ID_SDI_DATALAYER_MGR'],
    ];

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
        $request = $this->getRequest($config['ADOBE_IO_ACCESS_TOKEN']);
        $response = $this->provisionClient->request(
            $request['url'],
            $request['method'],
            $request['header'],
            $request['code'],
            $request['body'],
            $request['enctype']
        );
        if ($response && array_key_exists('data', $response)) {
            foreach (self::EXTENSION_MAP as $map) {
                foreach ($response['data'] as $resp) {
                    if ($resp['attributes']['name'] === $map['src']) {
                        $config[$map['target']] = $resp['id'];
                    }
                }
            }
        }
    }

    /**
     * @param string $token
     * @return array
     */
    private function getRequest(string $token): array
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
            'url' => 'https://reactor.adobe.io/extension_packages?page[size]=999&sort=display_name&'
                . 'filter[platform]=EQ%20web,EQ%20null&max_availability=public',
            'enctype' => 'application/vnd.api+json'
        ];
    }
}
