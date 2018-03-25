<?php

declare(strict_types=1);

namespace App\Model\Jenkins\Test;

use App\Model\Jenkins\Branch\BranchName;
use App\Model\Jenkins\Pipeline\PipelineName;
use App\Model\Jenkins\Run\RunId;

/**
 * @author    Alexandre Hocquard <alexandre.hocquard@akeneo.com>
 * @copyright 2018 Akeneo SAS (http://www.akeneo.com)
 * @license   http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */
class Test
{
    /** @var PipelineName */
    private $pipelineName;

    /** @var BranchName */
    private $branchName;

    /** @var RunId*/
    private $runId;

    /** @var string */
    private $id;

    /** @var float duration in seconds */
    private $duration;

    /**
     * @param PipelineName $pipelineName
     * @param BranchName   $branchName
     * @param RunId        $runId
     * @param TestId       $id
     * @param float        $duration
     */
    public function __construct(
        PipelineName $pipelineName,
        BranchName $branchName,
        RunId $runId,
        TestId $id,
        float $duration
    ) {
        $this->pipelineName = $pipelineName;
        $this->branchName = $branchName;
        $this->runId = $runId;
        $this->id = $id;
        $this->duration = $duration;
    }

    public function pipelineName(): PipelineName
    {
        return $this->pipelineName;
    }

    public function branchName(): BranchName
    {
        return $this->pipelineName;
    }

    public function runId(): RunId
    {
        return $this->runId;
    }

    public function id(): TestId
    {
        return $this->id;
    }

    public function duration(): float
    {
        return $this->duration;
    }

    public function type(): string
    {
        return 'phpunit';
    }
}
