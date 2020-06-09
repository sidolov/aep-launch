<?php
/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */

namespace Adobe\Launch\Model\Provision\Pipeline;

use Adobe\Launch\Model\Provision\PipeInterface;
use Adobe\Launch\Model\ProvisioningConfigProvider;
use Adobe\Launch\Webservice\Client\ProvisionClient;

class CreateBearerToken implements PipeInterface
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
        $request = $this->getRequest();
        $response =  $this->provisionClient->request(
            $request['url'],
            $request['method'],
            $request['header'],
            $request['code'],
            $request['body'],
            $request['enctype']
        );
        if ($response && array_key_exists('access_token', $response)) {
            $config['ADOBE_IO_ACCESS_TOKEN'] = $response['access_token'];
        }

        // TODO: throw exception
        if ($response && array_key_exists('error', $response)) {
            if (strpos($response['error'], 'client_secret') !== false) {
                $response = ['error' => 'The Client Secret is invalid.'];
            } elseif (strpos($response['error'], 'client_id') !== false) {
                $response = ['error' => 'The Client ID is invalid.'];
            } elseif (strpos($response['error'], 'JWT') !== false) {
                $response = ['error' => 'The JWT is invalid.'];
            }
        }

    }

    private function getRequest(): array
    {
        return [
            'code' => 200,
            'method' => 'POST',
            'header' => [
                'Cache-Control' => 'no-cache',
                'Content-Type' => 'application/x-www-form-urlencoded'
            ],
            'body' => [
                'client_id' => $this->provisioningConfigProvider->getClientID(),
                'client_secret' => $this->provisioningConfigProvider->getClientSecret(),
                'jwt_token' => $this->provisioningConfigProvider->getJWT()
            ],
            'url' => 'https://ims-na1.adobelogin.com/ims/exchange/jwt/',
            'enctype' => 'application/x-www-form-urlencoded'
        ];
    }
}
