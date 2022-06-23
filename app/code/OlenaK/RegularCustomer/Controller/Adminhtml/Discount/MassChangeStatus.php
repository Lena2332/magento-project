<?php

declare(strict_types=1);

namespace OlenaK\RegularCustomer\Controller\Adminhtml\Discount;

use OlenaK\RegularCustomer\Model\Authorization;
use OlenaK\RegularCustomer\Model\DiscountRequest;
use Magento\Framework\Controller\ResultFactory;
use Magento\Framework\Controller\ResultInterface;
use Magento\Framework\DB\Transaction;

class MassChangeStatus extends AbstractMassAction
{
    public const ADMIN_RESOURCE = Authorization::ACTION_DISCOUNT_REQUEST_EDIT;

    /**
     * Dispatch request
     *
     * @return ResultInterface
     * @throws \Magento\Framework\Exception\LocalizedException
     * @throws \Exception
     */
    public function execute(): ResultInterface
    {
        /** @var Transaction $transaction */
        $transaction = $this->transactionFactory->create();
        $collection = $this->filter->getCollection($this->discountRequestCollectionFactory->create());
        $status = (int) $this->getRequest()->getParam('status');
        $collection->addFieldToFilter('status', ['neq' => $status]);
        $collectionSize = $collection->count();

        // Get UserId
        $userData = $this->authSession->getUser();
        $userId = ($userData) ? (int) $userData->getData('user_id') : null;

        // Used for collect data for sending email
        $sendEmailTo = [];

        /** @var DiscountRequest $item */
        foreach ($collection as $item) {
            $item->setStatus($status);
            $item->setUserId($userId);
            $transaction->addObject($item);

            //Add to arr for sending email
            $sendEmailTo[] = [
                'customerEmail' => $this->getCustomerEmail($item->getEmail(), (int) $item->getCustomerId()),
                'productName' => $this->getProductName((int) $item->getProductId()),
                'storeId' => $item->getStoreId(),
                'status' => $status
            ];
        }

        try {
            $transaction->save();
            $this->messageManager->addSuccessMessage(__('%1 requests(s) have been updated.', $collectionSize));
        } catch (\Exception $e) {
            $this->messageManager->addSuccessMessage($e->getMessage());
        }

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

        /** @var \Magento\Backend\Model\View\Result\Redirect $resultRedirect */
        $resultRedirect = $this->resultFactory->create(ResultFactory::TYPE_REDIRECT);

        return $resultRedirect->setPath('*/*/');
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
