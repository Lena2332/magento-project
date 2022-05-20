<?php

declare(strict_types=1);

namespace OlenaK\RegularCustomer\Block\Customer;

use Magento\Customer\Block\Account\SortLinkInterface;

class RequestListLink extends \Magento\Framework\View\Element\Html\Link implements SortLinkInterface
{

    /**
     * @return string
     */
    public function getHref()
    {
        return $this->getUrl('regular-customer/customer/requestlist');
    }

    /**
     * {@inheritdoc}
     */
    public function getSortOrder()
    {
        return $this->getData(self::SORT_ORDER);
    }
}
