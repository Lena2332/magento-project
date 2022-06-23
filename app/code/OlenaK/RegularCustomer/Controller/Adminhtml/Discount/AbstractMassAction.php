<?php

declare(strict_types=1);

namespace OlenaK\RegularCustomer\Controller\Adminhtml\Discount;

use OlenaK\RegularCustomer\Model\ResourceModel\Collection\CollectionFactory
    as DiscountRequestCollectionFactory;

abstract class AbstractMassAction extends \Magento\Backend\App\Action implements
    \Magento\Framework\App\Action\HttpPostActionInterface
{
    /**
     * @var \Magento\Ui\Component\MassAction\Filter $filter
     */
    protected \Magento\Ui\Component\MassAction\Filter $filter;

    /**
     * @var DiscountRequestCollectionFactory $discountRequestCollectionFactory
     */
    protected DiscountRequestCollectionFactory $discountRequestCollectionFactory;

    /**
     * @var \Magento\Framework\DB\TransactionFactory $transactionFactory
     */
    protected \Magento\Framework\DB\TransactionFactory $transactionFactory;

    /**
     * @var \Magento\Backend\Model\Auth\Session $authSession
     */
    protected \Magento\Backend\Model\Auth\Session $authSession;

    /**
     * @param \Magento\Ui\Component\MassAction\Filter $filter
     * @param DiscountRequestCollectionFactory $discountRequestCollectionFactory
     * @param \Magento\Framework\DB\TransactionFactory $transactionFactory
     * @param \Magento\Backend\App\Action\Context $context
     * @param \Magento\Backend\Model\Auth\Session $authSession
     */
    public function __construct(
        \Magento\Ui\Component\MassAction\Filter $filter,
        DiscountRequestCollectionFactory $discountRequestCollectionFactory,
        \Magento\Framework\DB\TransactionFactory $transactionFactory,
        \Magento\Backend\App\Action\Context $context,
        \Magento\Backend\Model\Auth\Session $authSession
    ) {
        $this->filter = $filter;
        $this->discountRequestCollectionFactory = $discountRequestCollectionFactory;
        $this->transactionFactory = $transactionFactory;
        $this->authSession = $authSession;
        parent::__construct($context);
    }
}