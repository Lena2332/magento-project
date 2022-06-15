<?php

declare(strict_types=1);

namespace OlenaK\RegularCustomer\CustomerData;

use Magento\Customer\CustomerData\SectionSourceInterface;

class RegularCustomer implements SectionSourceInterface
{
    /**
     * @var \Magento\Customer\Model\Session $customerSession
     */
    private \Magento\Customer\Model\Session $customerSession;

    /**
     * @var \OlenaK\RegularCustomer\Model\DiscountRequestProvider
     */
    private \OlenaK\RegularCustomer\Model\DiscountRequestProvider $discountRequestProvider;

    /**
     * @param \OlenaK\RegularCustomer\Model\DiscountRequestProvider $discountRequestProvider
     * @param \Magento\Customer\Model\Session $customerSession
     */
    public function __construct(
        \Magento\Customer\Model\Session $customerSession,
        \OlenaK\RegularCustomer\Model\DiscountRequestProvider $discountRequestProvider
    ) {
        $this->customerSession = $customerSession;
        $this->discountRequestProvider = $discountRequestProvider;
    }

    /**
     * @inheritDoc
     */
    public function getSectionData(): array
    {
        $name = (string) $this->customerSession->getDiscountRequestCustomerName();
        $email = (string) $this->customerSession->getDiscountRequestCustomerEmail();

        if ($this->customerSession->isLoggedIn()) {
            $name = $this->customerSession->getCustomer()->getName();
            $email = $this->customerSession->getCustomer()->getEmail();

            $customerId =  (int) $this->customerSession->getCustomerId();
            $discountRequestCollection = $this->discountRequestProvider->getCurrentCustomerDiscountRequests($customerId);
            $productIds = $discountRequestCollection->getColumnValues('product_id');
            $productIds = array_unique(array_filter($productIds));
            $productIds = array_values(array_map('intval', $productIds));
        } else {
            $productIds = (array) $this->customerSession->getDiscountRequestProductIds();
        }

        return [
            'name' => $name,
            'email' => $email,
            'productIds' => $productIds,
            'isLoggedIn' => $this->customerSession->isLoggedIn()
        ];
    }
}
