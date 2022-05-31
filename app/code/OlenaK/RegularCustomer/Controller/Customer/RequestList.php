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
     * @param \Magento\Framework\View\Result\PageFactory $pageFactory
     * @param \Magento\Customer\Model\Session $customerSession
     * @param \Magento\Framework\Controller\Result\RedirectFactory $redirectFactory
     * @param \Magento\Customer\Model\Url $url
     */
    public function __construct(
        \Magento\Framework\View\Result\PageFactory $pageFactory,
        \Magento\Customer\Model\Session $customerSession,
        \Magento\Framework\Controller\Result\RedirectFactory $redirectFactory,
        \Magento\Customer\Model\Url $url
    ) {
        $this->pageFactory = $pageFactory;
        $this->customerSession = $customerSession;
        $this->redirectFactory = $redirectFactory;
        $this->url = $url;
    }

    /**
     * Page result demo: https://olena-kupriiets-magento.local/regular-customer/customer/requestlist
     *
     * @return ResultInterface
     */
    public function execute(): ResultInterface
    {
        if (!$this->customerSession->isLoggedIn()) {
            return $this->redirectFactory->create()->setUrl(
                $this->url->getLoginUrl()
            );
        }

        return $this->pageFactory->create();
    }
}
