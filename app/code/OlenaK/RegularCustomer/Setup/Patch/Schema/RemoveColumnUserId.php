<?php

declare(strict_types=1);

namespace OlenaK\RegularCustomer\Setup\Patch\Schema;

use Magento\Framework\DB\Transaction;
use Magento\Framework\DB\TransactionFactory;
use OlenaK\RegularCustomer\Model\DiscountRequest;
use OlenaK\RegularCustomer\Model\ResourceModel\Collection\CollectionFactory as DiscountRequestCollectionFactory;

class RemoveColumnUserId implements \Magento\Framework\Setup\Patch\SchemaPatchInterface
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
     * @var \Magento\Framework\Setup\SchemaSetupInterface $schemaSetup
     */
    private \Magento\Framework\Setup\SchemaSetupInterface $schemaSetup;

    /**
     * @param \Magento\Framework\Setup\SchemaSetupInterface $schemaSetup
     * @param \Magento\Framework\DB\TransactionFactory $transactionFactory
     * @param DiscountRequestCollectionFactory $discountRequestCollectionFactory
     */
    public function __construct(
        \Magento\Framework\Setup\SchemaSetupInterface $schemaSetup,
        \Magento\Framework\DB\TransactionFactory $transactionFactory,
        DiscountRequestCollectionFactory $discountRequestCollectionFactory
    ) {
        $this->schemaSetup = $schemaSetup;
        $this->transactionFactory = $transactionFactory;
        $this->discountRequestCollectionFactory = $discountRequestCollectionFactory;
    }

    /**
     * Run code inside patch
     *
     * @return RemoveColumnUserId
     */
    public function apply(): self
    {
        /** @var Transaction $transaction */
        $transaction = $this->transactionFactory->create();

        /** @var DiscountRequest $discountRequest */
        $discountRequestCollection = $this->discountRequestCollectionFactory->create();

        $adminTableName = $this->schemaSetup->getTable('admin_user');
        $tableName = $this->schemaSetup->getTable('olenak_regular_customer_request');

        $collection = $discountRequestCollection->addFieldToFilter(
            'main_table.user_id',
            ['neq' => null]
        )->join($adminTableName, 'main_table.user_id = '.$adminTableName.'.user_id');

        /** @var DiscountRequest $item */
        foreach ($collection as $item) {
            $userIdData = $item->getDataByKey('user_id');
            $item->setAdminUserId($userIdData);
            $transaction->addObject($item);
        }

        $transaction->save();

        $connection = $this->schemaSetup->getConnection();

        $connection->dropColumn($tableName, 'user_id');

        return $this;
    }

    /**
     * Get patch dependencies
     *
     * @return array
     */
    public static function getDependencies(): array
    {
        return [];
    }

    /**
     * Get patch aliases
     *
     * @return array
     */
    public function getAliases(): array
    {
        return [];
    }
}
