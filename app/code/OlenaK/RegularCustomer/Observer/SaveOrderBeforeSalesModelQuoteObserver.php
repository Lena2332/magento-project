<?php

declare(strict_types=1);

namespace OlenaK\RegularCustomer\Observer;

use Magento\Framework\DataObject\Copy;
use Magento\Framework\Event\Observer;
use Magento\Framework\Event\ObserverInterface;
use Magento\Quote\Model\Quote;
use Magento\Sales\Model\Order;

class SaveOrderBeforeSalesModelQuoteObserver implements ObserverInterface
{
    /**
     * @param Observer $observer
     */
    public function execute(Observer $observer)
    {
        /* @var Order $order */
        $order = $observer->getEvent()->getData('order');

        /* @var Quote $quote */
        $quote = $observer->getEvent()->getData('quote');

        $regularCustomer = $quote->getDataByKey('regular_customer');
        $baseRegularCustomer = $quote->getDataByKey('base_regular_customer');

        $order->setData('regular_customer', $regularCustomer);
        $order->setData('base_regular_customer', $baseRegularCustomer);

        return $this;
    }
}
