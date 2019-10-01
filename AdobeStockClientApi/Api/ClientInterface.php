<?php
/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */

declare(strict_types=1);

namespace Magento\AdobeStockClientApi\Api;

use Magento\AdobeStockClientApi\Api\Data\UserQuotaInterface;
use Magento\Framework\Api\Search\SearchResultInterface;
use Magento\Framework\Api\Search\SearchCriteriaInterface;

/**
 * Adobe Stock API Client
 */
interface ClientInterface
{
    /**
     * Search for assets
     *
     * @param SearchCriteriaInterface $searchCriteria
     * @return SearchResultInterface
     */
    public function search(SearchCriteriaInterface $searchCriteria): SearchResultInterface;

    /**
     * Gets  quota for current content from Adobe Stock API
     *
     * @param int $contentId
     * @return UserQuotaInterface
     */
    public function getQuota(int $contentId = 0): UserQuotaInterface;

    /**
     * Perform a basic request to Adobe Stock API to check network connection, API key, etc.
     *
     * @param string|null $apiKey
     *
     * @return bool
     */
    public function testConnection(string $apiKey = null): bool;

    /**
     * Invokes licensing image operation via Adobe Stock API
     *
     * @param int $contentId
     * @return void
     */
    public function licenseImage(int $contentId): void;

    /**
     * Returns download URL for a licensed image
     *
     * @param int $contentId
     * @return mixed
     */
    public function getImageDownloadUrl(int $contentId): string;
}
