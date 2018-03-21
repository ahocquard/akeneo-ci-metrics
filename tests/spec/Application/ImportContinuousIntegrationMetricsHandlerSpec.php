<?php

declare(strict_types=1);

namespace spec\App\Application;

use App\Application\ImportContinuousIntegrationMetrics;
use App\Application\ImportContinuousIntegrationMetricsHandler;
use App\Model\Jenkins\Pipeline\PipelineName;
use App\Model\Jenkins\Run\ListableRunRepository;
use App\Model\Jenkins\Run\Run;
use App\Model\Jenkins\Run\SaveableRunRepository;
use PhpSpec\ObjectBehavior;

class ImportContinuousIntegrationMetricsHandlerSpec extends ObjectBehavior
{

    function let(
        ListableRunRepository $listableRunRepository,
        SaveableRunRepository $saveableRunRepository
    ) {
        $this->beConstructedWith($listableRunRepository, $saveableRunRepository);
    }

    function it_is_initializable()
    {
        $this->shouldHaveType(ImportContinuousIntegrationMetricsHandler::class);
    }

    function it_imports_build_metrics(
        $listableRunRepository,
        $saveableRunRepository,
        Run $ceRun1,
        Run $ceRun2,
        Run $eeRun1
    ) {
        $command = new ImportContinuousIntegrationMetrics();
        $cePipelineName = new PipelineName('pim-community-dev');
        $eePipelineName = new PipelineName('pim-enterprise-dev');
        $command->pipelineNames = [$cePipelineName, $eePipelineName];

        $listableRunRepository->listRunsFrom($cePipelineName)->willReturn([$ceRun1, $ceRun2]);
        $listableRunRepository->listRunsFrom($eePipelineName)->willReturn([$eeRun1]);

        $ceRun1->isRunFinished()->willReturn(true);
        $ceRun2->isRunFinished()->willReturn(true);
        $eeRun1->isRunFinished()->willReturn(true);

        $saveableRunRepository->hasRun($ceRun1)->willReturn(false);
        $saveableRunRepository->hasRun($ceRun2)->willReturn(false);
        $saveableRunRepository->hasRun($eeRun1)->willReturn(false);

        $saveableRunRepository
            ->saveRuns([$ceRun1, $ceRun2, $eeRun1])
            ->shouldBeCalled();

        $this->handle($command);
    }

    function it_does_not_imports_metrics_from_running_build(
        $listableRunRepository,
        $saveableRunRepository,
        Run $ceRun1,
        Run $ceRun2
    ) {
        $command = new ImportContinuousIntegrationMetrics();
        $cePipelineName = new PipelineName('pim-community-dev');
        $command->pipelineNames = [$cePipelineName];

        $listableRunRepository->listRunsFrom($cePipelineName)->willReturn([$ceRun1, $ceRun2]);

        $ceRun1->isRunFinished()->willReturn(true);
        $ceRun2->isRunFinished()->willReturn(false);

        $saveableRunRepository->hasRun($ceRun1)->willReturn(false);
        $saveableRunRepository->hasRun($ceRun2)->willReturn(false);

        $saveableRunRepository
            ->saveRuns([$ceRun1])
            ->shouldBeCalled();

        $this->handle($command);
    }

    function it_does_not_imports_already_imported_build_metrics(
        $listableRunRepository,
        $saveableRunRepository,
        Run $ceRun1,
        Run $ceRun2
    ) {
        $command = new ImportContinuousIntegrationMetrics();
        $cePipelineName = new PipelineName('pim-community-dev');
        $command->pipelineNames = [$cePipelineName];

        $listableRunRepository->listRunsFrom($cePipelineName)->willReturn([$ceRun1, $ceRun2]);

        $ceRun1->isRunFinished()->willReturn(true);
        $ceRun2->isRunFinished()->willReturn(true);

        $saveableRunRepository->hasRun($ceRun1)->willReturn(false);
        $saveableRunRepository->hasRun($ceRun2)->willReturn(true);

        $saveableRunRepository
            ->saveRuns([$ceRun1])
            ->shouldBeCalled();

        $this->handle($command);
    }
}
