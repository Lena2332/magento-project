<?php

declare(strict_types=1);

namespace OlenaK\RegularCustomer\Setup\Patch\Schema;

class RemoveColumnUserId implements \Magento\Framework\Setup\Patch\SchemaPatchInterface
{
    /**
     * @var \Magento\Framework\Setup\SchemaSetupInterface $schemaSetup
     */
    private \Magento\Framework\Setup\SchemaSetupInterface $schemaSetup;

    /**
     * @param \Magento\Framework\Setup\SchemaSetupInterface $schemaSetup
     */
    public function __construct(
        \Magento\Framework\Setup\SchemaSetupInterface $schemaSetup
    ) {
        $this->schemaSetup = $schemaSetup;
    }

    /**
     * Run code inside patch
     *
     * @return RemoveColumnUserId
     */
    public function apply(): self
    {
        $connection = $this->schemaSetup->getConnection();
        $tableName = $this->schemaSetup->getTable('olenak_regular_customer_request');

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
