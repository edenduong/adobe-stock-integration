<?php
/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */

declare(strict_types=1);

namespace Magento\MediaGalleryMetadataApi\Api;

use Magento\Framework\Exception\LocalizedException;
use Magento\MediaGalleryMetadataApi\Api\Data\MetadataInterface;

/**
 * Extract asset metadata
 */
interface ExtractMetadataInterface
{
    /**
     * Extract metadata from the asset file
     *
     * @param string $path
     * @return MetadataInterface
     */
    public function execute(string $path): MetadataInterface;
}
