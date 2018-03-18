<?php

declare(strict_types=1);

namespace spec\App\Application;

use App\Application\ImportContinuousIntegrationMetrics;
use App\Application\ImportContinuousIntegrationMetricsHandler;
use App\Model\Jenkins\Build\Build;
use App\Model\Jenkins\Build\ListableBuildRepository;
use App\Model\Jenkins\Build\SaveableBuildRepository;
use App\Model\Jenkins\Build\BuildUri;
use App\Model\Jenkins\Job\Job;
use App\Model\Jenkins\Job\JobName;
use App\Model\Jenkins\Job\JobRepository;
use App\Model\Jenkins\PullRequest\PullRequest;
use App\Model\Jenkins\PullRequest\PullRequestRepository;
use App\Model\Jenkins\PullRequest\PullRequestUri;
use PhpSpec\ObjectBehavior;

class ImportContinuousIntegrationMetricsHandlerSpec extends ObjectBehavior
{

    function let(
        JobRepository $jobRepository,
        PullRequestRepository $pullRequestRepository,
        ListableBuildRepository $sourceBuildRepository,
        SaveableBuildRepository $targetBuildRepository
    ) {
        $this->beConstructedWith(
            $jobRepository,
            $pullRequestRepository,
            $sourceBuildRepository,
            $targetBuildRepository
        );
    }

    function it_is_initializable()
    {
        $this->shouldHaveType(ImportContinuousIntegrationMetricsHandler::class);
    }

    function it_imports_build_metrics(
        $jobRepository,
        $pullRequestRepository,
        $sourceBuildRepository,
        $targetBuildRepository
    ) {
        $command = new ImportContinuousIntegrationMetrics();
        $command->jobNames = [new JobName('akeneo/pim-community-dev'), new JobName('akeneo/pim-enterprise-dev')];

        $ce = new Job(
            new JobName('akeneo/pim-community-dev'),
            [
                new PullRequestUri('https://ci.akeneo.com/job/akeneo/job/pim-community-dev/job/PR-1000/api/json'),
                new PullRequestUri('https://ci.akeneo.com/job/akeneo/job/pim-community-dev/job/PR-1001/api/json'),
            ]
        );

        $ce_PR_1 = new PullRequest(
            'PR-1000',
            [
                new BuildUri('https://ci.akeneo.com/job/akeneo/job/pim-community-dev/job/PR-1000/1/api/json'),
                new BuildUri('https://ci.akeneo.com/job/akeneo/job/pim-community-dev/job/PR-1000/2/api/json'),
            ]
        );

        $ce_PR_2 = new PullRequest(
            'PR-1001',
            [
                new BuildUri('https://ci.akeneo.com/job/akeneo/job/pim-community-dev/job/PR-1001/1/api/json'),
            ]
        );

        $ce_PR_1_build_1 = new Build(
            $ce_PR_1,
            new BuildUri('https://ci.akeneo.com/job/akeneo/job/pim-community-dev/job/PR-1000/1/api/json'),
            'ce_pr_1_build_1',
            'SUCCESS',
            '{}',
            1,
            100,
            new \DateTime('now'),
            0,
            0,
            10
        );

        $ce_PR_1_build_2 = new Build(
            $ce_PR_1,
            new BuildUri('https://ci.akeneo.com/job/akeneo/job/pim-community-dev/job/PR-1000/2/api/json'),
            'ce_pr_1_build_2',
            'FAILURE',
            '{}',
            1,
            100,
            new \DateTime('now'),
            1,
            0,
            9
        );

        $ce_PR_2_build_1 = new Build(
            $ce_PR_1,
            new BuildUri('https://ci.akeneo.com/job/akeneo/job/pim-community-dev/job/PR-1001/1/api/json'),
            'ce_pr_2_build_1',
            'SUCCESS',
            '{}',
            1,
            100,
            new \DateTime('now'),
            0,
            0,
            10
        );

        $ee = new Job(
            new JobName('akeneo/pim-enterprise-dev'),
            [
                new PullRequestUri('https://ci.akeneo.com/job/akeneo/job/pim-enterprise-dev/job/PR-2000/api/json'),
            ]
        );

        $ee_PR_1 = new PullRequest(
            'PR-2000',
            [
                new BuildUri('https://ci.akeneo.com/job/akeneo/job/pim-community-dev/job/PR-2000/1/api/json'),
            ]
        );

        $ee_PR_1_build_1 = new Build(
            $ee_PR_1,
            new BuildUri('https://ci.akeneo.com/job/akeneo/job/pim-enterprise-dev/job/PR-2000/1/api/json'),
            'ee_pr_1_build_1',
            'SUCCESS',
            '{}',
            1,
            100,
            new \DateTime('now'),
            0,
            0,
            10
        );

        $other = new Job(
            new JobName('other'),
            [
                new PullRequestUri('https://ci.akeneo.com/job/akeneo/job/other/job/PR-3000/api/json'),
            ]
        );

        $jobRepository->listJobs()->willReturn([$other, $ce, $ee]);

        $pullRequestRepository->listPullRequestsFrom($ce)->willReturn([$ce_PR_1, $ce_PR_2]);
        $pullRequestRepository->listPullRequestsFrom($ee)->willReturn([$ee_PR_1]);
        $pullRequestRepository->listPullRequestsFrom($other)->shouldNotBeCalled();

        $sourceBuildRepository->listBuildsFrom($ce_PR_1)->willReturn([$ce_PR_1_build_1, $ce_PR_1_build_2]);
        $sourceBuildRepository->listBuildsFrom($ce_PR_2)->willReturn([$ce_PR_2_build_1]);
        $sourceBuildRepository->listBuildsFrom($ee_PR_1)->willReturn([$ee_PR_1_build_1]);

        $targetBuildRepository->hasBuild($ce_PR_1_build_1)->willReturn(false);
        $targetBuildRepository->hasBuild($ce_PR_1_build_2)->willReturn(false);
        $targetBuildRepository->hasBuild($ce_PR_2_build_1)->willReturn(false);
        $targetBuildRepository->hasBuild($ee_PR_1_build_1)->willReturn(false);

        $targetBuildRepository
            ->saveBuilds([$ce_PR_1_build_1, $ce_PR_1_build_2, $ce_PR_2_build_1, $ee_PR_1_build_1])
            ->shouldBeCalled();

        $this->handle($command);
    }

