<?php

declare(strict_types=1);

namespace OlenaK\RegularCustomer\Ui\Component\DiscountRequest\Form\Button;

use OlenaK\RegularCustomer\Model\Authorization;

class Delete implements \Magento\Framework\View\Element\UiComponent\Control\ButtonProviderInterface
{
    /**
     * @var \Magento\Backend\Model\UrlInterface $urlBuilder
     */
    private \Magento\Backend\Model\UrlInterface $urlBuilder;

    /**
     * @var \Magento\Framework\App\RequestInterface $request
     */
    private \Magento\Framework\App\RequestInterface $request;

    /**
     * @var \Magento\Framework\Escaper $escaper
     */
    private \Magento\Framework\Escaper $escaper;

    /**
     * Delete constructor.
     * @param \Magento\Backend\Model\UrlInterface $urlBuilder
     * @param \Magento\Framework\App\RequestInterface $request
     * @param \Magento\Framework\Escaper $escaper
     */
    public function __construct(
        \Magento\Backend\Model\UrlInterface $urlBuilder,
        \Magento\Framework\App\RequestInterface $request,
        \Magento\Framework\Escaper $escaper
    ) {
        $this->urlBuilder = $urlBuilder;
        $this->request = $request;
        $this->escaper = $escaper;
    }

    /**
     * Get button configuration
     *
     * @return array
     */
    public function getButtonData(): array
    {
        $url = $this->urlBuilder->getUrl('*/*/delete');
        $message = $this->escaper->escapeJs(
            $this->escaper->escapeHtml(__('Are you sure you want to delete this discount request?'))
        );
        $discountRequestId = (int) $this->request->getParam('request_id');
        $buttonConfiguration = [];

        if ($discountRequestId) {
            $buttonConfiguration = [
                'label' => __('Delete'),
                'class' => 'delete primary',
                'on_click' => "deleteConfirm('$message', '$url', {data:{request_id:$discountRequestId}})",
                'data_attribute' => [
                    'mage_init' => [
                        'button' => [
                            'event' => 'delete'
                        ],
                    ],
                    'form-role' => 'delete'
                ],
                'aclResource' => Authorization::ACTION_DISCOUNT_REQUEST_DELETE,
                'sort_order' => 20
            ];
        }

        return $buttonConfiguration;
    }
}
