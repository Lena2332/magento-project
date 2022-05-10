<?php

declare(strict_types=1);

namespace OlenaK\ControllerDemos\Controller\Lesson;

use Magento\Framework\App\Action\HttpGetActionInterface;
use Magento\Framework\Controller\Result\Redirect;

class RedirectResponseDemo implements HttpGetActionInterface
{
    private \Magento\Framework\Controller\Result\RedirectFactory $redirectFactory;

    /**
     * @param \Magento\Framework\Controller\Result\RedirectFactory $redirectFactory
     */
    public function __construct(
        \Magento\Framework\Controller\Result\RedirectFactory $redirectFactory
    )
    {
        $this->redirectFactory = $redirectFactory;
    }

    /**
     * @return Redirect
     */
    public function execute(): Redirect
    {
        $resultRedirect = $this->redirectFactory->create();
        $resultRedirect->setUrl('https://github.com/Lena2332');
        return $resultRedirect;
    }
}
