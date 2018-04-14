<?php

declare(strict_types=1);

namespace App\Model\Jenkins\Run;

use App\Model\Jenkins\Branch\BranchName;
use App\Model\Jenkins\Pipeline\PipelineName;

/**
 * In Blue Ocean, a run is a build for a given branch/pull request.
 *
 * @author Alexandre Hocquard <alexandre.hocquard@akeneo.com>
 */
class Run
{
    private const RUN_FINISHED = 'FINISHED';

    /** @var PipelineName */
    private $pipelineName;

    /** @var BranchName */
    private $branchName;

    /** @var RunId */
    private $id;

    /** @var string */
    private $result;

    /** @var string */
    private $state;

    /** @var int duration in seconds */
    private $duration;

    /** @var \DateTimeInterface */
    private $enQueueTime;

    /** @var \DateTimeInterface */
    private $startTime;

    /** @var \DateTimeInterface */
    private $endTime;

    /** @var int */
    private $numberOfFailedTests;

    /** @var int */
    private $numberOfSkippedTests;

    /** @var int */
    private $numberOfSucceededTests;

    /** @var int */
    private $numberOfTests;

    /**
     * @param PipelineName            $pipelineName
     * @param BranchName              $branchName
     * @param RunId                   $id
     * @param string                  $result
     * @param string                  $state
     * @param int                     $duration
     * @param null|\DateTimeInterface $enQueueTime
     * @param null|\DateTimeInterface $startTime
     * @param null|\DateTimeInterface $endTime
     * @param int                     $numberOfFailedTests
     * @param int                     $numberOfSkippedTests
     * @param int                     $numberOfSucceededTests
     * @param int                     $numberOfTests
     */
    public function __construct(
        PipelineName $pipelineName,
        BranchName $branchName,
        RunId $id,
        string $result,
        string $state,
        int $duration,
        ?\DateTimeInterface $enQueueTime,
        ?\DateTimeInterface $startTime,
        ?\DateTimeInterface $endTime,
        int $numberOfFailedTests,
        int $numberOfSkippedTests,
        int $numberOfSucceededTests,
        int $numberOfTests
    ) {
        $this->pipelineName = $pipelineName;
        $this->branchName = $branchName;
        $this->id = $id;
        $this->result = $result;
        $this->state = $state;
        $this->duration = $duration;
        $this->enQueueTime = $enQueueTime;
        $this->startTime = $startTime;
        $this->endTime = $endTime;
        $this->numberOfFailedTests = $numberOfFailedTests;
        $this->numberOfSkippedTests = $numberOfSkippedTests;
        $this->numberOfSucceededTests = $numberOfSucceededTests;
        $this->numberOfTests = $numberOfTests;
    }

    public function pipelineName(): PipelineName
    {
        return $this->pipelineName;
    }

    public function branchName(): BranchName
    {
        return $this->branchName;
    }

    public function id(): RunId
    {
        return $this->id;
    }

    public function isRunFinished(): bool
    {
        return self::RUN_FINISHED === $this->state;
    }

    /**
     * Indicate whether the run concerns a pull request.
     */
    public function isPullRequestRun(): bool
    {
        return false !== strpos($this->branchName->value(), 'PR-');
    }

    /**
     * Indicate whether the run concerns a branch (master, 1.x, 2.x)
     */
    public function isOriginBranchRun(): bool
    {
        return !$this->isPullRequestRun();
    }

    public function duration(): int
    {
        return $this->duration;
    }

    public function state(): string
    {
        return $this->state;
    }

    public function result(): string
    {
        return $this->result;
    }

    public function startTime(): \DateTimeInterface
    {
        return $this->startTime;
    }

    public function startTimestamp(): int
    {
        return $this->startTime->getTimestamp();
    }

    public function numberOfFailedTests(): int
    {
        return $this->numberOfFailedTests;
    }

    public function numberOfSkippedTests(): int
    {
        return $this->numberOfSkippedTests;
    }

    public function numberOfSucceededTests(): int
    {
        return $this->numberOfSucceededTests;
    }

    public function numberOfTests(): int
    {
        return $this->numberOfTests;
    }
}
