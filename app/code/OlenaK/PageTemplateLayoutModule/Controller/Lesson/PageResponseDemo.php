<?php

declare(strict_types=1);

namespace OlenaK\PageTemplateLayoutModule\Controller\Lesson;

use Magento\Framework\View\Result\Page;
use Magento\Framework\App\Action\HttpGetActionInterface;

class PageResponseDemo implements HttpGetActionInterface
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
     * Page result demo: https://olena-kupriiets-magento.local/olenak-page-tpl/lesson/pageresponsedemo
     *
     * @return Page
     */
    public function execute(): Page
    {
        return $this->pageFactory->create();
    }
}
