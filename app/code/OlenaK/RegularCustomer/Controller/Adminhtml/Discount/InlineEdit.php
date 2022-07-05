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
     * @param DiscountRequestCollectionFactory $discountRequestCollectionFactory
     * @param \Magento\Framework\DB\TransactionFactory $transactionFactory
     * @param \Magento\Framework\Controller\Result\JsonFactory $jsonFactory
     * @param \Magento\Backend\App\Action\Context $context
     * @param \Magento\Backend\Model\Auth\Session $authSession
     * @param \Magento\Customer\Model\ResourceModel\CustomerRepository $customerRepository
     * @param \Magento\Store\Model\StoreManager $storeManager
     * @param \OlenaK\RegularCustomer\Model\Email $email
     * @param \OlenaK\RegularCustomer\Model\CustomerProvider $customerProvider
     * @param \OlenaK\RegularCustomer\Model\ProductProvider $productProvider
     */
    public function __construct(
        DiscountRequestCollectionFactory $discountRequestCollectionFactory,
        \Magento\Framework\DB\TransactionFactory $transactionFactory,
        \Magento\Framework\Controller\Result\JsonFactory $jsonFactory,
        \Magento\Backend\App\Action\Context $context,
        \Magento\Backend\Model\Auth\Session $authSession,
        \Magento\Store\Model\StoreManager $storeManager,
        \OlenaK\RegularCustomer\Model\Email $email,
        \OlenaK\RegularCustomer\Model\CustomerProvider $customerProvider,
        \OlenaK\RegularCustomer\Model\ProductProvider $productProvider
    ) {
        parent::__construct($context);
        $this->jsonFactory = $jsonFactory;
        $this->discountRequestCollectionFactory = $discountRequestCollectionFactory;
        $this->transactionFactory = $transactionFactory;
        $this->authSession = $authSession;
        $this->storeManager = $storeManager;
        $this->email = $email;
        $this->customerProvider = $customerProvider;
        $this->productProvider = $productProvider;
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
                    'customerEmail' => $this->customerProvider->getCustomerEmail($discountRequest->getEmail(), (int) $discountRequest->getCustomerId()),
                    'productName' => $this->productProvider->getProductName((int) $discountRequest->getProductId()),
                    'storeId' => $discountRequest->getStoreId(),
                    'status' => $newStatus
                ];
            }

            $transaction->save();

            //Send Emails to Customers
            $this->email->massSend($sendEmailTo);

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
}
