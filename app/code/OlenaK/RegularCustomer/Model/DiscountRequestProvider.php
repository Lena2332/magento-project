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

    /**
     * @var \Magento\Customer\Model\Config\Share $shareConfig
     */
    private \Magento\Customer\Model\Config\Share $shareConfig;

    /**
     * @param DiscountRequestCollectionFactory $discountRequestCollectionFactory
     * @param \Magento\Store\Model\StoreManagerInterface $storeManager
     * @param \Magento\Customer\Model\Config\Share $shareConfig
     */
    public function __construct (
        DiscountRequestCollectionFactory $discountRequestCollectionFactory,
        \Magento\Store\Model\StoreManagerInterface $storeManager,
        \Magento\Customer\Model\Config\Share $shareConfig
    ) {
        $this->discountRequestCollectionFactory = $discountRequestCollectionFactory;
        $this->storeManager = $storeManager;
        $this->shareConfig = $shareConfig;
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

        //We show requests only for users registered this website and created request in this website
        //If we want show all requests from all websites we need to use ->isGlobalScope()
        if ($this->shareConfig->isWebsiteScope()) {
            $collection->addFieldToFilter('store_id', ['in' => $website->getStoreIds()]);
        }

        return $collection;
    }
}
