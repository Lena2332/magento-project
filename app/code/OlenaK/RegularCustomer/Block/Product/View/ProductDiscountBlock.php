<?php

declare(strict_types=1);

namespace OlenaK\RegularCustomer\Block\Product\View;

class ProductDiscountBlock extends \Magento\Framework\View\Element\Template
{
    /**
     * Get cache key information incl. current product ID
     *
     * @return array
     */
    public function getCacheKeyInfo(): array
    {
        return array_merge(parent::getCacheKeyInfo(), ['product_id' => $this->getProduct()->getId()]);
    }
}
