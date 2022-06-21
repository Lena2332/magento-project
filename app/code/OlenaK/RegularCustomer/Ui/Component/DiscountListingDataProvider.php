<?php

declare(strict_types=1);

namespace OlenaK\RegularCustomer\UI\Component;

use Magento\Catalog\Model\Product;

class DiscountListingDataProvider extends \Magento\Framework\View\Element\UiComponent\DataProvider\DataProvider
{
    /**
     * @var \Magento\Backend\Model\UrlInterface $urlBuilder
     */
    private \Magento\Backend\Model\UrlInterface $urlBuilder;

    /**
     * @var \Magento\Catalog\Model\ResourceModel\Product\CollectionFactory $productCollectionFactory
     */
    private \Magento\Catalog\Model\ResourceModel\Product\CollectionFactory $productCollectionFactory;

    /**
     * @param \Magento\Backend\Model\UrlInterface $urlBuilder
     * @param \Magento\Catalog\Model\ResourceModel\Product\CollectionFactory $productCollectionFactory
     * @param string $name
     * @param string $primaryFieldName
     * @param string $requestFieldName
     * @param \Magento\Framework\Api\Search\ReportingInterface $reporting
     * @param \Magento\Framework\Api\Search\SearchCriteriaBuilder $searchCriteriaBuilder
     * @param \Magento\Framework\App\RequestInterface $request
     * @param \Magento\Framework\Api\FilterBuilder $filterBuilder
     * @param array $meta
     * @param array $data
     */
    public function __construct(
        \Magento\Backend\Model\UrlInterface $urlBuilder,
        // @TODO: use repository or not?
        \Magento\Catalog\Model\ResourceModel\Product\CollectionFactory $productCollectionFactory,
        string $name,
        string $primaryFieldName,
        string $requestFieldName,
        \Magento\Framework\Api\Search\ReportingInterface $reporting,
        \Magento\Framework\Api\Search\SearchCriteriaBuilder $searchCriteriaBuilder,
        \Magento\Framework\App\RequestInterface $request,
        \Magento\Framework\Api\FilterBuilder $filterBuilder,
        array $meta = [],
        array $data = []
    ) {
        parent::__construct(
            $name,
            $primaryFieldName,
            $requestFieldName,
            $reporting,
            $searchCriteriaBuilder,
            $request,
            $filterBuilder,
            $meta,
            $data
        );
        $this->urlBuilder = $urlBuilder;
        $this->productCollectionFactory = $productCollectionFactory;
    }

    /**
     * Get listing data
     *
     * @return array
     */
    public function getData(): array
    {
        $data = parent::getData();
        $productCollection = $this->productCollectionFactory->create();
        $productCollection->addAttributeToSelect('name')
            ->addIdFilter(array_column($data['items'], 'product_id'));

        foreach ($data['items'] as &$item) {
            if ($item['product_id']) {
                $item['product_link'] = $this->urlBuilder->getUrl(
                    'catalog/product/edit',
                    ['id' => $item['product_id']]
                );
                /** @var Product $product */
                $product = $productCollection->getItemById($item['product_id']);
                $item['product_name'] = $product->getName();
            } else {
                $item['product_name'] = 'n/a';
            }

            if ($item['customer_id']) {
                $item['customer_link'] = $this->urlBuilder->getUrl(
                    'customer/index/edit',
                    ['id' => $item['customer_id']]
                );
            }
        }

        return $data;
    }
}
