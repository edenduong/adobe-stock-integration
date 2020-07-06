<?php
/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */

declare(strict_types=1);

namespace Magento\MediaGalleryCatalogUi\Model\Listing;

use Magento\Framework\Api\FilterBuilder;
use Magento\Framework\Api\Search\ReportingInterface;
use Magento\Framework\Api\Search\SearchCriteriaBuilder;
use Magento\Framework\Api\Search\SearchResultInterface;
use Magento\Framework\App\RequestInterface;
use Magento\Framework\View\Element\UiComponent\DataProvider\DataProvider as UiComponentDataProvider;
use Magento\Framework\Api\Search\SearchResultFactory;
use Magento\Framework\Api\AttributeValueFactory;
use Magento\Framework\Api\Search\Document;
use Magento\Framework\Api\Search\DocumentFactory;
use Magento\Catalog\Api\CategoryListInterface;

/**
 * DataProvider of category grid.
 */
class DataProvider extends UiComponentDataProvider
{
    /**
     * @var SearchResultFactory
     */
    private $searchResultFactory;

    /**
     * @var CategoryListInterface
     */
    private $categoryList;

    /**
     * @var AttributeValueFactory
     */
    private $attributeValueFactory;

    /**
     * @var DocumentFactory
     */
    private $documentFactory;

    /**
     * @param string $name
     * @param string $primaryFieldName
     * @param string $requestFieldName
     * @param ReportingInterface $reporting
     * @param SearchCriteriaBuilder $searchCriteriaBuilder
     * @param RequestInterface $request
     * @param FilterBuilder $filterBuilder
     * @param SearchResultFactory $searchResultFactory
     * @param CategoryListInterface $categoryList
     * @param AttributeValueFactory $attributeValueFactory
     * @param DocumentFactory $documentFactory
     * @param array $meta
     * @param array $data
     * @SuppressWarnings(PHPMD.ExcessiveParameterList)
     */
    public function __construct(
        string $name,
        string $primaryFieldName,
        string $requestFieldName,
        ReportingInterface $reporting,
        SearchCriteriaBuilder $searchCriteriaBuilder,
        RequestInterface $request,
        FilterBuilder $filterBuilder,
        SearchResultFactory $searchResultFactory,
        CategoryListInterface $categoryList,
        AttributeValueFactory $attributeValueFactory,
        DocumentFactory $documentFactory,
        array $meta = [],
        array $data = []
    ) {
        parent::__construct(
            $name,
            $primaryFieldName,
            $requestFieldName,
            $reporting,
            $searchCriteriaBuilder,
            $request,
            $filterBuilder,
            $meta,
            $data
        );
        $this->categoryList = $categoryList;
        $this->searchResultFactory = $searchResultFactory;
        $this->attributeValueFactory = $attributeValueFactory;
        $this->documentFactory = $documentFactory;
    }

    /**
     * @inheritdoc
     */
    public function getData()
    {
        try {
            return $this->searchResultToOutput($this->getSearchResult());
        } catch (\Exception $exception) {
            return [
                'items' => [],
                'totalRecords' => 0,
                'errorMessage' => $exception->getMessage()
            ];
        }
    }
    
    /**
     * @inheritDoc
     */
    public function getSearchResult(): SearchResultInterface
    {
        $searchCriteria = $this->getSearchCriteria();
        $collection = $this->categoryList->getList($searchCriteria);
        $items = [];
        foreach ($collection->getItems() as $category) {
            if ($category->getId() == 1) {
                continue;
            }
            $items[] = $this->createDocument(
                [
                    'entity_id' => $category->getEntityId(),
                    'name'  => $category->getName(),
                    'image' => $category->getImage(),
                    'path' => $category->getPath(),
                    'display_mode' => $category->getDisplayMode(),
                    'products' => $category->getProductCount(),
                    'include_in_menu' => $category->getIncludeInMenu(),
                    'is_active' => $category->getIsActive()
                ]
            );
        }
        
        $searchResult = $this->searchResultFactory->create();
        $searchResult->setSearchCriteria($searchCriteria);
        $searchResult->setItems($items);
        $searchResult->setTotalCount($collection->getTotalCount());
        
        return $searchResult;
    }

    /**
     * Add attributes to grid result
     *
     * @param array $attributes [code => value]
     */
    private function createDocument(array $attributes): Document
    {
        $item = $this->documentFactory->create();
        $customAttributes = [];

        foreach ($attributes as $code => $value) {
            $attribute = $this->attributeValueFactory->create();
            $attribute->setAttributeCode($code);
            $attribute->setValue($value);
            $customAttributes[$code] = $attribute;
        }

        $item->setCustomAttributes($customAttributes);

        return $item;
    }
}
