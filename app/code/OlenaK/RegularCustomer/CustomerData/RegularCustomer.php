<?php

declare(strict_types=1);

namespace OlenaK\RegularCustomer\CustomerData;

use Magento\Customer\CustomerData\SectionSourceInterface;

class RegularCustomer implements SectionSourceInterface
{
    /**
     * @inheritDoc
     */
    public function getSectionData(): array
    {
        return [
            'sectionData' => 'It works!'
        ];
    }
}
