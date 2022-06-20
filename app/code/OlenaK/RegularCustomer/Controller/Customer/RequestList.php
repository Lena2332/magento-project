<?php

declare(strict_types=1);

namespace OlenaK\RegularCustomer\Controller\Customer;

use Magento\Framework\Controller\ResultInterface;

class RequestList implements \Magento\Framework\App\Action\HttpGetActionInterface
{
    private \Magento\Framework\View\Result\PageFactory $pageFactory;

    /**
     * @var \Magento\Customer\Model\Session $customerSession
     */
    private \Magento\Customer\Model\Session $customerSession;

    /**
     * @var \Magento\Framework\Controller\Result\RedirectFactory $redirectFactory
     */
    private \Magento\Framework\Controller\Result\RedirectFactory $redirectFactory;

    /**
     * @var \Magento\Customer\Model\Url $url;
     */
    private \Magento\Customer\Model\Url $url;

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
     * @param \Magento\Customer\Model\Session $customerSession
     * @param \Magento\Framework\Controller\Result\RedirectFactory $redirectFactory
     * @param \Magento\Customer\Model\Url $url
     */
    public function __construct(
        \Magento\Framework\View\Result\PageFactory $pageFactory,
        \Magento\Customer\Model\Session $customerSession,
        \Magento\Framework\Controller\Result\RedirectFactory $redirectFactory,
        \Magento\Customer\Model\Url $url,
        \Magento\Framework\Controller\Result\ForwardFactory $forwardFactory,
        \OlenaK\RegularCustomer\Model\Config $config
    ) {
        $this->pageFactory = $pageFactory;
        $this->customerSession = $customerSession;
        $this->redirectFactory = $redirectFactory;
        $this->url = $url;
        $this->forwardFactory = $forwardFactory;
        $this->config = $config;
    }

    /**
     * Page result demo: https://olena-kupriiets-magento.local/regular-customer/customer/requestlist
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

        if (!$this->customerSession->isLoggedIn()) {
            return $this->redirectFactory->create()->setUrl(
                $this->url->getLoginUrl()
            );
        }

        return $this->pageFactory->create();
    }
}
