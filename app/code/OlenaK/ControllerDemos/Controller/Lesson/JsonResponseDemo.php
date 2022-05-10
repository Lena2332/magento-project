<?php

declare(strict_types=1);

namespace OlenaK\ControllerDemos\Controller\Lesson;

use Magento\Framework\App\Action\HttpGetActionInterface;
use Magento\Framework\Controller\Result\Json;

class JsonResponseDemo implements HttpGetActionInterface
{
    private \Magento\Framework\App\RequestInterface $request;

    private \Magento\Framework\Controller\Result\JsonFactory $jsonFactory;

    /**
     * @param \Magento\Framework\App\RequestInterface $request
     * @param \Magento\Framework\Controller\Result\JsonFactory $jsonFactory
     */
    public function __construct(
        \Magento\Framework\App\RequestInterface $request,
        \Magento\Framework\Controller\Result\JsonFactory $jsonFactory
    )
    {
        $this->request = $request;
        $this->jsonFactory = $jsonFactory;
    }

    /**
     * @return Json
     */
    public function execute(): Json
    {
        $vendorName = (string) $this->request->getParam('vendor_name');
        $moduleName = (string) $this->request->getParam('module_name');

        return $this->jsonFactory->create()
            ->setData([
                'vendorName' => $vendorName,
                'moduleName' => $moduleName
            ]);
    }
}
