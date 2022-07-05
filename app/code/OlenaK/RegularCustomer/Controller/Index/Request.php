<?php

declare(strict_types=1);

namespace OlenaK\RegularCustomer\Controller\Index;

use OlenaK\RegularCustomer\Model\DiscountRequest;
use Magento\Framework\App\Request\InvalidRequestException;
use Magento\Framework\App\RequestInterface;
use Magento\Framework\Controller\Result\Json;
use Magento\Catalog\Model\ResourceModel\Product\Collection as ProductCollection;
use OlenaK\RegularCustomer\Controller\InvalidFormRequestException;

class Request implements
    \Magento\Framework\App\Action\HttpPostActionInterface,
    \Magento\Framework\App\CsrfAwareActionInterface
{
    /**
     * @var \Magento\Framework\Controller\Result\JsonFactory $jsonFactory
     */
    private \Magento\Framework\Controller\Result\JsonFactory $jsonFactory;

    /**
     * @var \OlenaK\RegularCustomer\Model\DiscountRequestFactory $discountRequestFactory
     */
    private \OlenaK\RegularCustomer\Model\DiscountRequestFactory $discountRequestFactory;

    /**
     * @var \OlenaK\RegularCustomer\Model\ResourceModel\DiscountRequest $discountRequestResource
     */
    private \OlenaK\RegularCustomer\Model\ResourceModel\DiscountRequest $discountRequestResource;

    /**
     * @var \Magento\Framework\App\RequestInterface $request
     */
    private \Magento\Framework\App\RequestInterface $request;

    /**
     * @var \Magento\Store\Model\StoreManagerInterface $storeManager
     */
    private \Magento\Store\Model\StoreManagerInterface $storeManager;

    /**
     * @var \Magento\Framework\Data\Form\FormKey\Validator $formKeyValidator
     */
    private \Magento\Framework\Data\Form\FormKey\Validator $formKeyValidator;

    /**
     * @var \Magento\Customer\Model\Session $customerSession
     */
    private \Magento\Customer\Model\Session $customerSession;

    /**
     * @var \Magento\Catalog\Model\ResourceModel\Product\CollectionFactory $productCollectionFactory
     */
    private \Magento\Catalog\Model\ResourceModel\Product\CollectionFactory $productCollectionFactory;

    /**
     * @var \Psr\Log\LoggerInterface $logger
     */
    private \Psr\Log\LoggerInterface $logger;

    /**
     * @var \OlenaK\RegularCustomer\Model\Config $config
     */
    private \OlenaK\RegularCustomer\Model\Config $config;

    /**
     * @var \OlenaK\RegularCustomer\Model\Email $email
     */
    private \OlenaK\RegularCustomer\Model\Email $email;

    /**
     * @param \Magento\Framework\Controller\Result\JsonFactory $jsonFactory
     * @param \OlenaK\RegularCustomer\Model\DiscountRequestFactory $discountRequestFactory
     * @param \OlenaK\RegularCustomer\Model\ResourceModel\DiscountRequest $discountRequestResource
     * @param \Magento\Framework\App\RequestInterface $request
     * @param \Magento\Store\Model\StoreManagerInterface $storeManager
     * @param \Magento\Framework\Data\Form\FormKey\Validator $formKeyValidator
     * @param \Psr\Log\LoggerInterface $logger
     * @param \Magento\Customer\Model\Session $customerSession
     * @param \Magento\Catalog\Model\ResourceModel\Product\CollectionFactory $productCollectionFactory
     * @param \OlenaK\RegularCustomer\Model\Config $config
     * @param \OlenaK\RegularCustomer\Model\Email $email
     */
    public function __construct(
        \Magento\Framework\Controller\Result\JsonFactory $jsonFactory,
        \OlenaK\RegularCustomer\Model\DiscountRequestFactory $discountRequestFactory,
        \OlenaK\RegularCustomer\Model\ResourceModel\DiscountRequest $discountRequestResource,
        \Magento\Framework\App\RequestInterface $request,
        \Magento\Store\Model\StoreManagerInterface $storeManager,
        \Magento\Framework\Data\Form\FormKey\Validator $formKeyValidator,
        \Magento\Customer\Model\Session $customerSession,
        \Psr\Log\LoggerInterface $logger,
        \Magento\Catalog\Model\ResourceModel\Product\CollectionFactory $productCollectionFactory,
        \OlenaK\RegularCustomer\Model\Config $config,
        \OlenaK\RegularCustomer\Model\Email $email
    ) {
        $this->jsonFactory = $jsonFactory;
        $this->discountRequestFactory = $discountRequestFactory;
        $this->discountRequestResource = $discountRequestResource;
        $this->request = $request;
        $this->storeManager = $storeManager;
        $this->formKeyValidator = $formKeyValidator;
        $this->logger = $logger;
        $this->customerSession = $customerSession;
        $this->productCollectionFactory = $productCollectionFactory;
        $this->config = $config;
        $this->email = $email;
    }

    /**
     * Controller action
     *
     * @return Json
     */
    public function execute(): Json
    {
        /** @var DiscountRequest $discountRequest */
        $discountRequest = $this->discountRequestFactory->create();
        $response = $this->jsonFactory->create();

        try {
            if (!$this->config->enabled()) {
                throw new InvalidFormRequestException();
            }

            if (!$this->customerSession->isLoggedIn() && !$this->config->allowForGuests()) {
                throw new InvalidFormRequestException();
            }

            $customerId = $this->customerSession->getCustomerId()
                ? (int) $this->customerSession->getCustomerId()
                : null;

            if ($this->customerSession->isLoggedIn()) {
                $name = $this->customerSession->getCustomer()->getName();
                $email = $this->customerSession->getCustomer()->getEmail();
            } else {
                $name = $this->request->getParam('name');
                $email =  $this->request->getParam('email');
            }

            $productId = $this->request->getParam('product_id');
            $productName = '';
            if ((int) $productId !== 0) {
                /** @var ProductCollection $productCollection */
                $productCollection = $this->productCollectionFactory->create();
                $productCollection->addIdFilter($productId)
                    ->setPageSize(1)
                    ->addAttributeToSelect('name');
                $product = $productCollection->getFirstItem();
                $productId = (int) $product->getId();
                $productName = (string) $product->getName();

                if (!$productId) {
                    throw new \InvalidArgumentException("Product with id $productId does not exist");
                }

                $discountRequest->setProductId($productId);
            }

            $discountRequest
                ->setCustomerId($customerId)
                ->setName($name)
                ->setEmail($email)
                ->setStoreId($this->storeManager->getStore()->getId());

            $this->discountRequestResource->save($discountRequest);

            if (!$this->customerSession->isLoggedIn()) {
                $this->customerSession->setDiscountRequestCustomerName($name);
                $this->customerSession->setDiscountRequestCustomerEmail($email);
                $productIds = $this->customerSession->getDiscountRequestProductIds() ?? [];
                $productIds[] = $productId;
                $this->customerSession->setDiscountRequestProductIds(array_unique($productIds));
            }

            $this->email->sendNewDiscountRequestEmail($name, $email, $productName);

            return $response->setData([
                'message' => __(
                    'You request for product %1 accepted for review!',
                    $productName
                )
            ]);
        } catch (\Exception $e) {
            if (!($e instanceof InvalidFormRequestException)) {
                $this->logger->error($e->getMessage());
            }
        }

        return $response->setHttpResponseCode(400);
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
