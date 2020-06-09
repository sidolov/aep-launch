<?php
/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */
declare(strict_types=1);

namespace Adobe\LaunchAdminUi\Controller\Adminhtml\Config;

use Magento\Backend\App\Action;
use Magento\Backend\App\Action\Context;
use Magento\Framework\App\Action\HttpPostActionInterface;
use Magento\Framework\Controller\Result\JsonFactory;
use Magento\Framework\Module\Dir\Reader;
use Magento\Framework\Filesystem\Driver\File;
use Magento\Framework\Serialize\Serializer\Json;
use Adobe\Launch\Model\ProvisionAgent;

/**
 * Launch Property provisioning.
 */
class Provision extends Action implements HttpPostActionInterface
{
    /**
     * @var JsonFactory
     */
    private $resultJsonFactory;

    /**
     * @var Reader
     */
    private $moduleReader;

    /**
     * @var File
     */
    private $file;

    /**
     * @var ProvisionAgent
     */
    private $provisionAgent;

    /**
     * @var Json
     */
    private $jsonSerializer;

    /**
     * @var \Adobe\Launch\Model\Provision\Pipeline
     */
    private $pipeline;

    /**
     * @param Context $context
     * @param JsonFactory $resultJsonFactory
     * @param Reader $moduleReader
     * @param File $file
     * @param ProvisionAgent $provisionAgent
     * @param Json $jsonSerializer
     */
    public function __construct(
        Context $context,
        JsonFactory $resultJsonFactory,
        Reader $moduleReader,
        File $file,
        ProvisionAgent $provisionAgent,
        Json $jsonSerializer,
        \Adobe\Launch\Model\Provision\Pipeline $pipeline
    ) {
        $this->resultJsonFactory = $resultJsonFactory;
        $this->moduleReader = $moduleReader;
        $this->file = $file;
        $this->provisionAgent = $provisionAgent;
        $this->jsonSerializer = $jsonSerializer;
        $this->pipeline = $pipeline;
        parent::__construct($context);
    }

    /**
     * Execute Provisioning
     *
     * @return \Magento\Framework\Controller\Result\Json
     */
    public function execute()
    {

//        $conf = $this->getJsonConfig();
//        $test = [
//            'DATA_ELEMENT_CALLS' => $this->jsonSerializer->unserialize($conf['variables']['dataElementAPIcalls']),
//            'RULE_CALLS' => $this->jsonSerializer->unserialize($conf['variables']['ruleAPIcalls']),
//            'RULE_COMPONENT_CALLS' => $this->jsonSerializer->unserialize($conf['variables']['ruleComponentAPIcalls']),
//        ];


        /** @var \Magento\Framework\Controller\Result\Json $result */
        $result = $this->resultJsonFactory->create();
        $config = [
            'ADOBE_IO_ACCESS_TOKEN' => null,
            'LAUNCH_COMPANY_ID' => null,
            'LAUNCH_PROPERTY_ID' => null,
            'LAUNCH_ADAPTER_ID' => null,
            'LAUNCH_ENV_ID_DEV' => null,
            'LAUNCH_ENV_ID_STAGE' => null,
            'LAUNCH_ENV_ID_PROD' => null,
            'LAUNCH_LIB_ID_CONFIG' => null,
            'LAUNCH_EXT_ID_CORE' => null,
            'LAUNCH_EXT_ID_ADOBE_ANALYTICS' => null,
            'LAUNCH_EXT_ID_ADOBE_EXPERIENCE_CLOUD' => null,
            'LAUNCH_EXT_ID_CONSTANT_DATA_ELEMENT' => null,
            'TARGET_CLIENT_CODE' => null,
            'TARGET_GLOBAL_MBOX' => null,
            'LAUNCH_EXT_ID_ADOBE_TARGET' => null,
            'LAUNCH_EXT_PACKAGE_ID_ADOBE_TARGET' => null,
            'LAUNCH_EXT_ID_SDI_TOOLKIT' => null,
            'LAUNCH_EXT_ID_SDI_PRODUCT_STR' => null,
            'LAUNCH_EXT_ID_SDI_DATALAYER_MGR' => null,
        ];
        $this->pipeline->execute($config);

        $requestResponse = [];
        return $result->setData($requestResponse);
    }

    /**
     * Get the JSON file
     *
     * @return mixed
     */
    private function getJsonConfig()
    {
        $etcDir = $this->moduleReader->getModuleDir(
            \Magento\Framework\Module\Dir::MODULE_ETC_DIR,
            'Adobe_Launch'
        );
        $file = $etcDir . '/adminhtml/provision_config.json';
        try {
            $string = $this->file->fileGetContents($file);
            return $this->jsonSerializer->unserialize($string);
        } catch (FileSystemException $e) {
            return ["error"=>$e->getMessage()];
        }
    }
}
