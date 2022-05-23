<?php

declare(strict_types=1);

namespace OlenaK\Catalog\Setup\Patch\Data;

use Magento\Catalog\Model\Product\Website as ProductWebsite;
use Magento\Catalog\Model\ResourceModel\Product\Collection as ProductCollection;

class AddProductsToAllWebsites implements \Magento\Framework\Setup\Patch\DataPatchInterface
{
    /**
     * @var \Magento\Catalog\Model\ResourceModel\Product\CollectionFactory $productCollectionFactory
     */
    private \Magento\Catalog\Model\ResourceModel\Product\CollectionFactory $productCollectionFactory;

    /**
     * @var \Magento\Catalog\Model\Product\WebsiteFactory $productWebsiteFactory
     */
    private \Magento\Catalog\Model\Product\WebsiteFactory $productWebsiteFactory;

    /**
     * @var \Magento\Store\Model\StoreManagerInterface $storeManager
     */
    private \Magento\Store\Model\StoreManagerInterface $storeManager;

    /**
     * AddProductsToBothWebsites constructor.
     * @param \Magento\Catalog\Model\ResourceModel\Product\CollectionFactory $productCollectionFactory
     * @param \Magento\Catalog\Model\Product\WebsiteFactory $productWebsiteFactory
     * @param \Magento\Store\Model\StoreManagerInterface $storeManager
     */
    public function __construct(
        \Magento\Catalog\Model\ResourceModel\Product\CollectionFactory $productCollectionFactory,
        \Magento\Catalog\Model\Product\WebsiteFactory $productWebsiteFactory,
        \Magento\Store\Model\StoreManagerInterface $storeManager
    ) {
        $this->productCollectionFactory = $productCollectionFactory;
        $this->productWebsiteFactory = $productWebsiteFactory;
        $this->storeManager = $storeManager;
    }

    /**
     * @inheritDoc
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    public function apply(): void
    {
        /** @var ProductCollection $productCollection */
        $productCollection = $this->productCollectionFactory->create();
        $productIds = $productCollection->getAllIds();
        /** @var ProductWebsite $productWebsite */
        $productWebsite = $this->productWebsiteFactory->create();
        $productWebsite->addProducts(array_keys($this->storeManager->getWebsites()), $productIds);
    }

    /**
     * @inheritDoc
     */
    public static function getDependencies(): array
    {
        return [];
    }

    /**
     * @inheritDoc
     */
    public function getAliases(): array
    {
        return [];
    }
}
