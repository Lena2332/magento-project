<?php

declare(strict_types=1);

namespace OlenaK\RegularCustomer\Model\ResourceModel\Collection;

class Collection extends \Magento\Framework\Model\ResourceModel\Db\Collection\AbstractCollection
{
    /**
     * @inheritDoc
     */
    protected function _construct(): void
    {
        parent::_construct();
        $this->_init(
            \OlenaK\RegularCustomer\Model\DiscountRequest::class,
            \OlenaK\RegularCustomer\Model\ResourceModel\DiscountRequest::class
        );
    }
}
