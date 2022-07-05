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
     * @var \Magento\Store\Model\StoreManager $storeManager
     */
    protected \Magento\Store\Model\StoreManager $storeManager;

    /**
     * @var \OlenaK\RegularCustomer\Model\Email $email
     */
    protected \OlenaK\RegularCustomer\Model\Email $email;

    /**
     * @var \OlenaK\RegularCustomer\Model\CustomerProvider $customerProvider
     */
    protected \OlenaK\RegularCustomer\Model\CustomerProvider $customerProvider;

    /**
     * @var \OlenaK\RegularCustomer\Model\ProductProvider $productProvider
     */
    protected \OlenaK\RegularCustomer\Model\ProductProvider $productProvider;

    /**
     * @param \Magento\Ui\Component\MassAction\Filter $filter
     * @param DiscountRequestCollectionFactory $discountRequestCollectionFactory
     * @param \Magento\Framework\DB\TransactionFactory $transactionFactory
     * @param \Magento\Backend\App\Action\Context $context
     * @param \Magento\Backend\Model\Auth\Session $authSession
     * @param \Magento\Store\Model\StoreManager $storeManager
     * @param \OlenaK\RegularCustomer\Model\Email $email
     * @param \OlenaK\RegularCustomer\Model\CustomerProvider $customerProvider
     * @param \OlenaK\RegularCustomer\Model\ProductProvider $productProvider
     */
    public function __construct(
        \Magento\Ui\Component\MassAction\Filter $filter,
        DiscountRequestCollectionFactory $discountRequestCollectionFactory,
        \Magento\Framework\DB\TransactionFactory $transactionFactory,
        \Magento\Backend\App\Action\Context $context,
        \Magento\Backend\Model\Auth\Session $authSession,
        \Magento\Store\Model\StoreManager $storeManager,
        \OlenaK\RegularCustomer\Model\Email $email,
        \OlenaK\RegularCustomer\Model\CustomerProvider $customerProvider,
        \OlenaK\RegularCustomer\Model\ProductProvider $productProvider
    ) {
        $this->filter = $filter;
        $this->discountRequestCollectionFactory = $discountRequestCollectionFactory;
        $this->transactionFactory = $transactionFactory;
        $this->authSession = $authSession;
        $this->storeManager = $storeManager;
        $this->email = $email;
        $this->customerProvider = $customerProvider;
        $this->productProvider = $productProvider;
        parent::__construct($context);
    }
}
