<?php

declare(strict_types=1);

namespace OlenaK\CMS\Setup;

use Magento\Cms\Api\Data\BlockSearchResultsInterface;
use Magento\Cms\Api\Data\PageSearchResultsInterface;
use Magento\Cms\Model\Block;
use Magento\Cms\Model\BlockRepository;
use Magento\Cms\Model\Page;
use Magento\Cms\Model\PageRepository;
use Magento\Framework\Setup\InstallDataInterface;
use Magento\Framework\Setup\ModuleContextInterface;
use Magento\Framework\Setup\ModuleDataSetupInterface;
use Magento\Framework\Module\Dir;

/**
 * Set `'stores' => [0]` - assign page/block to All Stores. If there are store-specific pages/blocks - remove them
 * Set `'stores' => [1, 2]`:
 * - if there is a page/block exactly for these stores - update it;
 * - if there is a page/block for stores [1, 2, 3] - split them into 3 blocks;
 * - if there is a page/block for stores [1, 3] and a page/block for store [2] - split them into 3 blocks;
 * - if there is a page/block for store [0] (All Stores) - copy to other stores, create a new entity for stores [1, 2];
 * Algorithm will not merge identical pages/blocks together in case of rearranging them. You can implement this.
 */
class RecurringData implements InstallDataInterface
{
    /**
     * Entity type and directory with files must match
     */
    private const ENTITY_TYPE_PAGE = 'page';
    private const ENTITY_TYPE_BLOCK = 'block';

    private const MODULE_NAME = 'OlenaK_CMS';

    /**
     * @var array $content
     */
    private static array $content = [
        self::ENTITY_TYPE_PAGE => [
            [
                'identifier' => 'about-us',
                'content' => 'about-us-1.html',
                'title' => 'Про нас',
                'url_key' => 'about-us',
                'content_heading' => 'Про нас',
                'stores' => [1],
                'page_layout' => '1column',
                'sort_order' => 0
            ], [
                'identifier' => 'about-us',
                'content' => 'about-us-2.html',
                'title' => 'About Us',
                'url_key' => 'about-us',
                'content_heading' => 'About Us',
                'stores' => [2],
                'page_layout' => '1column',
                'sort_order' => 0
            ]
        ],
        self::ENTITY_TYPE_BLOCK => [

        ]
    ];

    /**
     * @var \Magento\Framework\Module\Dir\Reader $moduleReader
     */
    private \Magento\Framework\Module\Dir\Reader $moduleReader;

    /**
     * @var \Magento\Framework\Filesystem\Driver\File $fileDriver
     */
    private \Magento\Framework\Filesystem\Driver\File $fileDriver;

    /**
     * @var \Magento\Cms\Model\PageRepository $pageRepository
     */
    private \Magento\Cms\Model\PageRepository $pageRepository;

    /**
     * @var \Magento\Cms\Model\PageFactory $pageFactory
     */
    private \Magento\Cms\Model\PageFactory $pageFactory;

    /**
     * @var \Magento\Cms\Model\BlockRepository $blockRepository
     */
    private \Magento\Cms\Model\BlockRepository $blockRepository;

    /**
     * @var \Magento\Cms\Model\BlockFactory $blockFactory
     */
    private \Magento\Cms\Model\BlockFactory $blockFactory;

    /**
     * @var \Magento\Framework\Api\SearchCriteriaBuilder $searchCriteriaBuilder
     */
    private \Magento\Framework\Api\SearchCriteriaBuilder $searchCriteriaBuilder;

    /**
     * @var \Magento\Store\Model\StoreManagerInterface $storeManager
     */
    private \Magento\Store\Model\StoreManagerInterface $storeManager;

    /**
     * RecurringData constructor.
     * @param Dir\Reader $moduleReader
     * @param \Magento\Framework\Filesystem\Driver\File $fileDriver
     * @param PageRepository $pageRepository
     * @param \Magento\Cms\Model\PageFactory $pageFactory
     * @param BlockRepository $blockRepository
     * @param \Magento\Cms\Model\BlockFactory $blockFactory
     * @param \Magento\Framework\Api\SearchCriteriaBuilder $searchCriteriaBuilder
     * @param \Magento\Store\Model\StoreManagerInterface $storeManager
     */
    public function __construct(
        \Magento\Framework\Module\Dir\Reader $moduleReader,
        \Magento\Framework\Filesystem\Driver\File $fileDriver,
        \Magento\Cms\Model\PageRepository $pageRepository,
        \Magento\Cms\Model\PageFactory $pageFactory,
        \Magento\Cms\Model\BlockRepository $blockRepository,
        \Magento\Cms\Model\BlockFactory $blockFactory,
        \Magento\Framework\Api\SearchCriteriaBuilder $searchCriteriaBuilder,
        \Magento\Store\Model\StoreManagerInterface $storeManager
    ) {
        $this->moduleReader = $moduleReader;
        $this->fileDriver = $fileDriver;
        $this->pageRepository = $pageRepository;
        $this->pageFactory = $pageFactory;
        $this->blockRepository = $blockRepository;
        $this->blockFactory = $blockFactory;
        $this->searchCriteriaBuilder = $searchCriteriaBuilder;
        $this->storeManager = $storeManager;
    }