    function it_does_not_imports_metrics_from_running_build(
        $jobRepository,
        $pullRequestRepository,
        $sourceBuildRepository,
        $targetBuildRepository
    ) {
        $command = new ImportContinuousIntegrationMetrics();
        $command->jobNames = [new JobName('akeneo/pim-community-dev')];

        $ce = new Job(
            new JobName('pim-community-dev'),
            [
                new PullRequestUri('https://ci.akeneo.com/job/akeneo/job/pim-community-dev/job/PR-1000/api/json'),
            ]
        );

        $ce_PR_1 = new PullRequest(
            'PR-1000',
            [
                new BuildUri('https://ci.akeneo.com/job/akeneo/job/pim-community-dev/job/PR-1000/1/api/json'),
            ]
        );

        $ce_PR_1_build_1 = new Build(
            $ce_PR_1,
            new BuildUri('https://ci.akeneo.com/job/akeneo/job/pim-community-dev/job/PR-1000/1/api/json'),
            'ce_pr_1_build_1',
            null,
            '{}',
            1,
            100,
            new \DateTime('now'),
            -1,
            -1,
            -1
        );

        $jobRepository->listJobs()->willReturn([$ce]);

        $pullRequestRepository->listPullRequestsFrom($ce)->willReturn([$ce_PR_1]);

        $sourceBuildRepository->listBuildsFrom($ce_PR_1)->willReturn([$ce_PR_1_build_1]);

        $targetBuildRepository->hasBuild($ce_PR_1_build_1)->willReturn(false);

        $targetBuildRepository->saveBuilds([])->shouldBeCalled();

        $this->handle($command);
    }

    function it_does_not_imports_already_imported_build_metrics(
        $jobRepository,
        $pullRequestRepository,
        $sourceBuildRepository,
        $targetBuildRepository
    ) {
        $command = new ImportContinuousIntegrationMetrics();
        $command->jobNames = [new JobName('pim-community-dev')];

        $ce = new Job(
            new JobName('pim-community-dev'),
            [
                new PullRequestUri('https://ci.akeneo.com/job/akeneo/job/pim-community-dev/job/PR-1000/api/json'),
            ]
        );

        $ce_PR_1 = new PullRequest(
            'PR-1000',
            [
                new BuildUri('https://ci.akeneo.com/job/akeneo/job/pim-community-dev/job/PR-1000/1/api/json'),
            ]
        );

        $ce_PR_1_build_1 = new Build(
            $ce_PR_1,
            new BuildUri('https://ci.akeneo.com/job/akeneo/job/pim-community-dev/job/PR-1000/1/api/json'),
            'ce_pr_1_build_1',
            'SUCCESS',
            '{}',
            1,
            100,
            new \DateTime('now'),
            0,
            0,
            10
        );

        $jobRepository->listJobs()->willReturn([$ce]);

        $pullRequestRepository->listPullRequestsFrom($ce)->willReturn([$ce_PR_1]);

        $sourceBuildRepository->listBuildsFrom($ce_PR_1)->willReturn([$ce_PR_1_build_1]);

        $targetBuildRepository->hasBuild($ce_PR_1_build_1)->willReturn(true);

        $targetBuildRepository->saveBuilds([])->shouldBeCalled();

        $this->handle($command);
    }
}
