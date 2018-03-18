<?php

declare(strict_types=1);

namespace App\Application;

use App\Model\Jenkins\Job\Job;
use App\Model\Jenkins\Run\ListableRunRepository;
use App\Model\Jenkins\Run\SaveableRunRepository;

/**
 * @author    Alexandre Hocquard <alexandre.hocquard@akeneo.com>
 * @copyright 2018 Akeneo SAS (http://www.akeneo.com)
 * @license   http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */
class ImportContinuousIntegrationMetricsHandler
{

    /** @var ListableRunRepository */
    private $listableRunRepository;

    /** @var SaveableRunRepository */
    private $saveableRunRepository;

    public function __construct(
        ListableRunRepository $listableRunRepository,
        SaveableRunRepository $saveableRunRepository
    ) {
        $this->listableRunRepository = $listableRunRepository;
        $this->saveableRunRepository = $saveableRunRepository;
    }

    public function handle(ImportContinuousIntegrationMetrics $command): void
    {
        $runsToImport = [];

        foreach ($command->pipelineNames as $pipelineName) {
            $runs = $this->listableRunRepository->listRunsFrom($pipelineName);

            foreach ($runs as $run) {
                if ($run->isRunFinished() && !$this->saveableRunRepository->hasRun($run)) {
                    $runsToImport[] = $run;
                }
            }
        }


        $this->saveableRunRepository->saveRuns($runsToImport);
    }
}
