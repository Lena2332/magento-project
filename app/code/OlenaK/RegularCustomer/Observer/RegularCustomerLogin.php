<?php

declare(strict_types=1);

namespace OlenaK\RegularCustomer\Observer;

use Magento\Customer\Model\Session;
use Magento\Framework\DB\Transaction;
use Magento\Framework\DB\TransactionFactory;
use Magento\Framework\Event\Observer;
use Magento\Store\Model\StoreManagerInterface;
use Magento\Store\Model\Website;
use OlenaK\RegularCustomer\Model\DiscountRequest;
use OlenaK\RegularCustomer\Model\ResourceModel\Collection\CollectionFactory as DiscountRequestCollectionFactory;
use Psr\Log\LoggerInterface;

class RegularCustomerLogin implements \Magento\Framework\Event\ObserverInterface
{
    /**
     * @var \Magento\Customer\Model\Session $customerSession
     */
    private \Magento\Customer\Model\Session $customerSession;

    /**
     * @var DiscountRequestCollectionFactory $discountRequestCollectionFactory
     */
    private DiscountRequestCollectionFactory $discountRequestCollectionFactory;

    /**
     * @var \Magento\Framework\DB\TransactionFactory $transactionFactory
     */
    private \Magento\Framework\DB\TransactionFactory $transactionFactory;

    /**
     * @var \Magento\Store\Model\StoreManagerInterface $storeManager
     */
    private \Magento\Store\Model\StoreManagerInterface $storeManager;

    /**
     * @var \Psr\Log\LoggerInterface $logger
     */
    private \Psr\Log\LoggerInterface $logger;

    /**
     * @param Session $customerSession
     * @param DiscountRequestCollectionFactory $discountRequestCollectionFactory
     * @param TransactionFactory $transactionFactory
     * @param StoreManagerInterface $storeManager
     */
    public function __construct(
        \Magento\Customer\Model\Session $customerSession,
        DiscountRequestCollectionFactory $discountRequestCollectionFactory,
        \Magento\Framework\DB\TransactionFactory $transactionFactory,
        \Magento\Store\Model\StoreManagerInterface $storeManager,
        \Psr\Log\LoggerInterface $logger
    )
    {
        $this->customerSession = $customerSession;
        $this->discountRequestCollectionFactory = $discountRequestCollectionFactory;
        $this->transactionFactory = $transactionFactory;
        $this->storeManager = $storeManager;
        $this->logger = $logger;
    }

    public function execute(Observer $observer)
    {
        try {
            /** @var Website $website */
            $website = $this->storeManager->getWebsite();

            // Take customerId
            $customerId = $this->customerSession->getCustomerId();

            // Take all requests
            $productIds = $this->customerSession->getDiscountRequestProductIds() ?? [];

            if ($customerId && !empty($productIds)) {
                $name = $this->customerSession->getCustomer()->getName();
                $email = $this->customerSession->getCustomer()->getEmail();

                // Set customerId for all requests, delete duplicates if it needs
                /** @var Transaction $transaction */
                $transaction = $this->transactionFactory->create();

                /** @var DiscountRequest $discountRequest */
                $discountRequestCollection = $this->discountRequestCollectionFactory->create();

                $collection = $discountRequestCollection->addFieldToFilter(
                        'name',
                        ['eq' => $name]
                    )->addFieldToFilter(
                        'email',
                        ['eq' => $email]
                    )->addFieldToFilter(
                        'product_id',
                        ['in' => $productIds]
                    )->addFieldToFilter(
                        'store_id',
                        ['in' => $website->getStoreIds()]
                );

                if ($collection->count()) {
                    /** @var DiscountRequest $item */
                    foreach ($collection as $item) {
                        $item->setCustomerId($customerId);
                        $transaction->addObject($item);
                    }
                }

                $transaction->save();
            }
        } catch (\Exception $e) {
            $this->logger->error($e->getMessage());
        }
    }
}
