<?php

declare(strict_types=1);

namespace App\Model\Jenkins\Test;

use App\Model\Jenkins\Branch\BranchName;
use App\Model\Jenkins\Pipeline\PipelineName;
use App\Model\Jenkins\Run\RunId;

/**
 * @author Alexandre Hocquard <alexandre.hocquard@akeneo.com>
 */
class Test
{
    /** @var PipelineName */
    private $pipelineName;

    /** @var BranchName */
    private $branchName;

    /** @var RunId*/
    private $runId;

    /** @var \DateTimeInterface */
    private $executionTime;

    /** @var string */
    private $name;

    /** @var float duration in seconds */
    private $duration;

    /** @var string */
    private $type;

    /**
     * @param PipelineName       $pipelineName
     * @param BranchName         $branchName
     * @param RunId              $runId
     * @param TestName           $name
     * @param \DateTimeInterface $executionTime
     * @param float              $duration
     */
    public function __construct(
        PipelineName $pipelineName,
        BranchName $branchName,
        RunId $runId,
        TestName $name,
        \DateTimeInterface $executionTime,
        float $duration
    ) {
        $this->pipelineName = $pipelineName;
        $this->branchName = $branchName;
        $this->runId = $runId;
        $this->name = $name;
        $this->duration = $duration;
        $this->executionTime = $executionTime;

        $explodedId = explode('/', $this->name->value());
        $type = $explodedId[1] ?? '';
        $this->type = trim($type);
    }

    public function pipelineName(): PipelineName
    {
        return $this->pipelineName;
    }

    public function branchName(): BranchName
    {
        return $this->branchName;
    }

    public function runId(): RunId
    {
        return $this->runId;
    }

    public function name(): TestName
    {
        return $this->name;
    }

    public function duration(): float
    {
        return $this->duration;
    }

    public function type(): string
    {
        return $this->type;
    }

    public function executionTimestamp(): int
    {
        return $this->executionTime->getTimestamp();
    }

    public function executionTime(): \DateTimeInterface
    {
        return $this->executionTime;
    }
}
