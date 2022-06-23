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
     * @var \Magento\Customer\Model\ResourceModel\CustomerRepository $customerRepository
     */
    protected \Magento\Customer\Model\ResourceModel\CustomerRepository $customerRepository;

    /**
     * @var \Magento\Catalog\Model\ProductRepository $productRepository
     */
    protected \Magento\Catalog\Model\ProductRepository $productRepository;

    /**
     * @var \Magento\Store\Model\StoreManager $storeManager
     */
    protected \Magento\Store\Model\StoreManager $storeManager;

    /**
     * @var \OlenaK\RegularCustomer\Model\Email $email
     */
    protected \OlenaK\RegularCustomer\Model\Email $email;

    /**
     * @param \Magento\Ui\Component\MassAction\Filter $filter
     * @param DiscountRequestCollectionFactory $discountRequestCollectionFactory
     * @param \Magento\Framework\DB\TransactionFactory $transactionFactory
     * @param \Magento\Backend\App\Action\Context $context
     * @param \Magento\Backend\Model\Auth\Session $authSession
     * @param \Magento\Customer\Model\ResourceModel\CustomerRepository $customerRepository
     * @param \Magento\Catalog\Model\ProductRepository $productRepository
     * @param \Magento\Store\Model\StoreManager $storeManager
     * @param \OlenaK\RegularCustomer\Model\Email $email
     */
    public function __construct(
        \Magento\Ui\Component\MassAction\Filter $filter,
        DiscountRequestCollectionFactory $discountRequestCollectionFactory,
        \Magento\Framework\DB\TransactionFactory $transactionFactory,
        \Magento\Backend\App\Action\Context $context,
        \Magento\Backend\Model\Auth\Session $authSession,
        \Magento\Customer\Model\ResourceModel\CustomerRepository $customerRepository,
        \Magento\Catalog\Model\ProductRepository $productRepository,
        \Magento\Store\Model\StoreManager $storeManager,
        \OlenaK\RegularCustomer\Model\Email $email
    ) {
        $this->filter = $filter;
        $this->discountRequestCollectionFactory = $discountRequestCollectionFactory;
        $this->transactionFactory = $transactionFactory;
        $this->authSession = $authSession;
        $this->customerRepository = $customerRepository;
        $this->productRepository = $productRepository;
        $this->storeManager = $storeManager;
        $this->email = $email;
        parent::__construct($context);
    }
}
