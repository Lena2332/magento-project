<?php

declare(strict_types=1);

namespace OlenaK\RegularCustomer\Console\Command;

use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Magento\Framework\Console\Cli;
use Magento\Framework\App\Area;

class AreaEmulationDemo extends \Symfony\Component\Console\Command\Command
{
    private \Magento\Framework\App\State $state;

    private \Psr\Log\LoggerInterface $logger;

    /**
     * RefreshStatistics constructor.
     * @param \Magento\Framework\App\State $state
     * @param \Psr\Log\LoggerInterface $logger
     * @param string|null $name
     */
    public function __construct(
        \Magento\Framework\App\State $state,
        \Psr\Log\LoggerInterface $logger,
        string $name = null
    ) {
        parent::__construct($name);
        $this->state = $state;
        $this->logger = $logger;
    }

    /**
     * @inheritDoc
     */
    protected function configure(): void
    {
        $this->setName('olenak:regular-customer:area-emulation-demo')
            ->setDescription('{OlenaK} Area Emulation Demo')
            ->setHelp(<<<'EOF'
                Area emulation demo command.
                Command: <info>%command.full_name%</info>
                EOF);
        parent::configure();
    }

    /**
     * @param InputInterface $input
     * @param OutputInterface $output
     * @return int
     */
    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        try {
            $this->state->emulateAreaCode(
                Area::AREA_ADMINHTML,
                \Closure::fromCallable([$this, 'emulateArea']),
                [$output]
            );
        } catch (\Exception $e) {
            $this->logger->critical($e->getMessage());

            return Cli::RETURN_FAILURE;
        }

        return Cli::RETURN_SUCCESS;
    }

    /**
     * @param OutputInterface $output
     */
    private function emulateArea(OutputInterface $output): void
    {
        $areaCode = $this->state->getAreaCode();
        $output->writeln("<info>Emulating area: $areaCode</info>");
    }
}
