<?php

declare(strict_types=1);

namespace OlenaK\RegularCustomer\Model\Sales\Pdf;

class RegularCustomerTotal extends \Magento\Sales\Model\Order\Pdf\Total\DefaultTotal
{
    public function getTotalsForDisplay(): array
    {
        $order = $this->getOrder();

        $regularCustomer = $order->getDataByKey('regular_customer');

        if ($regularCustomer === null) {
            return [];
        }

        $fontSize = $this->getFontSize() ? $this->getFontSize() : 7;

        return [
            [
                'amount' => $regularCustomer,
                'label' => __('Regular Customer'),
                'font_size' => $fontSize,
            ]
        ];
    }
}
