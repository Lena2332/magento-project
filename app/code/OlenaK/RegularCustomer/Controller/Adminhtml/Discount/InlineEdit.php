<?php

declare(strict_types=1);

namespace OlenaK\RegularCustomer\Controller\Adminhtml\Discount;

use OlenaK\RegularCustomer\Model\Authorization;
use OlenaK\RegularCustomer\Model\DiscountRequest;
use OlenaK\RegularCustomer\Model\ResourceModel\Collection\Collection as DiscountRequestCollection;
use OlenaK\RegularCustomer\Model\ResourceModel\Collection\CollectionFactory
    as DiscountRequestCollectionFactory;
use Magento\Framework\Controller\ResultInterface;
use Magento\Framework\DB\Transaction;

class InlineEdit extends \Magento\Backend\App\Action implements \Magento\Framework\App\Action\HttpPostActionInterface
{
    public const ADMIN_RESOURCE = Authorization::ACTION_DISCOUNT_REQUEST_EDIT;

    /**
     * @var DiscountRequestCollectionFactory $discountRequestCollectionFactory
     */
    private DiscountRequestCollectionFactory $discountRequestCollectionFactory;

    /**
     * @var \Magento\Framework\DB\TransactionFactory $transactionFactory
     */
    private \Magento\Framework\DB\TransactionFactory $transactionFactory;

    /**
     * @var \Magento\Framework\Controller\Result\JsonFactory $jsonFactory
     */
    private \Magento\Framework\Controller\Result\JsonFactory $jsonFactory;

    /**
     * @var \Magento\Backend\Model\Auth\Session $authSession
     */
    private \Magento\Backend\Model\Auth\Session $authSession;


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
     * @param DiscountRequestCollectionFactory $discountRequestCollectionFactory
     * @param \Magento\Framework\DB\TransactionFactory $transactionFactory
     * @param \Magento\Framework\Controller\Result\JsonFactory $jsonFactory
     * @param \Magento\Backend\App\Action\Context $context
     * @param \Magento\Backend\Model\Auth\Session $authSession
     * @param \Magento\Customer\Model\ResourceModel\CustomerRepository $customerRepository
     * @param \Magento\Catalog\Model\ProductRepository $productRepository
     * @param \Magento\Store\Model\StoreManager $storeManager
     * @param \OlenaK\RegularCustomer\Model\Email $email
     */
    public function __construct(
        DiscountRequestCollectionFactory $discountRequestCollectionFactory,
        \Magento\Framework\DB\TransactionFactory $transactionFactory,
        \Magento\Framework\Controller\Result\JsonFactory $jsonFactory,
        \Magento\Backend\App\Action\Context $context,
        \Magento\Backend\Model\Auth\Session $authSession,
        \Magento\Customer\Model\ResourceModel\CustomerRepository $customerRepository,
        \Magento\Catalog\Model\ProductRepository $productRepository,
        \Magento\Store\Model\StoreManager $storeManager,
        \OlenaK\RegularCustomer\Model\Email $email
    ) {
        parent::__construct($context);
        $this->jsonFactory = $jsonFactory;
        $this->discountRequestCollectionFactory = $discountRequestCollectionFactory;
        $this->transactionFactory = $transactionFactory;
        $this->authSession = $authSession;
        $this->customerRepository = $customerRepository;
        $this->productRepository = $productRepository;
        $this->storeManager = $storeManager;
        $this->email = $email;
    }

    /**
     * Edit action
     *
     * @return ResultInterface
     */
    public function execute(): ResultInterface
    {
        /** @var \Magento\Framework\Controller\Result\Json $resultJson */
        $resultJson = $this->jsonFactory->create();
        $error = true;
        $messages = [];

        try {
            if (!$this->getRequest()->getParam('isAjax')) {
                throw new \InvalidArgumentException(__('Invalid request'));
            }

            $items = $this->getRequest()->getParam('items', []);

            if (!count($items)) {
                throw new \InvalidArgumentException(__('Please correct the data sent.'));
            }

            /** @var DiscountRequestCollection $discountRequestCollection */
            $discountRequestCollection = $this->discountRequestCollectionFactory->create();
            $discountRequestCollection->addFieldToFilter(
                $discountRequestCollection->getResource()->getIdFieldName(),
                array_keys($items)
            );

            // Get UserId
            $userData = $this->authSession->getUser();
            $userId = ($userData) ? (int) $userData->getData('user_id') : null;

            /** @var Transaction $transaction */
            $transaction = $this->transactionFactory->create();

            // Used for collect data for sending email
            $sendEmailTo = [];

            foreach ($items as $discountRequestId => $itemData) {
                /** @var DiscountRequest $discountRequest */
                if (!($discountRequest = $discountRequestCollection->getItemById($discountRequestId))) {
                    $messages[] = __('Request with ID %1 does not exist', $discountRequestId);

                    continue;
                }

                $origStatus = (int) $discountRequest->getStatus();
                $newStatus = (int) $itemData['status'];

                if ($newStatus === $origStatus) {
                    continue;
                }

                $discountRequest->setStatus($newStatus);
                $discountRequest->setUserId($userId);
                $transaction->addObject($discountRequest);

                //Add to arr for sending email
                $sendEmailTo[] = [
                    'customerEmail' => $this->getCustomerEmail($discountRequest->getEmail(), (int) $discountRequest->getCustomerId()),
                    'productName' => $this->getProductName((int) $discountRequest->getProductId()),
                    'storeId' => $discountRequest->getStoreId(),
                    'status' => $newStatus
                ];
            }

            $transaction->save();

            //Send Emails to Customers
            if (!empty($sendEmailTo)) {
                foreach ($sendEmailTo as $item) {
                    $storeId = (int) $this->storeManager->getWebsite($item['storeId'])->getDefaultStore()->getId();

                    switch ($item['status']) {
                        case DiscountRequest::STATUS_APPROVED:
                            $this->email->sendRequestApprovedEmail($item['customerEmail'], $item['productName'], $storeId);
                            break;
                        case DiscountRequest::STATUS_DECLINED:
                            $this->email->sendRequestDeclinedEmail($item['customerEmail'], $item['productName'], $storeId);
                            break;
                        default:
                            break;
                    }
                }
            }

            $messages[] = __('%1 requests(s) have been updated.', count($items));
            $error = false;
        } catch (\Exception $e) {
            $messages[] = $e->getMessage();
        }

        return $resultJson->setData([
            'messages' => $messages,
            'error' => $error
        ]);
    }

    /**
     * @param int $customerId
     */
    private function getCustomerEmail (string $email, int $customerId = 0): string
    {
        $customerEmail = $email;
        if ($customerId) {
            $customerEmail = $this->customerRepository->getById($customerId)->getEmail();
        }

        return $customerEmail;
    }

    /**
     * @param int $productId
     */
    private function getProductName (int $productId): string
    {
        if ($productId) {
            $product = $this->productRepository->getById($productId);
            $productName = ($product) ? $product->getName(): '';
        }

        return $productName;
    }
}
