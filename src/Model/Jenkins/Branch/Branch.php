<?php

declare(strict_types=1);

namespace App\Model\Jenkins\Branch;

use App\Model\Jenkins\Pipeline\PipelineName;
use App\Model\Jenkins\Run\Run;

/**
 * Represents a branch.
 * In Jenkins, a branch can be a pull request or a based branch (master, 1.x, etc).
 *
 * @author    Alexandre Hocquard <alexandre.hocquard@akeneo.com>
 * @copyright 2018 Akeneo SAS (http://www.akeneo.com)
 * @license   http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */
class Branch
{
    /** @var PipelineName */
    private $pipelineName;

    /** @var BranchName */
    private $name;

    /** @var Run[] */
    private $runs;

    /**
     * @param PipelineName $pipelineName
     * @param BranchName   $name
     * @param array        $runs
     */
    public function __construct(
        PipelineName $pipelineName,
        BranchName $name,
        array $runs
    )
    {
        $this->pipelineName = $pipelineName;
        $this->name = $name;
        $this->runs = $runs;
    }

    public function pipelineName(): PipelineName
    {
        $this->pipelineName;
    }

    public function name(): BranchName
    {
        return $this->name;
    }

    public function runs(): array
    {
        return $this->runs;
    }
}
