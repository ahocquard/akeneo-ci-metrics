<?php

declare(strict_types=1);

namespace spec\App\Application\Command;

use App\Application\Command\ImportRunMetrics;
use App\Application\Command\ImportRunMetricsHandler;
use App\Model\Jenkins\Branch\Branch;
use App\Model\Jenkins\Branch\BranchName;
use App\Model\Jenkins\Pipeline\GettablePipelineRepository;
use App\Model\Jenkins\Pipeline\Pipeline;
use App\Model\Jenkins\Pipeline\PipelineName;
use App\Model\Jenkins\Run\ListableRunRepository;
use App\Model\Jenkins\Run\Run;
use App\Model\Jenkins\Run\RunRepository;
use PhpSpec\ObjectBehavior;
use Psr\Log\LoggerInterface;

class ImportRunMetricsHandlerSpec extends ObjectBehavior
{

    function let(
        LoggerInterface $logger,
        GettablePipelineRepository $pipelineRepository,
        ListableRunRepository $listableRunRepository,
        RunRepository $saveableRunRepository
    ) {
        $this->beConstructedWith(
            $logger,
            $pipelineRepository,
            $listableRunRepository,
            $saveableRunRepository
        );
    }

    function it_is_initializable()
    {
        $this->shouldHaveType(ImportRunMetricsHandler::class);
    }

    function it_imports_run_metrics(
        $pipelineRepository,
        $listableRunRepository,
        $saveableRunRepository,
        Pipeline $cePipeline,
        Pipeline $eePipeline,
        Branch $ceMasterBranch,
        Branch $eeMasterBranch,
        Run $ceRun1,
        Run $ceRun2,
        Run $eeRun1
    ) {
        $command = new ImportRunMetrics();
        $cePipelineName = new PipelineName('pim-community-dev');
        $eePipelineName = new PipelineName('pim-enterprise-dev');
        $command->pipelineNames = [$cePipelineName, $eePipelineName];

        $pipelineRepository->getPipeline($cePipelineName)->willReturn($cePipeline);
        $cePipeline->branches()->willReturn([$ceMasterBranch]);
        $listableRunRepository->listRunsFrom($ceMasterBranch)->willReturn([$ceRun1, $ceRun2]);
        $ceMasterBranch->name()->willReturn(new BranchName('master'));

        $pipelineRepository->getPipeline($eePipelineName)->willReturn($eePipeline);
        $eePipeline->branches()->willReturn([$eeMasterBranch]);
        $listableRunRepository->listRunsFrom($eeMasterBranch)->willReturn([$eeRun1]);
        $eeMasterBranch->name()->willReturn(new BranchName('master'));

        $ceRun1->isRunFinished()->willReturn(true);
        $ceRun2->isRunFinished()->willReturn(true);
        $eeRun1->isRunFinished()->willReturn(true);

        $saveableRunRepository->hasRun($ceRun1)->willReturn(false);
        $saveableRunRepository->hasRun($ceRun2)->willReturn(false);
        $saveableRunRepository->hasRun($eeRun1)->willReturn(false);

        $saveableRunRepository
            ->saveRuns([$ceRun1, $ceRun2])
            ->shouldBeCalled();

        $saveableRunRepository
            ->saveRuns([$eeRun1])
            ->shouldBeCalled();

        $this->handle($command);
    }

    function it_does_not_imports_metrics_from_running_build(
        $pipelineRepository,
        $listableRunRepository,
        $saveableRunRepository,
        Pipeline $cePipeline,
        Branch $ceMasterBranch,
        Run $ceRun1,
        Run $ceRun2
    ) {
        $command = new ImportRunMetrics();
        $cePipelineName = new PipelineName('pim-community-dev');
        $command->pipelineNames = [$cePipelineName];

        $pipelineRepository->getPipeline($cePipelineName)->willReturn($cePipeline);
        $cePipeline->branches()->willReturn([$ceMasterBranch]);
        $listableRunRepository->listRunsFrom($ceMasterBranch)->willReturn([$ceRun1, $ceRun2]);
        $ceMasterBranch->name()->willReturn(new BranchName('master'));

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
        $pipelineRepository,
        $listableRunRepository,
        $saveableRunRepository,
        Pipeline $cePipeline,
        Branch $ceMasterBranch,
        Run $ceRun1,
        Run $ceRun2
    ) {
        $command = new ImportRunMetrics();
        $cePipelineName = new PipelineName('pim-community-dev');
        $command->pipelineNames = [$cePipelineName];

        $pipelineRepository->getPipeline($cePipelineName)->willReturn($cePipeline);
        $cePipeline->branches()->willReturn([$ceMasterBranch]);
        $listableRunRepository->listRunsFrom($ceMasterBranch)->willReturn([$ceRun1, $ceRun2]);
        $ceMasterBranch->name()->willReturn(new BranchName('master'));

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
