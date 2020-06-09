<?php
/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */

namespace Adobe\Launch\Model\Provision\Pipeline;

use Adobe\Launch\Model\Provision\PipeInterface;
use Adobe\Launch\Webservice\Client\ProvisionClient;

class CreateRules implements PipeInterface
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
        return;
        //TODO: finish rule components creation.
        foreach ($this->getRules() as $rule) {
            $request = $this->getRuleRequest(
                $config['ADOBE_IO_ACCESS_TOKEN'],
                $config['LAUNCH_PROPERTY_ID'],
                $rule['name']
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
            } elseif ($response && array_key_exists('data', $response)) {
                $config['Launch_Rule_id'] = $response['data']['id'];
                $componentResponse = $this->createRuleAddRuleComponents($rule['name'], $request, $config);
                if (array_key_exists('error', $componentResponse)) {
                    $response =  $componentResponse;
                    break;
                }
            }
        }
    }

    /**
     * @param string $token
     * @param string $propertyId
     * @param array $ruleName
     * @return array
     */
    private function getRuleRequest(
        string $token,
        string $propertyId,
        string $ruleName
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
            'body' => json_encode(
                [
                    'data' => [
                        'attributes' => [
                            'name' => $ruleName,
                            'reevision' => false
                        ],
                        'type' => 'rules'
                    ]
                ]
            ),
            'url' => 'https://reactor.adobe.io/properties/' . $propertyId . '/rules',
            'enctype' => 'application/vnd.api+json'
        ];
    }

    /**
     * @return array
     */
    private function getRules(): array
    {
        return [
            [
                'name' => 'Cart Viewed [AA]',
                'components' => [

                ]
            ],
            [
                'name' => 'Listing Viewed [AA]',
                'components' => [

                ]
            ],
            [
                'name' => 'Order Placed [AA]',
                'components' => [

                ]
            ],
            [
                'name' => 'Page Loaded [AA]',
                'components' => [

                ]
            ],
            [
                'name' => 'Product Added [AA]',
                'components' => [

                ]
            ],
            [
                'name' => 'Product Removed [AA]',
                'components' => [

                ]
            ],
            [
                'name' => 'Product Viewed [AA]',
                'components' => [

                ]
            ],
            [
                'name' => 'User Registered [AA]',
                'components' => [

                ]
            ],
            [
                'name' => 'User Signed In [AA]',
                'components' => [

                ]
            ],
            [
                'name' => 'Load & Fire Global [AT]',
                'components' => [

                ]
            ]
        ];
    }
}
