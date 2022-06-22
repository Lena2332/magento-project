<?php

declare(strict_types=1);

namespace OlenaK\RegularCustomer\Controller\Adminhtml\Discount;

use OlenaK\RegularCustomer\Model\Authorization;
use Magento\Framework\Controller\ResultFactory;
use Magento\Framework\Controller\ResultInterface;
use Magento\Framework\DB\Transaction;

class MassDelete extends AbstractMassAction
{
    public const ADMIN_RESOURCE = Authorization::ACTION_DISCOUNT_REQUEST_DELETE;

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
        $collectionSize = $collection->count();

        foreach ($collection as $item) {
            $transaction->addObject($item);
        }

        try {
            $transaction->delete();
            $this->messageManager->addSuccessMessage(__('%1 requests(s) have been deleted.', $collectionSize));
        } catch (\Exception $e) {
            $this->messageManager->addSuccessMessage($e->getMessage());
        }

        /** @var \Magento\Backend\Model\View\Result\Redirect $resultRedirect */
        $resultRedirect = $this->resultFactory->create(ResultFactory::TYPE_REDIRECT);

        return $resultRedirect->setPath('*/*/');
    }
}
