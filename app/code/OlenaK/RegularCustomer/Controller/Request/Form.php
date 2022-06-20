<?php

declare(strict_types=1);

namespace OlenaK\RegularCustomer\Controller\Request;

use Magento\Framework\App\Action\HttpGetActionInterface;
use Magento\Framework\Controller\ResultInterface;

class Form implements HttpGetActionInterface
{
    /**
     * @var \Magento\Framework\View\Result\PageFactory $pageFactory
     */
    private \Magento\Framework\View\Result\PageFactory $pageFactory;

    /**
     * @var \Magento\Framework\Controller\Result\ForwardFactory $forwardFactory
     */
    private \Magento\Framework\Controller\Result\ForwardFactory $forwardFactory;

    /**
     * @var \OlenaK\RegularCustomer\Model\Config $config
     */
    private \OlenaK\RegularCustomer\Model\Config $config;


    /**
     * @param \Magento\Framework\View\Result\PageFactory $pageFactory
     * @param \Magento\Framework\Controller\Result\ForwardFactory $forwardFactory
     * @param \OlenaK\RegularCustomer\Model\Config $config
     */
    public function __construct(
        \Magento\Framework\View\Result\PageFactory $pageFactory,
        \Magento\Framework\Controller\Result\ForwardFactory $forwardFactory,
        \OlenaK\RegularCustomer\Model\Config $config
    ) {
        $this->pageFactory = $pageFactory;
        $this->forwardFactory = $forwardFactory;
        $this->config = $config;
    }

    /**
     * Page result demo: https://olena-kupriiets-magento.local/olenak-cms/lesson/cmscontroller
     *
     * @return ResultInterface
     */
    public function execute(): ResultInterface
    {
        if (!$this->config->enabled()) {
            return $this->forwardFactory->create()
                ->setController('index')
                ->forward('defaultNoRoute');
        }

        return $this->pageFactory->create();
    }
}
