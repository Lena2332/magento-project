<?php

declare(strict_types=1);

namespace OlenaK\RegularCustomer\Controller\Adminhtml\Discount;

use Magento\Catalog\Model\ResourceModel\Product\Collection as ProductCollection;
use Magento\Customer\Model\ResourceModel\Customer\Collection as CustomerCollection;
use OlenaK\RegularCustomer\Model\Authorization;
use Magento\Framework\Controller\ResultInterface;
use OlenaK\RegularCustomer\Model\DiscountRequest;

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
     * @var  \OlenaK\RegularCustomer\Model\Email $email
     */
    private  \OlenaK\RegularCustomer\Model\Email $email;

    /**
     * Save constructor.
     * @param \OlenaK\RegularCustomer\Model\DiscountRequestFactory $discountRequestFactory
     * @param \OlenaK\RegularCustomer\Model\ResourceModel\DiscountRequest $discountRequestResource
     * @param \Magento\Backend\App\Action\Context $context
     * @param \Magento\Catalog\Model\ResourceModel\Product\CollectionFactory $productCollectionFactory
     * @param \Magento\Customer\Model\ResourceModel\Customer\CollectionFactory $customerCollectionFactory
     * @param \Magento\Backend\Model\Auth\Session $authSession
     * @param \OlenaK\RegularCustomer\Model\Email $email
     */
    public function __construct(
        \OlenaK\RegularCustomer\Model\DiscountRequestFactory $discountRequestFactory,
        \OlenaK\RegularCustomer\Model\ResourceModel\DiscountRequest $discountRequestResource,
        \Magento\Backend\App\Action\Context $context,
        \Magento\Catalog\Model\ResourceModel\Product\CollectionFactory $productCollectionFactory,
        \Magento\Customer\Model\ResourceModel\Customer\CollectionFactory $customerCollectionFactory,
        \Magento\Backend\Model\Auth\Session $authSession,
        \OlenaK\RegularCustomer\Model\Email $email
    ) {
        parent::__construct($context);
        $this->discountRequestFactory = $discountRequestFactory;
        $this->discountRequestResource = $discountRequestResource;
        $this->productCollectionFactory = $productCollectionFactory;
        $this->customerCollectionFactory = $customerCollectionFactory;
        $this->authSession = $authSession;
        $this->email = $email;
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

        $productName = "";
        if($productFormId !== 0) {
            /** @var ProductCollection $productCollection */
            $productCollection = $this->productCollectionFactory->create();
            $productCollection->addIdFilter($productFormId)
                ->setPageSize(1)
                ->addAttributeToSelect('name');
            $product = $productCollection->getFirstItem();
            $productId = (int) $product->getId();
            $productName = (string) $product->getName();

            $discountRequest->setProductId($productId);

            if (!$productId) {
                $this->messageManager->addErrorMessage(__('Product with id %1 does not exist.', $productFormId));

                return $resultRedirect->setPath(
                    '*/*/edit',
                    [
                        'request_id' => $discountRequest->getId()
                    ]
                );
            }
        }


        //Customer validation
        $customerFormId = (int) $request->getParam('customer_id');

        if ($customerFormId > 0) {
            /** @var CustomerCollection $customerCollection */
            $customerCollection = $this->customerCollectionFactory->create();
            $customerCollection->addFilter('entity_id', $customerFormId)
                ->setPageSize(1)
                ->addAttributeToSelect('email');
            $customer = $customerCollection->getFirstItem();
            $customerId = (int) $customer->getId();
            $customerEmail = (string) $customer->getEmail();

            $discountRequest->setCustomerId($customerId);

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

        // Get previous and current statuses
        $previousStatus = (int) $discountRequest->getStatus();
        $currentStatus = (int) $request->getParam('status');

        $discountRequest->setName($request->getParam('name'))
            ->setEmail($request->getParam('email'))
            ->setStatus($currentStatus)
            ->setStoreId((int) $request->getParam('store_id'))
            ->setAdminUserId($userId);

        try {
            $this->discountRequestResource->save($discountRequest);
        } catch (\Exception $e) {
            $this->messageManager->addErrorMessage($e->getMessage());
        }

        if ($discountRequest->getId()) {
            //Send email to customer
            $storeId = (int) $discountRequest->getStoreId();
            $customerEmail = $customerEmail ?? $discountRequest->getEmail();

            $emailSentStatus = false;
            if ($request->getParam('notify')) {
                switch ($currentStatus) {
                    case DiscountRequest::STATUS_APPROVED:
                        $emailSentStatus = $this->email->sendRequestApprovedEmail($customerEmail, $productName, $storeId);
                        break;
                    case DiscountRequest::STATUS_DECLINED:
                        $emailSentStatus = $this->email->sendRequestDeclinedEmail($customerEmail, $productName, $storeId);
                        break;
                    default:
                        break;
                }
            }

            // Update field email_sent 0 if cheanged status and not allowed notification, 1 - if email was sent
            if (($currentStatus !== $previousStatus && !$request->getParam('notify')) ||  $emailSentStatus) {
                $this->updateEmailStatus($discountRequest, $emailSentStatus);
            }

            return $resultRedirect->setPath(
                '*/*/edit',
                [
                    'request_id' => $discountRequest->getId()
                ]
            );
        }

        return $resultRedirect->setPath('*/*/index');
    }

    /**
     * @param DiscountRequest $discountRequest
     * @param bool $emailStatus
     * @return void
     */
    private function updateEmailStatus(DiscountRequest $discountRequest, bool $emailStatus): void
    {
        $discountRequest->setEmailSent((int) $emailStatus);

        try {
            $this->discountRequestResource->save($discountRequest);
        } catch (\Exception $e) {
            $this->messageManager->addErrorMessage($e->getMessage());
        }
    }
}
