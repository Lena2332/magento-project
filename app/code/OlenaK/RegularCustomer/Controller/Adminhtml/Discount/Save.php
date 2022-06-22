<?php

declare(strict_types=1);

namespace OlenaK\RegularCustomer\Controller\Adminhtml\Discount;

use Magento\Catalog\Model\ResourceModel\Product\Collection as ProductCollection;
use Magento\Customer\Model\ResourceModel\Customer\Collection as CustomerCollection;
use OlenaK\RegularCustomer\Model\Authorization;
use Magento\Framework\Controller\ResultInterface;

class Save extends \Magento\Backend\App\Action implements \Magento\Framework\App\Action\HttpPostActionInterface
{
    public const ADMIN_RESOURCE = Authorization::ACTION_DISCOUNT_REQUEST_EDIT;

    /**
     * @var \OlenaK\RegularCustomer\Model\DiscountRequestFactory $discountRequestFactory
     */
    private \OlenaK\RegularCustomer\Model\DiscountRequestFactory $discountRequestFactory;

    /**
     * @var \OlenaK\RegularCustomer\Model\ResourceModel\DiscountRequest $discountRequestResource
     */
    private \OlenaK\RegularCustomer\Model\ResourceModel\DiscountRequest $discountRequestResource;

    /**
     * @var \Magento\Catalog\Model\ResourceModel\Product\CollectionFactory $productCollectionFactory
     */
    private \Magento\Catalog\Model\ResourceModel\Product\CollectionFactory $productCollectionFactory;

    /**
     * @var \Magento\Customer\Model\ResourceModel\Customer\CollectionFactory $customerCollectionFactory
     */
    private \Magento\Customer\Model\ResourceModel\Customer\CollectionFactory $customerCollectionFactory;

    /**
     * @var \Magento\Backend\Model\Auth\Session $authSession
     */
    private \Magento\Backend\Model\Auth\Session $authSession;

    /**
     * Save constructor.
     * @param \OlenaK\RegularCustomer\Model\DiscountRequestFactory $discountRequestFactory
     * @param \OlenaK\RegularCustomer\Model\ResourceModel\DiscountRequest $discountRequestResource
     * @param \Magento\Backend\App\Action\Context $context
     * @param \Magento\Catalog\Model\ResourceModel\Product\CollectionFactory $productCollectionFactory
     * @param \Magento\Customer\Model\ResourceModel\Customer\CollectionFactory $customerCollectionFactory
     * @param \Magento\Backend\Model\Auth\Session $authSession
     */
    public function __construct(
        \OlenaK\RegularCustomer\Model\DiscountRequestFactory $discountRequestFactory,
        \OlenaK\RegularCustomer\Model\ResourceModel\DiscountRequest $discountRequestResource,
        \Magento\Backend\App\Action\Context $context,
        \Magento\Catalog\Model\ResourceModel\Product\CollectionFactory $productCollectionFactory,
        \Magento\Customer\Model\ResourceModel\Customer\CollectionFactory $customerCollectionFactory,
        \Magento\Backend\Model\Auth\Session $authSession
    ) {
        parent::__construct($context);
        $this->discountRequestFactory = $discountRequestFactory;
        $this->discountRequestResource = $discountRequestResource;
        $this->productCollectionFactory = $productCollectionFactory;
        $this->customerCollectionFactory = $customerCollectionFactory;
        $this->authSession = $authSession;
    }

    /**
     * Validate request data and save it
     *
     * @return ResultInterface
     */
    public function execute(): ResultInterface
    {
        $request = $this->getRequest();
        $discountRequestId = $request->getParam('request_id');
        /** @var \Magento\Backend\Model\View\Result\Redirect $resultRedirect */
        $resultRedirect = $this->resultRedirectFactory->create();

        $discountRequest = $this->discountRequestFactory->create();
        $this->discountRequestResource->load($discountRequest, $request->getParam('request_id'));

        if ($discountRequestId && !$discountRequest->getId()) {
            $this->messageManager->addErrorMessage(__('This request no longer exists.'));

            return $resultRedirect->setPath('*/*/');
        }

        //Product validation
        $productFormId = (int) $request->getParam('product_id');

        /** @var ProductCollection $productCollection */
        $productCollection = $this->productCollectionFactory->create();
        $productCollection->addIdFilter($productFormId)->setPageSize(1);
        $product = $productCollection->getFirstItem();
        $productId = (int) $product->getId();

        if (!$productId) {
            $this->messageManager->addErrorMessage(__('Product with id %1 does not exist.', $productFormId));

            return $resultRedirect->setPath(
                '*/*/edit',
                [
                    'request_id' => $discountRequest->getId()
                ]
            );
        }

        //Customer validation
        $customerFormId = (int) $request->getParam('customer_id');

        if ($customerFormId > 0) {
            /** @var CustomerCollection $customerCollection */
            $customerCollection = $this->customerCollectionFactory->create();
            $customerCollection->addFilter('entity_id', $customerFormId)->setPageSize(1);
            $customer = $customerCollection->getFirstItem();
            $customerId = (int) $customer->getId();

            if (!$customerId) {
                $this->messageManager->addErrorMessage(__('Customer with id %1 does not exist.', $customerFormId));

                return $resultRedirect->setPath(
                    '*/*/edit',
                    [
                        'request_id' => $discountRequest->getId()
                    ]
                );
            }
        }


        // Get UserID
        $userData = $this->authSession->getUser();
        $userId = ($userData) ? (int) $userData->getData('user_id') : null;

        $discountRequest->setProductId(((int) $request->getParam('product_id')) ?: null)
            ->setCustomerId(((int) $request->getParam('customer_id')) ? : null)
            ->setName($request->getParam('name'))
            ->setEmail($request->getParam('email'))
            ->setStatus((int) $request->getParam('status'))
            ->setStoreId((int) $request->getParam('store_id'))
            ->setUserId($userId);

        try {
            $this->discountRequestResource->save($discountRequest);
        } catch (\Exception $e) {
            $this->messageManager->addErrorMessage($e->getMessage());
        }

        if ($discountRequest->getId()) {
            return $resultRedirect->setPath(
                '*/*/edit',
                [
                    'request_id' => $discountRequest->getId()
                ]
            );
        }

        return $resultRedirect->setPath('*/*/index');
    }
}
