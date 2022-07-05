<?php

declare(strict_types=1);

namespace OlenaK\RegularCustomer\Model;

class CustomerProvider
{
    /**
     * @var \Magento\Customer\Model\ResourceModel\CustomerRepository $customerRepository
     */
    protected \Magento\Customer\Model\ResourceModel\CustomerRepository $customerRepository;

    /**
     * @param \Magento\Customer\Model\ResourceModel\CustomerRepository $customerRepository
     */
    public function __construct(
        \Magento\Customer\Model\ResourceModel\CustomerRepository $customerRepository
    ) {
        $this->customerRepository = $customerRepository;
    }

    /**
     * @param int $customerId
     * @param string $email
     * @return string
     */
    public function getCustomerEmail(string $email, int $customerId = 0): string
    {
        $customerEmail = $email;
        if ($customerId) {
            $customerEmail = $this->customerRepository->getById($customerId)->getEmail();
        }

        return $customerEmail;
    }
}
