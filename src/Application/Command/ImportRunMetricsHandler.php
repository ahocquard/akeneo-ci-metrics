<?php

declare(strict_types=1);

namespace App\Application\Command;

use App\Model\Jenkins\Pipeline\GettablePipelineRepository;
use App\Model\Jenkins\Run\ListableRunRepository;
use App\Model\Jenkins\Run\RunRepository;
use Psr\Log\LoggerInterface;

/**
 * @author    Alexandre Hocquard <alexandre.hocquard@akeneo.com>
 * @copyright 2018 Akeneo SAS (http://www.akeneo.com)
 * @license   http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */
class ImportRunMetricsHandler
{
    /** @var LoggerInterface */
    private $logger;

    /** @var GettablePipelineRepository */
    private $pipelineRepository;

    /** @var ListableRunRepository */
    private $listableRunRepository;

    /** @var RunRepository */
    private $saveableRunRepository;

    public function __construct(
        LoggerInterface $logger,
        GettablePipelineRepository $pipelineRepository,
        ListableRunRepository $listableRunRepository,
        RunRepository $saveableRunRepository
    ) {
        $this->logger = $logger;
        $this->pipelineRepository = $pipelineRepository;
        $this->listableRunRepository = $listableRunRepository;
        $this->saveableRunRepository = $saveableRunRepository;
    }

    public function handle(ImportRunMetrics $command): void
    {
        $this->logger->info('Starting to import run metrics.');
        foreach ($command->pipelineNames as $pipelineName) {
            $pipeline = $this->pipelineRepository->getPipeline($pipelineName);

            foreach ($pipeline->branches() as $branch) {

                $runsToImport = [];
                $runs = $this->listableRunRepository->listRunsFrom($branch);
                $this->logger->debug(sprintf('List runs from branch "%s".', $branch->name()->value()));
                foreach ($runs as $run) {
                    if ($run->isRunFinished() && !$this->saveableRunRepository->hasRun($run)) {
                        $runsToImport[] = $run;
                    }
                }

                $this->saveableRunRepository->saveRuns($runsToImport);
            }

        }
    }
}
