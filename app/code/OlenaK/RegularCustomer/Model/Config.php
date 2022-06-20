<?php

declare(strict_types=1);

namespace OlenaK\RegularCustomer\Model;

use Magento\Store\Model\ScopeInterface;

class Config
{
    public const XML_PATH_OLENAK_REGULAR_CUSTOMER_GENERAL_ENABLED
        = 'olenak_regular_customer/general/enabled';

    public const XML_PATH_OLENAK_REGULAR_CUSTOMER_GENERAL_ALLOW_FOR_GUESTS
        = 'olenak_regular_customer/general/allow_for_guests';

    /**
     * @var \Magento\Framework\App\Config\ScopeConfigInterface $scopeConfig
     */
    private \Magento\Framework\App\Config\ScopeConfigInterface $scopeConfig;

    /**
     * Config constructor.
     * @param \Magento\Framework\App\Config\ScopeConfigInterface $scopeConfig
     */
    public function __construct(\Magento\Framework\App\Config\ScopeConfigInterface $scopeConfig)
    {
        $this->scopeConfig = $scopeConfig;
    }

    /**
     * Get whether the module is enabled or not
     *
     * @return bool
     */
    public function enabled(): bool
    {
        return (bool)$this->scopeConfig->getValue(
            self::XML_PATH_OLENAK_REGULAR_CUSTOMER_GENERAL_ENABLED,
            ScopeInterface::SCOPE_WEBSITE
        );
    }

    /**
     * Get if guest customers can submit requests
     *
     * @return bool
     */
    public function allowForGuests(): bool
    {
        return (bool)$this->scopeConfig->getValue(
            self::XML_PATH_OLENAK_REGULAR_CUSTOMER_GENERAL_ALLOW_FOR_GUESTS,
            ScopeInterface::SCOPE_WEBSITE
        );
    }
}
