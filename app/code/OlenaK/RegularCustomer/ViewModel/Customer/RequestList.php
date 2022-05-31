<?php

declare(strict_types=1);

namespace OlenaK\RegularCustomer\ViewModel\Customer;

use OlenaK\RegularCustomer\Model\DiscountRequestProvider;
use OlenaK\RegularCustomer\Model\ResourceModel\Collection\Collection as DiscountRequestCollection;
use Magento\Catalog\Model\ResourceModel\Product\Collection as ProductCollection;
use Magento\Catalog\Model\Product;

class RequestList implements \Magento\Framework\View\Element\Block\ArgumentInterface
{
    /**
     * @var \Magento\Catalog\Model\ResourceModel\Product\CollectionFactory $productCollectionFactory
     */
    private \Magento\Catalog\Model\ResourceModel\Product\CollectionFactory $productCollectionFactory;


    /**
     * @var DiscountRequestCollection $loadedDiscountRequestCollection
     */
    private DiscountRequestCollection $loadedDiscountRequestCollection;

    /**
     * @var ProductCollection $loadedProductCollection
     */
    private ProductCollection $loadedProductCollection;

    /**
     * @var \Magento\Catalog\Model\Product\Visibility $productVisibility
     */
    private \Magento\Catalog\Model\Product\Visibility $productVisibility;

    /**
     * @var \Magento\Customer\Model\Session $customerSession
     */
    private \Magento\Customer\Model\Session $customerSession;

    /**
     * @var DiscountRequestProvider
     */
    private DiscountRequestProvider $discountRequestProvider;

    /**
     * @param DiscountRequestProvider $discountRequestProvider
     * @param \Magento\Catalog\Model\ResourceModel\Product\CollectionFactory $productCollectionFactory
     * @param \Magento\Store\Model\StoreManagerInterface $storeManager
     * @param Product\Visibility $productVisibility
     * @param \Magento\Customer\Model\Session $customerSession
     */
    public function __construct(
        DiscountRequestProvider $discountRequestProvider,
        \Magento\Catalog\Model\ResourceModel\Product\CollectionFactory $productCollectionFactory,
        \Magento\Store\Model\StoreManagerInterface $storeManager,
        \Magento\Catalog\Model\Product\Visibility $productVisibility,
        \Magento\Customer\Model\Session $customerSession
    ) {
        $this->discountRequestProvider = $discountRequestProvider;
        $this->storeManager = $storeManager;
        $this->productCollectionFactory = $productCollectionFactory;
        $this->productVisibility = $productVisibility;
        $this->customerSession = $customerSession;
    }

    /**
     * Get a list of customer discount requests
     *
     * @return DiscountRequestCollection
     */
    public function getDiscountRequestCollection(): DiscountRequestCollection
    {
        if (isset($this->loadedDiscountRequestCollection)) {
            return $this->loadedDiscountRequestCollection;
        }
        // Get current customer Id
        $customerId =  (int) $this->customerSession->getCustomerId();
        $collection = $this->discountRequestProvider->getCurrentCustomerDiscountRequests($customerId);
        $this->loadedDiscountRequestCollection = $collection;

        return $this->loadedDiscountRequestCollection;
    }

    /**
     * Get product for customer discount request
     *
     * @param int $productId
     * @return Product|null
     */
    public function getProduct(int $productId): ?Product
    {
        if (isset($this->loadedProductCollection)) {
            return $this->loadedProductCollection->getItemById($productId);
        }

        $discountRequestCollection = $this->getDiscountRequestCollection();
        $productIds = array_unique(array_filter($discountRequestCollection->getColumnValues('product_id')));

        $productCollection = $this->productCollectionFactory->create();
        // Inactive products are filtered by default
        $productCollection->addAttributeToFilter('entity_id', ['in' => $productIds])
            ->addAttributeToSelect('name')
            ->addWebsiteFilter()
            ->setVisibility($this->productVisibility->getVisibleInSiteIds());
        $this->loadedProductCollection = $productCollection;

        return $this->loadedProductCollection->getItemById($productId);
    }
}
