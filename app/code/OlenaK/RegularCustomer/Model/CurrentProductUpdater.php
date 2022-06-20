<?php

declare(strict_types=1);

namespace OlenaK\RegularCustomer\Model;

class CurrentProductUpdater implements \Magento\Framework\View\Layout\Argument\UpdaterInterface
{
    /**
     * @var \Magento\Catalog\Helper\Data $productHelper
     */
    private \Magento\Catalog\Helper\Data $productHelper;

    /**
     * @var \OlenaK\RegularCustomer\Model\Config $config
     */
    private \OlenaK\RegularCustomer\Model\Config $config;

    /**
     * @param \Magento\Catalog\Helper\Data $productHelper
     * @param \OlenaK\RegularCustomer\Model\Config $config
     */
    public function __construct(
        \Magento\Catalog\Helper\Data $productHelper,
        \OlenaK\RegularCustomer\Model\Config $config
    ) {
        $this->productHelper = $productHelper;
        $this->config = $config;
    }

    /**
     * Set current product id to jsLayout for passing it to the Knockout component
     *
     * @param array $value
     * @return array
     */
    public function update($value): array
    {
        if ($this->productHelper->getProduct()) {
            $value['components']['regularCustomerRequest']['children']['regularCustomerRequestForm']['config']
            ['productId'] = (int) $this->productHelper->getProduct()->getId();
        }

        $value['components']['regularCustomerRequest']['children']['regularCustomerRequestForm']['config']
        ['allowForGuests'] = (bool) $this->config->allowForGuests();

        return $value;
    }
}
