<?php

declare(strict_types=1);

namespace OlenaK\RegularCustomer\Model;


class ProductProvider
{
    /**
     * @var \Magento\Catalog\Model\ProductRepository $productRepository
     */
    protected \Magento\Catalog\Model\ProductRepository $productRepository;

    /**
     * @param \Magento\Catalog\Model\ProductRepository $productRepository
     */
    public function __construct(
        \Magento\Catalog\Model\ProductRepository $productRepository
    ) {
        $this->productRepository = $productRepository;
    }

    /**
     * @param int $productId
     * @return string
     */
    public function getProductName(int $productId): string
    {
        $productName = '';

        if ($productId) {
            $product = $this->productRepository->getById($productId);
            $productName = ($product) ? $product->getName(): '';
        }

        return $productName;
    }
}
