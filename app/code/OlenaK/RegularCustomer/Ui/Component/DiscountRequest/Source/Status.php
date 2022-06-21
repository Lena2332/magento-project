<?php

declare(strict_types=1);

namespace OlenaK\RegularCustomer\Ui\Component\DiscountRequest\Source;

use OlenaK\RegularCustomer\Model\DiscountRequest;

class Status implements \Magento\Framework\Data\OptionSourceInterface
{
    /**
     * Get request status and respective labels
     *
     * @return array
     */
    public function toOptionArray(): array
    {
        return [
            [
                'value' => DiscountRequest::STATUS_PENDING,
                'label' => __('Pending')
            ], [
                'value' => DiscountRequest::STATUS_APPROVED,
                'label' => __('Approved')
            ], [
                'value' => DiscountRequest::STATUS_DECLINED,
                'label' => __('Declined')
            ]
        ];
    }

    /**
     * Get array of statuses and corresponding option labels
     *
     * @return array
     */
    public function asArray(): array
    {
        return [
            DiscountRequest::STATUS_PENDING => __('Pending'),
            DiscountRequest::STATUS_APPROVED => __('Approved'),
            DiscountRequest::STATUS_DECLINED => __('Declined')
        ];
    }
}
