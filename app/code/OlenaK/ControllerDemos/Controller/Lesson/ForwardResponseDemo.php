<?php

declare(strict_types=1);

namespace OlenaK\ControllerDemos\Controller\Lesson;

use Magento\Framework\App\Action\HttpGetActionInterface;
use Magento\Framework\Controller\Result\Forward;

class ForwardResponseDemo implements HttpGetActionInterface
{
    private \Magento\Framework\Controller\Result\ForwardFactory $forwardFactory;

    /**
     * @param \Magento\Framework\Controller\Result\ForwardFactory $forwardFactory
     */
    public function __construct(
        \Magento\Framework\Controller\Result\ForwardFactory $forwardFactory
    )
    {
        $this->forwardFactory = $forwardFactory;
    }

    /**
     * @return Forward
     */
    public function execute(): Forward
    {
        return $this->forwardFactory->create()
            ->setParams([
                'vendor_name' => 'OlenaK',
                'module_name' => 'ControllerDemos',
                ])
            ->forward('jsonresponsedemo');
    }
}
