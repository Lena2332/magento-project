<?php

declare(strict_types=1);

namespace OlenaK\RegularCustomer\Model\Quote\Total;

use Magento\Quote\Model\Quote\Address\Total\AbstractTotal;
use OlenaK\RegularCustomer\Model\DiscountRequest;
use OlenaK\RegularCustomer\Model\ResourceModel\Collection\CollectionFactory as DiscountRequestCollectionFactory;

class RegularCustomer extends \Magento\Quote\Model\Quote\Address\Total\AbstractTotal
{
    public const DISCOUNT_PERCENT = 0.05;

    public const TOTAL_CODE = 'regular_customer';

    private $productRequestIds = [];

    /**
     * @var \Magento\Customer\Model\Session $customerSession
     */
    private \Magento\Customer\Model\Session $customerSession;

    /**
     * @var DiscountRequestCollectionFactory $discountRequestCollectionFactory
     */
    private DiscountRequestCollectionFactory $discountRequestCollectionFactory;

    public function __construct(
        \Magento\Customer\Model\Session $customerSession,
        DiscountRequestCollectionFactory $discountRequestCollectionFactory
    )
    {
        $this->customerSession = $customerSession;
        $this->discountRequestCollectionFactory = $discountRequestCollectionFactory;
    }

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

        //Get from session productIds for request
        $this->productRequestIds = $this->getProductRequestIds();

        //Calculate our discount
        $productsInCart = $quote->getAllItems();
        $regularCustomerDiscount = 0;
        $baseRegularCustomerDiscount = 0;
        foreach ($productsInCart as $product) {
            $productId = (int) $product->getProduct()->getId();

            $baseProductPrice = ($product->getParentItem()) ?
                $product->getParentItem()->getBaseCalculationPrice() :
                $product->getBaseCalculationPrice();

            $productPrice = ($product->getParentItem()) ?
                $product->getParentItem()->getCalculationPrice() :
                $product->getCalculationPrice();

            $quantity = $product->getTotalQty();

            if (in_array($productId, $this->productRequestIds)) {
                $regularCustomerDiscount += ($productPrice * self::DISCOUNT_PERCENT) * $quantity;
                $baseRegularCustomerDiscount += ($baseProductPrice * self::DISCOUNT_PERCENT) * $quantity;
            }
        }

        $total->addTotalAmount(self::TOTAL_CODE, -$regularCustomerDiscount);
        $total->addBaseTotalAmount(self::TOTAL_CODE, -$baseRegularCustomerDiscount);
        $quote->setData(self::TOTAL_CODE, -$regularCustomerDiscount);
        $quote->setData('base_' . self::TOTAL_CODE, -$baseRegularCustomerDiscount);

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

    /**
     * @return array
     */
    private function getProductRequestIds(): array
    {
        if ($this->customerSession->isLoggedIn()) {
            $customerId = $this->customerSession->getCustomerId();

            /** @var DiscountRequest $discountRequest */
            $discountRequestCollection = $this->discountRequestCollectionFactory->create();
            $productRequestIds = $discountRequestCollection->addFieldToFilter('status',
                ['eq' => DiscountRequest::STATUS_APPROVED]
            )->addFieldToFilter('customer_id',
                ['eq' => $customerId]
            )->getColumnValues('product_id');
            $productRequestIds = array_map('intval', $productRequestIds);
        } else {
            $productRequestIds = $this->customerSession->getDiscountRequestProductIds();
        }

        return $productRequestIds;
    }
}
