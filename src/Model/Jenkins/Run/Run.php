<?php

declare(strict_types=1);

namespace App\Model\Jenkins\Run;

use App\Model\Jenkins\Job\PipelineName;
use App\Model\Jenkins\PullRequest\PullRequest;
use App\Model\Jenkins\Step\StepUri;

/**
 * In Blue Ocean, a run is a build for a given branch/pull request.
 *
 * @author    Alexandre Hocquard <alexandre.hocquard@akeneo.com>
 * @copyright 2018 Akeneo SAS (http://www.akeneo.com)
 * @license   http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */
class Run
{
    /** @var string */
    private $name;

    /** @var PipelineName */
    private $pipelineName;

    /** @var string */
    private $result;

    /** @var string */
    private $state;

    /** @var int */
    private $duration;

    /** @var \DateTimeInterface */
    private $enQueueTime;

    /** @var \DateTimeInterface */
    private $startTime;

    /** @var \DateTimeInterface */
    private $endTime;

    /** @var int */
    private $numberFailedTests;

    /** @var int */
    private $numberSkippedTests;

    /** @var int */
    private $numberSucceededTests;

    /** @var int */
    private $numberTests;

    /** @var StepUri */
    private $stepUri;

    /**
     * @param string             $name
     * @param PipelineName       $pipelineName
     * @param string             $result
     * @param string             $state
     * @param int                $duration
     * @param \DateTimeInterface $enQueueTime
     * @param \DateTimeInterface $startTime
     * @param \DateTimeInterface $endTime
     * @param int                $numberFailedTests
     * @param int                $numberSkippedTests
     * @param int                $numberSucceededTests
     * @param int                $numberTests
     * @param StepUri            $stepUri
     */
    public function __construct(
        string $name,
        PipelineName $pipelineName,
        string $result,
        string $state,
        int $duration,
        \DateTimeInterface $enQueueTime,
        \DateTimeInterface $startTime,
        \DateTimeInterface $endTime,
        int $numberFailedTests,
        int $numberSkippedTests,
        int $numberSucceededTests,
        int $numberTests,
        StepUri $stepUri
    ) {
        $this->name = $name;
        $this->pipelineName = $pipelineName;
        $this->result = $result;
        $this->state = $state;
        $this->duration = $duration;
        $this->enQueueTime = $enQueueTime;
        $this->startTime = $startTime;
        $this->endTime = $endTime;
        $this->numberFailedTests = $numberFailedTests;
        $this->numberSkippedTests = $numberSkippedTests;
        $this->numberSucceededTests = $numberSucceededTests;
        $this->numberTests = $numberTests;
        $this->stepUri = $stepUri;
    }

    public function isRunFinished(): bool
    {
        return 'FINISHED' === $this->state;
    }

    public function isPullRequestRun(): bool
    {
        return !$this->isBranchRun();
    }

    public function isBranchRun(): bool
    {
        return false;
    }
}
