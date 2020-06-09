<?php

/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */
declare(strict_types=1);

namespace Adobe\Launch\Model\Provision;

class Pipeline implements PipeInterface
{
    /**
     * @var PipeInterface[]
     */
    private $pipes;

    /**
     * @param array $pipes
     */
    public function __construct(array $pipes)
    {
        $this->pipes = [];
        usort(
            $pipes,
            function ($firstItem, $secondItem) {
                if (!isset($firstItem['sortOrder']) || !isset($secondItem['sortOrder'])) {
                    return 0;
                }
                return $firstItem['sortOrder'] <=> $secondItem['sortOrder'];
            }
        );

        foreach ($pipes as $readerInfo) {
            if (!isset($readerInfo['pipe'])) {
                continue;
            }
            $this->pipes[] = $readerInfo['pipe'];
        }
    }

    /**
     * @return void
     */
    public function execute(&$config): void
    {
        foreach ($this->pipes as $pipe) {
            $pipe->execute($config);
        }
    }
}
