<?php
/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */

namespace Adobe\Launch\Model\Provision\Pipeline;

use Adobe\Launch\Model\Provision\PipeInterface;
use Adobe\Launch\Model\ProvisioningConfigProvider;
use Adobe\Launch\Webservice\Client\ProvisionClient;

class GetCompanyId implements PipeInterface
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
        $request = $this->getRequest($config['ADOBE_IO_ACCESS_TOKEN']);
        $response =  $this->provisionClient->request(
            $request['url'],
            $request['method'],
            $request['header'],
            $request['code'],
            $request['body'],
            $request['enctype']
        );
        if ($response && array_key_exists('data', $response)) {
            foreach ($response['data'] as $item) {
                if ($item['attributes']['org_id'] === $this->provisioningConfigProvider->getOrgID()) {
                    $config['LAUNCH_COMPANY_ID'] = $item['id'];
                    break;
                }
            }
        }
        //TODO: throw exception in case of company id is absent
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
            'url' => 'https://reactor.adobe.io/companies',
            'enctype' => 'application/vnd.api+json'
        ];
    }
}
