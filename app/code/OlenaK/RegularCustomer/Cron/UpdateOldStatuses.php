<?php

declare(strict_types=1);

namespace OlenaK\RegularCustomer\Cron;

use Magento\Framework\DB\Transaction;
use Magento\Framework\DB\TransactionFactory;
use OlenaK\RegularCustomer\Model\DiscountRequest;
use OlenaK\RegularCustomer\Model\ResourceModel\Collection\CollectionFactory as DiscountRequestCollectionFactory;

class UpdateOldStatuses
{
    /**
     * @var DiscountRequestCollectionFactory $discountRequestCollectionFactory
     */
    private DiscountRequestCollectionFactory $discountRequestCollectionFactory;

    /**
     * @var \Magento\Framework\DB\TransactionFactory $transactionFactory
     */
    private \Magento\Framework\DB\TransactionFactory $transactionFactory;

    /**
     * @param DiscountRequestCollectionFactory $discountRequestCollectionFactory
     * @param TransactionFactory $transactionFactory
     */
    public function __construct(
        DiscountRequestCollectionFactory $discountRequestCollectionFactory,
        \Magento\Framework\DB\TransactionFactory $transactionFactory
    )
    {
        $this->discountRequestCollectionFactory = $discountRequestCollectionFactory;
        $this->transactionFactory = $transactionFactory;
    }

    public function execute(): void
    {
        /** @var Transaction $transaction */
        $transaction = $this->transactionFactory->create();

        /** @var DiscountRequest $discountRequest */
        $discountRequestCollection = $this->discountRequestCollectionFactory->create();

        $dateNow = strtotime(date("Y-m-d h:i:s"));
        $datePast = strtotime('-3 day', $dateNow);

        $collection = $discountRequestCollection->addFieldToFilter('status',
                ['eq' => DiscountRequest::STATUS_PENDING]
            )
            ->addFieldToFilter('created_at',
                ['lt' => date("Y-m-d h:i:s", $datePast)]
            );

        /** @var DiscountRequest $item */
        if ($collection->count()) {
            foreach ($collection as $item) {
                $item->setStatus(DiscountRequest::STATUS_APPROVED);
                $item->setStatusChangedAt(date("Y-m-d h:i:s"));

                $transaction->addObject($item);
            }
        }

        $transaction->save();
    }
}
