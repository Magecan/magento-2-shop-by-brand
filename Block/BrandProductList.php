<?php
/**
 * Copyright © Magecan, Inc. All rights reserved.
 */
namespace Magecan\ShopByBrand\Block;

use Magento\Catalog\Block\Product\ListProduct;
use Magento\Framework\Registry;
use Magento\Catalog\Model\ResourceModel\Product\CollectionFactory as ProductCollectionFactory;
use Magento\Catalog\Api\CategoryRepositoryInterface;
use Magento\Catalog\Model\Layer\Resolver;
use Magento\Framework\Data\Helper\PostHelper;
use Magento\Framework\Url\Helper\Data;
use Magento\Catalog\Helper\Output as OutputHelper;

class BrandProductList extends ListProduct
{
    protected $registry;
    protected $productCollectionFactory;

    public function __construct(
        \Magento\Catalog\Model\ResourceModel\Product\CollectionFactory $productCollectionFactory,
        Registry $registry,
        \Magento\Catalog\Block\Product\Context $context,
        PostHelper $postDataHelper,
        Resolver $layerResolver,
        CategoryRepositoryInterface $categoryRepository,
        Data $urlHelper,
        array $data = [],
        ?OutputHelper $outputHelper = null
    ) {
        $this->registry = $registry;
        $this->productCollectionFactory = $productCollectionFactory;
        parent::__construct(
            $context,
            $postDataHelper,
            $layerResolver,
            $categoryRepository,
            $urlHelper,
            $data,
            $outputHelper
        );
    }

    protected function _getProductCollection()
    {
        $brandId = $this->registry->registry('current_brand_id');

        $pageSize = (int) $this->getRequest()->getParam('limit', 12); // Default to 12 per page
        $currentPage = (int) $this->getRequest()->getParam('p', 1);   // Current page

        $collection = $this->productCollectionFactory->create();
        $collection->addAttributeToSelect('*')
            ->addAttributeToFilter('tire_brand', $brandId)
            ->addAttributeToFilter('visibility', ['neq' => 1])
            ->addAttributeToFilter('status', 1)
            ->setPageSize($pageSize)
            ->setCurPage($currentPage);

        $this->_productCollection = $collection;

        return $this->_productCollection;
    }
}
