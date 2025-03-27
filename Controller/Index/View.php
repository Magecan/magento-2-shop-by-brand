<?php
/**
 * Copyright © Magecan, Inc. All rights reserved.
 */
namespace Magecan\ShopByBrand\Controller\Index;

use Magento\Framework\App\Action\Context;
use Magento\Framework\View\Result\PageFactory;
use Magento\Catalog\Model\ResourceModel\Product\CollectionFactory as ProductCollectionFactory;
use Magento\Eav\Api\AttributeRepositoryInterface;

class View extends \Magento\Framework\App\Action\Action
{
    protected $resultPageFactory;
    protected $productCollectionFactory;
    protected $attributeRepository;

    public function __construct(
        Context $context,
        PageFactory $resultPageFactory,
        ProductCollectionFactory $productCollectionFactory,
        AttributeRepositoryInterface $attributeRepository
    ) {
        $this->resultPageFactory = $resultPageFactory;
        $this->productCollectionFactory = $productCollectionFactory;
        $this->attributeRepository = $attributeRepository;
        parent::__construct($context);
    }

    public function execute()
    {
        $brandLabel = $this->getRequest()->getParam('brand');

        if (!$brandLabel) {
            return $this->_redirect('noroute');
        }

        // Get the brand option ID
        $attribute = $this->attributeRepository->get('catalog_product', 'brand');
        $options = $attribute->getSource()->getAllOptions();
        $brandId = null;

        foreach ($options as $option) {
            if ($option['label'] === $brandLabel) {
                $brandId = $option['value'];
                break;
            }
        }

        if (!$brandId) {
            return $this->_redirect('noroute');
        }

        $resultPage = $this->resultPageFactory->create();
        $resultPage->getConfig()->getTitle()->set(__($brandLabel));

        // Set brand ID in the registry
        $this->_objectManager->get(\Magento\Framework\Registry::class)
            ->register('current_brand_id', $brandId);

        return $resultPage;
    }
}
