<?php

declare(strict_types=1);

namespace App\Infrastructure\Delivery\CLI;

use App\Application\Command\ImportRunMetrics;
use App\Application\Command\ImportRunMetricsHandler;
use App\Application\Command\ImportTestMetrics;
use App\Application\Command\ImportTestMetricsHandler;
use App\Model\Jenkins\Pipeline\PipelineName;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Command\LockableTrait;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

/**
 * @author    Alexandre Hocquard <alexandre.hocquard@akeneo.com>
 * @copyright 2018 Akeneo SAS (http://www.akeneo.com)
 * @license   http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */
class ImportTestMetricsCommand extends Command
{
    use LockableTrait;

    protected static $defaultName = 'akeneo:import:test-metrics';

    /** @var ImportRunMetricsHandler */
    private $handler;

    /** @var PipelineName[] */
    private $pipelineNames;

    /**
     * @param ImportTestMetricsHandler $handler
     * @param PipelineName[]           $pipelineNames
     */
    public function __construct(ImportTestMetricsHandler $handler, array $pipelineNames)
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
        $command = new ImportTestMetrics();
        $command->pipelineNames = $this->pipelineNames;

        $this->handler->handle($command);
    }
}
