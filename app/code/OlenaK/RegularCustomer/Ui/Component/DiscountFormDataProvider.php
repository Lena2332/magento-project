<?php

declare(strict_types=1);

namespace OlenaK\RegularCustomer\Ui\Component;

use OlenaK\RegularCustomer\Model\ResourceModel\Collection\CollectionFactory as DiscountRequestCollectionFactory;

class DiscountFormDataProvider extends \Magento\Ui\DataProvider\AbstractDataProvider
{
    /**
     * DiscountFormDataProvider constructor.
     * @param DiscountRequestCollectionFactory $discountRequestCollectionFactory
     * @param string $name
     * @param string $primaryFieldName
     * @param string $requestFieldName
     * @param array $meta
     * @param array $data
     */
    public function __construct(
        DiscountRequestCollectionFactory $discountRequestCollectionFactory,
        string $name,
        string $primaryFieldName,
        string $requestFieldName,
        array $meta = [],
        array $data = []
    ) {
        parent::__construct($name, $primaryFieldName, $requestFieldName, $meta, $data);
        $this->collection = $discountRequestCollectionFactory->create();
    }

    /**
     * Get data
     *
     * @return array
     */
    public function getData(): array
    {
        $data = [];

        foreach (parent::getData()['items'] as $item) {
            $item['email_sent'] = $this->convertBooltoYesNo((int) $item['email_sent']);
            $data[$item['request_id']] = $item;
        }

        return $data;
    }


    /**
     * @param int $val 0|1
     * @return string yes|no
     */
    private function convertBooltoYesNo (int $val): string
    {
        $sourceData = [0 => __('No'), 1 => __('Yes')];
        return (isset($sourceData[$val])) ? $sourceData[$val]->getText() : '';
    }
}

