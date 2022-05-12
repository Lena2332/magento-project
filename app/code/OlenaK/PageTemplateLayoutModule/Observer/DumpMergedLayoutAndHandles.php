<?php

declare(strict_types=1);

namespace OlenaK\PageTemplateLayoutModule\Observer;

use Magento\Framework\App\Filesystem\DirectoryList;
use Magento\Framework\Event\Observer;
use Magento\Framework\View\Layout;

class DumpMergedLayoutAndHandles implements \Magento\Framework\Event\ObserverInterface
{
    private \Magento\Framework\UrlInterface $url;

    private \Magento\Framework\App\Filesystem\DirectoryList $dir;

    /**
     * @param \Magento\Framework\UrlInterface $url
     * @param \Magento\Framework\App\Filesystem\DirectoryList $dir
     */
    public function __construct(
        \Magento\Framework\UrlInterface $url,
        \Magento\Framework\App\Filesystem\DirectoryList $dir
    ) {
        $this->url = $url;
        $this->dir = $dir;
    }

    /**
     * @param Observer $observer
     * @return void
     * @throws \Magento\Framework\Exception\FileSystemException
     */
    public function execute(Observer $observer): void
    {
        /** @var Layout $layout */
        $layout = $observer->getEvent()->getData('layout');
        $logsDir = $this->dir->getPath(DirectoryList::LOG) . DIRECTORY_SEPARATOR;
        // Get page layout handles
        $layoutHandles = ['Current page URL: ' . $this->url->getCurrentUrl()];

        foreach ($layout->getUpdate()->getHandles() as $handle) {
            $layoutHandles[] = '- ' . $handle;
        }

        // ONLY FOR DEBUG! Use \Magento\Framework\Filesystem\Directory\Write::writeFile() instead!
        file_put_contents(
            $logsDir . 'layout_handles.log',
            implode("\n", $layoutHandles) . "\n\n",
            FILE_APPEND
        );

        // Get merged page layout
        // ONLY FOR DEBUG! Use \Magento\Framework\Filesystem\Directory\Write::writeFile() instead!
        file_put_contents(
            $logsDir . 'layout_merged.xml',
            $layout->getXmlString() . "\n\n"
        );
    }
}
