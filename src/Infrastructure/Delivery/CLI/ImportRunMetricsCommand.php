<?php

declare(strict_types=1);

namespace App\Infrastructure\Delivery\CLI;

use App\Application\Command\ImportRunMetrics;
use App\Application\Command\ImportRunMetricsHandler;
use App\Model\Jenkins\Pipeline\PipelineName;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Command\LockableTrait;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

/**
 * @author Alexandre Hocquard <alexandre.hocquard@akeneo.com>
 */
class ImportRunMetricsCommand extends Command
{
    use LockableTrait;

    protected static $defaultName = 'akeneo:import:run-metrics';

    /** @var ImportRunMetricsHandler */
    private $handler;

    /** @var PipelineName[] */
    private $pipelineNames;

    /**
     * @param ImportRunMetricsHandler $handler
     * @param PipelineName[]          $pipelineNames
     */
    public function __construct(ImportRunMetricsHandler $handler, array $pipelineNames)
    {
        parent::__construct();

        $this->handler = $handler;
        $this->pipelineNames = array_map(function ($pipelineName) {
            return new PipelineName($pipelineName);
        }, $pipelineNames);
    }

    /**
     * {@inheritdoc}
     */
    protected function configure()
    {
        $this
            ->setDescription('Import CI metrics about builds into InfluxDB database.');
    }


    protected function execute(InputInterface $input, OutputInterface $output)
    {
        if (!$this->lock()) {
            $output->writeln(sprintf('The command "%s" is already running in another process.', self::$defaultName));

            return 0;
        }
        $command = new ImportRunMetrics();
        $command->pipelineNames = $this->pipelineNames;

        $this->handler->handle($command);
    }
}
