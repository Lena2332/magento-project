<?php

declare(strict_types=1);

namespace OlenaK\CMS\Model;

class Options implements \Magento\Framework\Data\OptionSourceInterface
{
    /**
     * Get example select options for usage in models, templates, whatever
     *
     * @return \string[][]
     */
    public function toOptionArray(): array
    {
        return [
            [
                'label' => __('Option 1'),
                'value' => 'option_1'
            ], [
                'label' => __('Option 2'),
                'value' => 'option_2'
            ], [
                'label' => __('Option 3'),
                'value' => 'option_3'
            ]
        ];
    }
}
