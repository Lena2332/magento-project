<?php

declare(strict_types=1);

namespace OlenaK\RegularCustomer\Ui\Component\DiscountRequest\Form\Button;

use OlenaK\RegularCustomer\Model\Authorization;

class Save implements \Magento\Framework\View\Element\UiComponent\Control\ButtonProviderInterface
{
    /**
     * Get button configuration
     *
     * @return array
     */
    public function getButtonData(): array
    {
        return [
            'label' => __('Save'),
            'class' => 'save primary',
            'data_attribute' => [
                'mage_init' => [
                    'button' => [
                        'event' => 'save'
                    ],
                ],
                'form-role' => 'save'
            ],
            'aclResource' => Authorization::ACTION_DISCOUNT_REQUEST_EDIT,
            'sort_order' => 10
        ];
    }
}
