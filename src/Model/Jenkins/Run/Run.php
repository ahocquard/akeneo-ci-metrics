<?php

declare(strict_types=1);

namespace App\Model\Jenkins\Run;

use App\Model\Jenkins\Pipeline\PipelineName;
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
    private const RUN_FINISHED = 'FINISHED';

    /** @var string */
    private $name;

    /** @var int */
    private $runNumber;

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
    private $numberOfFailedTests;

    /** @var int */
    private $numberOfSkippedTests;

    /** @var int */
    private $numberOfSucceededTests;

    /** @var int */
    private $numberOfTests;

    /** @var StepUri */
    private $stepUri;

    /**
     * @param string                  $name
     * @param int                     $runNumber
     * @param PipelineName            $pipelineName
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
     * @param StepUri                 $stepUri
     */
    public function __construct(
        string $name,
        int $runNumber,
        PipelineName $pipelineName,
        string $result,
        string $state,
        int $duration,
        ?\DateTimeInterface $enQueueTime,
        ?\DateTimeInterface $startTime,
        ?\DateTimeInterface $endTime,
        int $numberOfFailedTests,
        int $numberOfSkippedTests,
        int $numberOfSucceededTests,
        int $numberOfTests,
        StepUri $stepUri
    ) {
        $this->name = $name;
        $this->runNumber = $runNumber;
        $this->pipelineName = $pipelineName;
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
        $this->stepUri = $stepUri;
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
        return false !== strpos($this->name, 'PR-');
    }

    /**
     * Indicate whether the run concerns a branch (master, 1.x, 2.x)
     */
    public function isBranchRun(): bool
    {
        return !$this->isPullRequestRun();
    }

    public function identifier(): string
    {
        return $this->pipelineName->value() . '_' . $this->name . '_' . $this->runNumber;
    }

    public function name(): string
    {
        return $this->pipelineName->value() . '_' . $this->name;
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

    public function startTimestamp(): int
    {
        return $this->startTime->getTimestamp();
    }

    public function pipelineName(): PipelineName
    {
        return $this->pipelineName;
    }

    public function numberOfFailedTests()
    {
        return $this->numberOfFailedTests;
    }

    public function numberOfSkippedTests()
    {
        return $this->numberOfFailedTests;
    }

    public function numberOfSucceededTests()
    {
        return $this->numberOfFailedTests;
    }

    public function numberOfTests()
    {
        return $this->numberOfTests;
    }
}
