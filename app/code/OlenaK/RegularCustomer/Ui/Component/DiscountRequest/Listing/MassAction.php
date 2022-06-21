<?php

declare(strict_types=1);

namespace OlenaK\RegularCustomer\Ui\Component\DiscountRequest\Listing;

use OlenaK\RegularCustomer\Model\Authorization;
use Magento\Framework\View\Element\UiComponentInterface;

/**
 * Based on \Magento\Catalog\Ui\Component\Product\MassAction
 */
class MassAction extends \Magento\Ui\Component\Action
{
    /**
     * @var \OlenaK\RegularCustomer\Model\Authorization $authorization
     */
    private \OlenaK\RegularCustomer\Model\Authorization $authorization;

    /**
     * Constructor
     *
     * @param \OlenaK\RegularCustomer\Model\Authorization $authorization
     * @param \Magento\Framework\View\Element\UiComponent\ContextInterface $context
     * @param UiComponentInterface[] $components
     * @param array $data
     */
    public function __construct(
        \OlenaK\RegularCustomer\Model\Authorization $authorization,
        \Magento\Framework\View\Element\UiComponent\ContextInterface $context,
        array $components = [],
        array $data = []
    ) {
        parent::__construct($context, $components, $data);
        $this->authorization = $authorization;
    }

    /**
     * @inheritdoc
     */
    public function prepare(): void
    {
        $config = $this->getConfiguration();

        foreach ($this->getChildComponents() as $actionComponent) {
            $actionType = $actionComponent->getConfiguration()['type'];

            switch ($actionType) {
                case 'edit':
                    $alcResource = Authorization::ACTION_DISCOUNT_REQUEST_EDIT;
                    break;
                case 'delete':
                    $alcResource = Authorization::ACTION_DISCOUNT_REQUEST_DELETE;
                    break;
                default:
                    throw new \InvalidArgumentException("Unknown action type: $actionType");
            }

            if ($this->authorization->isAllowed($alcResource)) {
                // phpcs:ignore Magento2.Performance.ForeachArrayMerge
                $config['actions'][] = array_merge($actionComponent->getConfiguration());
            }
        }

        $origConfig = $this->getConfiguration();

        // If configs are equal this means no mass actions were added
        if ($origConfig === $config) {
            $config['componentDisabled'] = true;
        } else {
            $config = array_replace_recursive($config, $origConfig);
        }

        $this->setData('config', $config);
        $this->components = [];

        parent::prepare();
    }
}
