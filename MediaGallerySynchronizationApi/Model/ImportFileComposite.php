<?php
/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */
declare(strict_types=1);

namespace Magento\MediaGallerySynchronizationApi\Model;

/**
 * File save pool
 */
class ImportFileComposite
{
    /**
     * @var ImportFileInterface[]
     */
    private $importers;

    /**
     * @param ImportFileInterface[] $importers
     */
    public function __construct(array $importers)
    {
        ksort($importers);
        $this->importers =$importers;
    }

    /**
     * Save file data
     *
     * @param string $path
     * @throws LocalizedException
     */
    public function execute(string $path): void
    {
        foreach ($this->importers as $importer) {
            $importer->execute($path);
        }
    }
}
