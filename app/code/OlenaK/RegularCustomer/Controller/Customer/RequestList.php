<?php

declare(strict_types=1);

namespace OlenaK\RegularCustomer\Controller\Customer;

use Magento\Framework\View\Result\Page;

class RequestList implements \Magento\Framework\App\Action\HttpGetActionInterface
{
    private \Magento\Framework\View\Result\PageFactory $pageFactory;

    /**
     * @param \Magento\Framework\View\Result\PageFactory $pageFactory
     */
    public function __construct(\Magento\Framework\View\Result\PageFactory $pageFactory)
    {
        $this->pageFactory = $pageFactory;
    }

    /**
     * Page result demo: https://olena-kupriiets-magento.local/olenak-cms/lesson/cmscontroller
     *
     * @return Page
     */
    public function execute(): Page
    {
        return $this->pageFactory->create();
    }
}
