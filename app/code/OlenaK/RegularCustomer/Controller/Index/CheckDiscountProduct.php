<?php

declare(strict_types=1);

namespace OlenaK\RegularCustomer\Controller\Index;

use Magento\Framework\App\Request\InvalidRequestException;
use Magento\Framework\App\RequestInterface;
use Magento\Framework\Controller\Result\Json;
use OlenaK\RegularCustomer\Model\DiscountRequestProvider;

class CheckDiscountProduct implements
    \Magento\Framework\App\Action\HttpPostActionInterface,
    \Magento\Framework\App\CsrfAwareActionInterface
{
    /**
     * @var \Magento\Framework\App\RequestInterface $request
     */
    private \Magento\Framework\App\RequestInterface $request;

    /**
     * @var \Magento\Framework\Controller\Result\JsonFactory $jsonFactory
     */
    private \Magento\Framework\Controller\Result\JsonFactory $jsonFactory;

    /**
     * @var DiscountRequestProvider
     */
    private DiscountRequestProvider $discountRequestProvider;

    /**
     * @var \Magento\Framework\Data\Form\FormKey\Validator $formKeyValidator
     */
    private \Magento\Framework\Data\Form\FormKey\Validator $formKeyValidator;

    /**
     * @var \Magento\Customer\Model\Session $customerSession
     */
    private \Magento\Customer\Model\Session $customerSession;

    /**
     * @param \Magento\Framework\App\RequestInterface $request
     * @param \Magento\Framework\Controller\Result\JsonFactory $jsonFactory
     * @param DiscountRequestProvider $discountRequestProvider
     * @param \Magento\Framework\Data\Form\FormKey\Validator $formKeyValidator
     * @param \Magento\Customer\Model\Session $customerSession
     */
    public function __construct (
        \Magento\Framework\App\RequestInterface $request,
        \Magento\Framework\Controller\Result\JsonFactory $jsonFactory,
        DiscountRequestProvider $discountRequestProvider,
        \Magento\Framework\Data\Form\FormKey\Validator $formKeyValidator,
        \Magento\Customer\Model\Session $customerSession
    ) {
        $this->request = $request;
        $this->jsonFactory = $jsonFactory;
        $this->discountRequestProvider = $discountRequestProvider;
        $this->formKeyValidator = $formKeyValidator;
        $this->customerSession = $customerSession;
    }

    /**
     * Check if has been requested product by customer
     * @return Json
     */
    public function execute(): Json
    {
        $productId = (int) $this->request->getParam('product_id');

        if ($this->customerSession->isLoggedIn()) {
           $customerId = (int) $this->customerSession->getCustomerId();
           $disqountRequestCollection = $this->discountRequestProvider->getCurrentCustomerDiscountRequests($customerId);
           $productIdsArr = array_unique(array_filter($disqountRequestCollection->getColumnValues('product_id')));
        } else{
           $productIdsArr = $this->customerSession->getDiscountRequestProductIds();
        }

        $isUsed = in_array($productId, $productIdsArr);

        return $this->jsonFactory->create()
            ->setData([
                'isUsed' => $isUsed
            ]);
    }

    /**
     * Create exception in case CSRF validation failed. Return null if default exception will suffice.
     *
     * @param RequestInterface $request
     * @return InvalidRequestException|null
     */
    public function createCsrfValidationException(RequestInterface $request): ?InvalidRequestException
    {
        return null;
    }

    /**
     * Perform custom request validation. Return null if default validation is needed.
     *
     * @param RequestInterface $request
     * @return bool
     */
    public function validateForCsrf(RequestInterface $request): bool
    {
        return $this->formKeyValidator->validate($request);
    }
}
