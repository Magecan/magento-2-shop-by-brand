<?php
/**
 * Copyright © Magecan, Inc. All rights reserved.
 */
namespace Magecan\ShopByBrand\Block;

use Magento\Framework\View\Element\Template;
use Magento\Framework\View\Element\Template\Context;
use Magento\Widget\Block\BlockInterface;
use Magento\Eav\Model\Config;
use Magento\Swatches\Model\Swatch;
use Magento\Swatches\Helper\Data as SwatchDataHelper;
use Magento\Swatches\Helper\Media as SwatchMediaHelper;
use Magento\Store\Model\ScopeInterface;

class BrandSlider extends Template implements BlockInterface
{
    protected $_template = "brandslider.phtml";
    protected $swatchDataHelper;
    protected $swatchMediaHelper;

    public function __construct(
        Context $context,
        Config $eavConfig,
        SwatchDataHelper $swatchDataHelper,
        SwatchMediaHelper $swatchMediaHelper,
        array $data = []
    ) {
        $this->eavConfig = $eavConfig;
        $this->swatchDataHelper = $swatchDataHelper;
        $this->swatchMediaHelper = $swatchMediaHelper;
        parent::__construct($context, $data);
    }
    
    protected function _construct()
    {
        parent::_construct();
        
        $this->setEnable(
            $this->_scopeConfig->isSetFlag(
                    'shop_by_brand/general/enable',
                    ScopeInterface::SCOPE_STORE
            )
        );
        
        $this->setCarouselTitle(
            $this->_scopeConfig->getValue(
                    'shop_by_brand/brand_carousel/carousel_title',
                    ScopeInterface::SCOPE_STORE
            )
        );
        
        $this->setInfinite(
            $this->_scopeConfig->isSetFlag(
                    'shop_by_brand/brand_carousel/infinite',
                    ScopeInterface::SCOPE_STORE
            )
        );
        
        $this->setAutoplay(
            $this->_scopeConfig->isSetFlag(
                    'shop_by_brand/brand_carousel/autoplay',
                    ScopeInterface::SCOPE_STORE
            )
        );
        
        $this->setAutoplaySpeed(
            $this->_scopeConfig->getValue(
                    'shop_by_brand/brand_carousel/autoplay_speed',
                    ScopeInterface::SCOPE_STORE
            )
        );
        
        $this->setArrows(
            $this->_scopeConfig->isSetFlag(
                    'shop_by_brand/brand_carousel/arrows',
                    ScopeInterface::SCOPE_STORE
            )
        );
        
        $this->setDots(
            $this->_scopeConfig->isSetFlag(
                    'shop_by_brand/brand_carousel/dots',
                    ScopeInterface::SCOPE_STORE
            )
        );
    }

    public function getBrandOptions()
    {
        $attribute = $this->eavConfig->getAttribute('catalog_product', 'tire_brand');

        if (!$attribute ||
            !$attribute->getId() ||
            $attribute->getFrontendInput() !== 'select'
        ) {
            return [];
        }

        $swatchData = [];
        
        $options = $attribute->getSource()->getAllOptions();
        $swatches = $this->swatchDataHelper->getSwatchesByOptionsId(array_column($options, 'value'));

        foreach ($options as $option) {
            $optionId = $option['value'];
            if (!isset($swatches[$optionId])) {
                continue;
            }

            $swatch = $swatches[$optionId];
            if ($swatch['type'] == Swatch::SWATCH_TYPE_VISUAL_IMAGE) { // Image swatch
                $swatchImageUrl = $this->swatchMediaHelper->getSwatchAttributeImage(Swatch::SWATCH_THUMBNAIL_NAME, $swatch['value']);
                $swatchData[] = [
                    'label' => $option['label'],
                    'image' => $swatchImageUrl
                ];
            }
        }

        return $swatchData;
    }
}
