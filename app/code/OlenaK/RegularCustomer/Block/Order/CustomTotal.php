<?php

declare(strict_types=1);

namespace OlenaK\RegularCustomer\Block\Order;

class CustomTotal extends \Magento\Framework\View\Element\AbstractBlock
{
    public function initTotals()
    {
        $orderTotalsBlock = $this->getParentBlock();
        $order = $orderTotalsBlock->getOrder();

        $regularCustomer = $order->getDataByKey('regular_customer');
        $baseRegularCustomer = $order->getDataByKey('base_regular_customer');

        if ($order->getCustomAmount() > 0) {
            $orderTotalsBlock->addTotal(new \Magento\Framework\DataObject([
                'code' => 'regular_customer',
                'label' => __('Regular Customer'),
                'value' => $regularCustomer,
                'base_value' => $baseRegularCustomer,
            ]), 'subtotal');
        }
    }

}
