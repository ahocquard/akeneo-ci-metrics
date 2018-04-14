<?php

declare(strict_types=1);

namespace App\Model\Jenkins\Branch;

use App\Model\Jenkins\Pipeline\PipelineName;

/**
 * Represents a branch.
 * In Jenkins, a branch can be a pull request or a based branch (master, 1.x, etc).
 *
 * @author Alexandre Hocquard <alexandre.hocquard@akeneo.com>
 */
class Branch
{
    /** @var PipelineName */
    private $pipelineName;

    /** @var BranchName */
    private $name;

    /**
     * @param PipelineName $pipelineName
     * @param BranchName   $name
     */
    public function __construct(
        PipelineName $pipelineName,
        BranchName $name
    )
    {
        $this->pipelineName = $pipelineName;
        $this->name = $name;
    }

    public function pipelineName(): PipelineName
    {
        return $this->pipelineName;
    }

    public function name(): BranchName
    {
        return $this->name;
    }

    /**
     * Indicate whether the run concerns a branch (master, 1.x, 2.x)
     */
    public function isPullRequestBranch(): bool
    {
        return false !== strpos($this->name->value(), 'PR-');
    }

    /**
     * Indicate whether the run concerns a branch (master, 1.x, 2.x)
     */
    public function isOriginBranch(): bool
    {
        return !$this->isPullRequestBranch();
    }
}
