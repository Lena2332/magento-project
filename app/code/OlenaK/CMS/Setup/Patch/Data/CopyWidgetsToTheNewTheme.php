<?php

declare(strict_types=1);

namespace OlenaK\CMS\Setup\Patch\Data;

use Magento\Widget\Model\ResourceModel\Widget\Instance\Collection as WidgetCollection;
use Magento\Widget\Model\Widget\Instance as WidgetModel;
use Magento\Widget\Model\WidgetFactory;

class CopyWidgetsToTheNewTheme implements \Magento\Framework\Setup\Patch\DataPatchInterface
{
    /**
     * @var \Magento\Widget\Model\ResourceModel\Widget\Instance\CollectionFactory $widgetCollectionFactory
     */
    private \Magento\Widget\Model\ResourceModel\Widget\Instance\CollectionFactory $widgetCollectionFactory;

    /**
     * @var \Magento\Framework\App\State $appState
     */
    private \Magento\Framework\App\State $appState;

    /**
     * @param \Magento\Widget\Model\ResourceModel\Widget\Instance\CollectionFactory $widgetCollectionFactory
     * @param \Magento\Framework\App\State $appState
     */
    public function __construct(
        \Magento\Widget\Model\ResourceModel\Widget\Instance\CollectionFactory $widgetCollectionFactory,
        \Magento\Framework\App\State $appState
    ) {
        $this->widgetCollectionFactory = $widgetCollectionFactory;
        $this->appState = $appState;
    }

    /**
     * @inheritDoc
     * @throws \Exception
     */
    public function apply(): void
    {
        $this->appState->emulateAreaCode(
            'frontend',
            \Closure::fromCallable([$this, 'copyWidgets'])
        );
    }

    /**
     * Copy widgets
     *
     * @return void
     * @throws \Magento\Framework\Exception\AlreadyExistsException
     */
    private function copyWidgets(): void
    {
        /** @var WidgetCollection $widgetCollection */
        $widgetCollection = $this->widgetCollectionFactory->create();
        $widgetResource = $widgetCollection->getResource();

        /** @var WidgetModel $widget */
        foreach ($widgetCollection as $widget) {
            $widgetResource->load($widget, $widget->getId());
            $widget->unsetData('instance_id')
                // Get theme ID in the `theme` table
                ->setThemeId(4);
            $preparedPageGroups = [];

            foreach ($widget->getData('page_groups') as $pageGroup) {
                $preparedPageGroups[] = [
                    'page_group' => $pageGroup['page_group'],
                    $pageGroup['page_group'] => [
                        'page_id' => $pageGroup['page_id'],
                        'group' => $pageGroup['page_group'],
                        'layout_handle' => $pageGroup['layout_handle'],
                        'for' => $pageGroup['page_for'],
                        'block' => $pageGroup['block_reference'],
                        'entities' => $pageGroup['entities'],
                        'template' => $pageGroup['page_template']
                    ]
                ];
            }

            $widget->setData('page_groups', $preparedPageGroups);
            $widgetResource->save($widget);
        }
    }

    /**
     * @inheritDoc
     */
    public static function getDependencies(): array
    {
        return [];
    }

    /**
     * @inheritDoc
     */
    public function getAliases(): array
    {
        return [];
    }
}
