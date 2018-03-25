<?php

declare(strict_types=1);

namespace App\Application;

use App\Model\Jenkins\Pipeline\PipelineRepository;
use App\Model\Jenkins\Run\RunRepository;

/**
 * @author    Alexandre Hocquard <alexandre.hocquard@akeneo.com>
 * @copyright 2018 Akeneo SAS (http://www.akeneo.com)
 * @license   http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */
class ImportContinuousIntegrationMetricsHandler
{
    /** @var PipelineRepository */
    private $pipelineRepository;

    /** @var RunRepository */
    private $saveableRunRepository;

    public function __construct(
        PipelineRepository $pipelineRepository,
        RunRepository $runRepository
    ) {
        $this->saveableRunRepository = $runRepository;
        $this->pipelineRepository = $pipelineRepository;
    }

    public function handle(ImportContinuousIntegrationMetrics $command): void
    {
        foreach ($command->pipelineNames as $pipelineName) {
            $runsToImport = [];
            $pipeline = $this->pipelineRepository->getPipeline($pipelineName);

            foreach ($pipeline->branches() as $branch) {
                foreach ($branch->runs() as $run) {
                    if ($run->isRunFinished() && !$this->saveableRunRepository->hasRun($run)) {
                        $runsToImport[] = $run;
                    }
                }

            }

            $this->saveableRunRepository->saveRuns($runsToImport);
        }
    }
}
