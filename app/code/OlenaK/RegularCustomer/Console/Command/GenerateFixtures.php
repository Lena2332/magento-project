<?php

declare(strict_types=1);

namespace OlenaK\RegularCustomer\Console\Command;

use OlenaK\RegularCustomer\Model\DiscountRequest;
use Magento\Framework\Console\Cli;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;
use Magento\Framework\Data\Collection\AbstractDb;
use Magento\Framework\DB\Select;

class GenerateFixtures extends \Symfony\Component\Console\Command\Command
{
    /**
     * @var \Magento\Catalog\Model\ResourceModel\Product\CollectionFactory $productCollectionFactory
     */
    private \Magento\Catalog\Model\ResourceModel\Product\CollectionFactory $productCollectionFactory;

    /**
     * @var \Magento\Customer\Model\ResourceModel\Customer\CollectionFactory $customerCollectionFactory
     */
    private \Magento\Customer\Model\ResourceModel\Customer\CollectionFactory $customerCollectionFactory;

    /**
     * @var \OlenaK\RegularCustomer\Model\DiscountRequestFactory $discountRequestFactory
     */
    private \OlenaK\RegularCustomer\Model\DiscountRequestFactory $discountRequestFactory;

    /**
     * @var \Magento\Framework\DB\TransactionFactory $transactionFactory
     */
    private \Magento\Framework\DB\TransactionFactory $transactionFactory;

    /**
     * @var  \Magento\Store\Model\StoreManager $storeManager
     */
    private  \Magento\Store\Model\StoreManager $storeManager;

    private array $idsByCollection = [];

    /**
     * GenerateFixtures constructor.
     * @param \Magento\Catalog\Model\ResourceModel\Product\CollectionFactory $productCollectionFactory
     * @param \Magento\Customer\Model\ResourceModel\Customer\CollectionFactory $customerCollectionFactory
     * @param \OlenaK\RegularCustomer\Model\DiscountRequestFactory $discountRequestFactory
     * @param \Magento\Framework\DB\TransactionFactory $transactionFactory
     * @param \Magento\Store\Model\StoreManager $storeManager
     * @param string|null $name
     */
    public function __construct(
        \Magento\Catalog\Model\ResourceModel\Product\CollectionFactory $productCollectionFactory,
        \Magento\Customer\Model\ResourceModel\Customer\CollectionFactory $customerCollectionFactory,
        \OlenaK\RegularCustomer\Model\DiscountRequestFactory $discountRequestFactory,
        \Magento\Framework\DB\TransactionFactory $transactionFactory,
        \Magento\Store\Model\StoreManager $storeManager,
        string $name = null
    ) {
        parent::__construct($name);
        $this->productCollectionFactory = $productCollectionFactory;
        $this->customerCollectionFactory = $customerCollectionFactory;
        $this->discountRequestFactory = $discountRequestFactory;
        $this->storeManager = $storeManager;
        $this->transactionFactory = $transactionFactory;
    }

    /**
     * @inheritDoc
     */
    protected function configure(): void
    {
        $this->setName('olenak:regular-customer:generate-fixtures')
            ->setDescription('{OlenaK} Generate Fixtures')
            ->addOption(
                'amount-per-user',
                'a',
                InputOption::VALUE_OPTIONAL,
                'Amount of requests per user and requests without user. Random product IDs of the visible products are used.',
                10
            )
            ->setHelp(<<<'EOF'
                Generate fixtures (test data) for the module testing.
                Command: <info>%command.full_name% -n=100</info>
                EOF);
        parent::configure();
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $amount = (int) $input->getOption('amount-per-user');
        $customerIds = $this->getIdsFromCollection($this->customerCollectionFactory->create());
        // Create requests for the guest customer as well
        $customerIds[] = null;

        foreach ($customerIds as $customerId) {
            $this->generateRequests($customerId, $amount);
        }

        $output->writeln('<info>Generated!</info>');

        return Cli::RETURN_SUCCESS;
    }

    /**
     * @param AbstractDb $collection
     * @return int[]
     */
    private function getIdsFromCollection(AbstractDb $collection): array
    {
        $collectionClass = get_class($collection);

        if (!isset($this->idsByCollection[$collectionClass])) {
            $select = $collection->getSelect();
            $select->reset(Select::COLUMNS)
                ->columns($collection->getIdFieldName());
            $this->idsByCollection[$collectionClass]
                = array_map('intval', $collection->getConnection()->fetchCol($select));
        }

        return $this->idsByCollection[$collectionClass];
    }

    /**
     * @param int|null $customerId
     * @param int $amountPerCustomer
     * @throws \Exception
     */
    private function generateRequests(?int $customerId, int $amountPerCustomer): void
    {
        $productIds = $this->getIdsFromCollection($this->productCollectionFactory->create());
        $productIdsRandomKeys = array_rand($productIds, $amountPerCustomer);

        static $statuses = [
            DiscountRequest::STATUS_PENDING,
            DiscountRequest::STATUS_APPROVED,
            DiscountRequest::STATUS_DECLINED
        ];
        $transaction = $this->transactionFactory->create();

        $storesIds = array_keys($this->storeManager->getWebsites());

        foreach ($productIdsRandomKeys as $productIdsRandomKey) {
            /** @var DiscountRequest $discountRequest */
            $discountRequest = $this->discountRequestFactory->create();

            // Generate random dada for the last 7 days for statusChangedAt
            $dateNow = strtotime(date("Y-m-d h:i:s"));
            $datePast = strtotime('-7 day', $dateNow);
            $statusUpdatedAt = date('Y-m-d h:i:s', random_int($datePast, $dateNow));

            $randomWebsiteId = (!empty($storesIds)) ? (int) $storesIds[array_rand($storesIds)] : 1;

            $discountRequest->setStoreId($randomWebsiteId)
                ->setProductId($productIds[$productIdsRandomKey])
                ->setCustomerId($customerId)
                ->setEmail($customerId ? null : 'john-doe@example.com')
                ->setName($customerId ? null : 'John Doe')
                ->setStatus($statuses[array_rand($statuses)])
                ->setStatusChangedAt($statusUpdatedAt);
            $transaction->addObject($discountRequest);
        }

        $transaction->save();
    }
}