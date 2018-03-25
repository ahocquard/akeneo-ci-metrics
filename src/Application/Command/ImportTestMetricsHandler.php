<?php

declare(strict_types=1);

namespace App\Application\Command;

use App\Model\Jenkins\Pipeline\GettablePipelineRepository;
use App\Model\Jenkins\Run\ListableRunRepository;
use App\Model\Jenkins\Run\RunRepository;
use App\Model\Jenkins\Test\ListableTestRepository;
use App\Model\Jenkins\Test\TestRepository;
use Psr\Log\LoggerInterface;

/**
 * @author    Alexandre Hocquard <alexandre.hocquard@akeneo.com>
 * @copyright 2018 Akeneo SAS (http://www.akeneo.com)
 * @license   http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */
class ImportTestMetricsHandler
{
    /** @var LoggerInterface */
    private $logger;

    /** @var GettablePipelineRepository */
    private $pipelineRepository;

    /** @var ListableRunRepository */
    private $listableRunRepository;

    /** @var ListableTestRepository */
    private $listableTestRepository;

    /** @var TestRepository */
    private $saveableTestRepository;

    public function __construct(
        LoggerInterface $logger,
        GettablePipelineRepository $pipelineRepository,
        ListableRunRepository $listableRunRepository,
        ListableTestRepository $listableTestRepository,
        TestRepository $saveableTestRepository
    ) {
        $this->logger = $logger;
        $this->pipelineRepository = $pipelineRepository;
        $this->listableRunRepository = $listableRunRepository;
        $this->listableTestRepository = $listableTestRepository;
        $this->saveableTestRepository = $saveableTestRepository;
    }

    public function handle(ImportTestMetrics $command): void
    {
        $this->logger->info('Starting to import test metrics.');
        foreach ($command->pipelineNames as $pipelineName) {
            $pipeline = $this->pipelineRepository->getPipeline($pipelineName);

            foreach ($pipeline->originBranches() as $branch) {

                $runs = $this->listableRunRepository->listRunsFrom($branch);
                $this->logger->debug(sprintf('List runs from branch "%s".', $branch->name()->value()));
                foreach ($runs as $run) {
                    if ($run->isRunFinished() && !$this->saveableTestRepository->hasRun($run)) {
                        $this->logger->debug(
                            sprintf(
                                'List tests from branch "%s" and run "%s".',
                                $branch->name()->value(),
                                $run->id()->value()
                            )
                        );
                        $testsToImport = $this->listableTestRepository->listTestsFrom($run);

                        $this->saveableTestRepository->saveTests($testsToImport);
                    }

                    break;
                }
            }

        }
    }
}
