<?php
/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */

namespace Adobe\Launch\Model\Provision\Pipeline;

use Adobe\Launch\Model\Provision\PipeInterface;
use Adobe\Launch\Model\ProvisioningConfigProvider;
use Adobe\Launch\Webservice\Client\ProvisionClient;

class GetTargetClientCode implements PipeInterface
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
        $request = $this->getRequest($config['ADOBE_IO_ACCESS_TOKEN']);
        $response = $this->provisionClient->request(
            $request['url'],
            $request['method'],
            $request['header'],
            $request['code'],
            $request['body'],
            $request['enctype']
        );

        $clientCode = 'clientCode';
        $globalMboxName = 'globalMboxName';
        if ($response
            && array_key_exists('clientCode', $response)
            && array_key_exists('globalMboxName', $response)
        ) {
            $clientCode = $response['clientCode'];
            $globalMboxName = $response['globalMboxName'];
        }
        $config['TARGET_CLIENT_CODE'] = $clientCode;
        $config['TARGET_GLOBAL_MBOX'] = $globalMboxName;
    }

    /**
     * @param string $token
     * @return array
     */
    private function getRequest(string $token)
    {
        $body = [
            'accessToken' => $token,
            'imsOrgId' => $this->provisioningConfigProvider->getOrgID()
        ];
        return [
            'code' => 200,
            'method' => 'POST',
            'header' => [
                'Content-Type' => 'application/vnd.api+json',
                'Accept-Encoding' => 'gzip, deflate, br',
            ],
            'body' => json_encode($body), //TODO: replace with serializer
            'url' => 'https://trace.testandtarget.omniture.com/v1/settings',
            'enctype' => 'application/vnd.api+json'
        ];
    }
}
