<?php

declare(strict_types=1);

namespace OlenaK\RegularCustomer\Model;

use Magento\Store\Model\Website;
use OlenaK\RegularCustomer\Model\ResourceModel\Collection\Collection as DiscountRequestCollection;
use OlenaK\RegularCustomer\Model\ResourceModel\Collection\CollectionFactory as DiscountRequestCollectionFactory;

class DiscountRequestProvider
{
    /**
     * @var DiscountRequestCollectionFactory $discountRequestCollectionFactory
     */
    private DiscountRequestCollectionFactory $discountRequestCollectionFactory;

    /**
     * @var \Magento\Store\Model\StoreManagerInterface $storeManager
     */
    private \Magento\Store\Model\StoreManagerInterface $storeManager;

    public function __construct (
        DiscountRequestCollectionFactory $discountRequestCollectionFactory,
        \Magento\Store\Model\StoreManagerInterface $storeManager
    ) {
        $this->discountRequestCollectionFactory = $discountRequestCollectionFactory;
        $this->storeManager = $storeManager;
    }

    /**
     * Get a list of customer discount requests
     * @return DiscountRequestCollection
     */
    public function getCurrentCustomerDiscountRequests(int $customerId): DiscountRequestCollection
    {
        /** @var Website $website */
        $website = $this->storeManager->getWebsite();

        /** @var DiscountRequestCollection $collection */
        $collection = $this->discountRequestCollectionFactory->create();
        $collection->addFieldToFilter('customer_id', $customerId);
        // @TODO: check if accounts are shared per website or not
        $collection->addFieldToFilter('store_id', ['in' => $website->getStoreIds()]);

        return $collection;
    }
}
