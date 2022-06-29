<?php

declare(strict_types=1);

namespace OlenaK\RegularCustomer\Model\Quote\Total;

use Magento\Quote\Model\Quote\Address\Total\AbstractTotal;

class RegularCustomer extends \Magento\Quote\Model\Quote\Address\Total\AbstractTotal
{
    public const DISCOUNT_PERCENT = 0.05;

    public const TOTAL_CODE = 'regular_customer';

    /**
     * @param \Magento\Quote\Model\Quote $quote
     * @param \Magento\Quote\Api\Data\ShippingAssignmentInterface $shippingAssignment
     * @param \Magento\Quote\Model\Quote\Address\Total $total
     * @return AbstractTotal
     */
    public function collect(
        \Magento\Quote\Model\Quote $quote,
        \Magento\Quote\Api\Data\ShippingAssignmentInterface $shippingAssignment,
        \Magento\Quote\Model\Quote\Address\Total $total
    ): AbstractTotal {
        parent::collect($quote, $shippingAssignment, $total);
        $regularCustomer = -($total->getSubtotal() * self::DISCOUNT_PERCENT);
        $baseRegularCustomer = -($total->getBaseSubtotal() * self::DISCOUNT_PERCENT);

        $total->addTotalAmount(self::TOTAL_CODE, $regularCustomer);
        $total->addBaseTotalAmount(self::TOTAL_CODE, $baseRegularCustomer);
        $quote->setData(self::TOTAL_CODE, $regularCustomer);
        $quote->setData('base_' . self::TOTAL_CODE, $baseRegularCustomer);

        return $this;
    }

    /**
     * Assign subtotal amount and label to address object
     *
     * @param \Magento\Quote\Model\Quote $quote
     * @param \Magento\Quote\Model\Quote\Address\Total $total
     * @return array
     * @SuppressWarnings(PHPMD.UnusedFormalParameter)
     */
    public function fetch(\Magento\Quote\Model\Quote $quote, \Magento\Quote\Model\Quote\Address\Total $total)
    {
        return [
            'code'  => self::TOTAL_CODE,
            'title' => $this->getLabel(),
            'value' => $quote->getData(self::TOTAL_CODE)
        ];
    }

    /**
     * Get Subtotal label
     *
     * @return \Magento\Framework\Phrase
     */
    public function getLabel()
    {
        return __('Regular Customer');
    }
}