    /**
     * Install Pages and Blocks
     *
     * @param ModuleDataSetupInterface $setup
     * @param ModuleContextInterface $context
     * @throws \Magento\Framework\Exception\CouldNotSaveException
     * @throws \Magento\Framework\Exception\FileSystemException
     */
    public function install(ModuleDataSetupInterface $setup, ModuleContextInterface $context): void
    {
        // Do not run the patch during data migration phase
        if (getenv('MIGRATION_MODE')) {
            return;
        }

        $this->extractAndInstall(self::ENTITY_TYPE_PAGE);
        $this->extractAndInstall(self::ENTITY_TYPE_BLOCK);
    }

    /**
     * Get content from files and update entities
     *
     * @param string $entityType
     * @throws \Magento\Framework\Exception\CouldNotSaveException
     * @throws \Magento\Framework\Exception\FileSystemException
     */
    private function extractAndInstall(string $entityType): void
    {
        foreach (self::$content[$entityType] as $entityData) {
            $entityData['content'] = $this->extractContent($entityType, $entityData['content']);
            $this->update($entityType, $entityData, $entityData['stores']);
        }
    }

    /**
     * Get content from files
     *
     * @param string $entityType
     * @param string $fileName
     * @return string
     * @throws \Magento\Framework\Exception\FileSystemException
     */
    private function extractContent(string $entityType, string $fileName): string
    {
        $filePath = $this->moduleReader->getModuleDir(Dir::MODULE_SETUP_DIR, self::MODULE_NAME)
            . DIRECTORY_SEPARATOR . $entityType . DIRECTORY_SEPARATOR . $fileName;
        $this->fileDriver->isFile($filePath);

        return $this->fileDriver->fileGetContents($filePath, 'rb');
    }

    /**
     * Update PAge or Block
     *
     * @param string $entityType
     * @param array $entityData
     * @param array $storeIds
     * @return void
     * @throws \Magento\Framework\Exception\CouldNotSaveException
     */
    private function update(string $entityType, array $entityData, array $storeIds): void
    {
        switch ($entityType) {
            case self::ENTITY_TYPE_PAGE:
                $repository = $this->pageRepository;
                $entityFactory = $this->pageFactory;
                break;
            case self::ENTITY_TYPE_BLOCK:
                $repository = $this->blockRepository;
                $entityFactory = $this->blockFactory;
                break;
            default:
                throw new \InvalidArgumentException("Unknown CMS entity type: $entityType");
        }

        $allStoreIds = array_keys($this->storeManager->getStores());
        $searchResult = $this->getSearchResult($repository, $entityData['identifier'], $storeIds);

        // If 0 entities found then create a new page or block
        if (!$searchResult->getTotalCount()) {
            $entity = $entityFactory->create();
            $entity->addData($entityData);
            $repository->save($entity);

            return;
        }

        // Copy page/block to every store if needed
        foreach ($searchResult->getItems() as $searchItem) {
            $searchItemStoreIds = array_map('intval', $searchItem->getStoreId());

            // If both are [0] or both are [1, 2, 3]
            if (!array_diff($storeIds, $searchItemStoreIds) && !array_diff($searchItemStoreIds, $storeIds)) {
                $searchItem->addData($entityData);
                $repository->save($searchItem);

                continue;
            }

            if ($searchItemStoreIds[0] === 0) {
                $searchItemStoreIds = $allStoreIds;
            }

            // Copy page/block to all stores except the those where the content should be updated
            if (count($searchItemStoreIds) > 1) {
                $this->split($repository, $searchItem, $searchItemStoreIds);
            }
        }

        // Update individual pages/blocks after they've been split
        $searchResult = $this->getSearchResult($repository, $entityData['identifier'], $storeIds);
        $processedStoreIds = [];
        unset($entityData['stores']);

        /** @var Page|Block $searchItem */
        foreach ($searchResult->getItems() as $searchItem) {
            $searchItem->addData($entityData);
            $repository->save($searchItem);
            $processedStoreIds[] = (int) $searchItem->getStoreId()[0];
        }

        // Save new pages for the store
        foreach (array_diff($storeIds, array_unique($processedStoreIds)) as $storeId) {
            $entity = $entityFactory->create();
            $entity->addData($entityData);
            $entity->setStoreId($storeId);
            $repository->save($entity);
        }
    }

    /**
     * Find page or block by identifier
     *
     * @param PageRepository|BlockRepository $repository
     * @param string $identifier
     * @param array $storeIds
     * @return PageSearchResultsInterface|BlockSearchResultsInterface
     */
    private function getSearchResult($repository, string $identifier, array $storeIds): object
    {
        $this->searchCriteriaBuilder->addFilter('identifier', $identifier);

        if ($storeIds[0] !== 0) {
            $this->searchCriteriaBuilder->addFilter('store_id', array_merge([[0], $storeIds]), 'in');
        }

        return $repository->getList($this->searchCriteriaBuilder->create());
    }

    /**
     * Split single page or block into multiple for different stores
     *
     * @param PageRepository|BlockRepository $repository
     * @param Page|Block $entity
     * @param array $storeIds
     * @return void
     * @throws \Magento\Framework\Exception\CouldNotSaveException
     */
    private function split($repository, $entity, array $storeIds): void
    {
        $firstStoreId = array_shift($storeIds);
        $entity->setStoreId($firstStoreId);
        $repository->save($entity);

        foreach ($storeIds as $storeId) {
            $entity = clone $entity;
            $entity->setId(null);
            $entity->setStoreId($storeId);
            $repository->save($entity);
        }
    }
}
