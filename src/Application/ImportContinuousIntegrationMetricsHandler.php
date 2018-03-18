<?php

declare(strict_types=1);

namespace App\Application;

use App\Model\Jenkins\Build\ListableBuildRepository;
use App\Model\Jenkins\Build\SaveableBuildRepository;
use App\Model\Jenkins\Job\Job;
use App\Model\Jenkins\Job\JobRepository;
use App\Model\Jenkins\PullRequest\PullRequestRepository;

/**
 * @author    Alexandre Hocquard <alexandre.hocquard@akeneo.com>
 * @copyright 2018 Akeneo SAS (http://www.akeneo.com)
 * @license   http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */
class ImportContinuousIntegrationMetricsHandler
{

    /** @var JobRepository */
    private $jobRepository;

    /** @var PullRequestRepository */
    private $pullRequestRepository;

    /** @var SaveableBuildRepository */
    private $sourceBuildRepository;

    /** @var SaveableBuildRepository */
    private $targetBuildRepository;

    public function __construct(
        JobRepository $jobRepository,
        PullRequestRepository $pullRequestRepository,
        ListableBuildRepository $sourceBuildRepository,
        SaveableBuildRepository $targetBuildRepository
    ) {
        $this->jobRepository = $jobRepository;
        $this->pullRequestRepository = $pullRequestRepository;
        $this->sourceBuildRepository = $sourceBuildRepository;
        $this->targetBuildRepository = $targetBuildRepository;
    }

    public function handle(ImportContinuousIntegrationMetrics $command): void
    {
        $buildsToImport = [];

        $jobs = $this->jobRepository->listJobs();
        $filteredJobs = array_filter($jobs, function (Job $job) use ($command) {
            foreach ($command->jobNames as $jobName) {
                if ($jobName->value() === $job->fullName()->value()) {
                    return true;
                }
            }

            return false;
        });

        foreach ($filteredJobs as $job) {
            $pullRequests = $this->pullRequestRepository->listPullRequestsFrom($job);

            foreach ($pullRequests as $pullRequest) {
                $builds = $this->sourceBuildRepository->listBuildsFrom($pullRequest);

                foreach ($builds as $build) {
                    if ($build->isBuildFinished() && !$this->targetBuildRepository->hasBuild($build)) {
                        $buildsToImport[] = $build;
                    }
                }
            }
        }

        $this->targetBuildRepository->saveBuilds($buildsToImport);
    }
}
