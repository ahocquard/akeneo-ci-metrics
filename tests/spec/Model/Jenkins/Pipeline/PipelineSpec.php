<?php

declare(strict_types=1);

namespace spec\App\Model\Jenkins\Pipeline;

use App\Model\Jenkins\Branch\Branch;
use App\Model\Jenkins\Branch\BranchName;
use App\Model\Jenkins\Pipeline\Pipeline;
use App\Model\Jenkins\Pipeline\PipelineName;
use PhpSpec\ObjectBehavior;

class PipelineSpec extends ObjectBehavior
{
    function let()
    {

        $pipelineName = new PipelineName('pim-community-dev');
        $this->beConstructedWith(
            $pipelineName,
            [
                new Branch($pipelineName, new BranchName('master')),
                new Branch($pipelineName, new BranchName('PR-1337')),
            ]
        );
    }

    function it_is_initializable()
    {
        $this->shouldHaveType(Pipeline::class);
    }

    function it_has_a_pipeline_name()
    {
        $this->name()->shouldBeLike(new PipelineName('pim-community-dev'));
    }

    function it_has_branches()
    {
        $this->branches()->shouldBeLike([
            new Branch(new PipelineName('pim-community-dev'), new BranchName('master')),
            new Branch(new PipelineName('pim-community-dev'), new BranchName('PR-1337')),
        ]);
    }

    function it_has_pull_request_branches()
    {
        $this->pullRequestBranches()->shouldBeLike([
            new Branch(new PipelineName('pim-community-dev'), new BranchName('PR-1337')),
        ]);
    }

    function it_has_origin_branches()
    {
        $this->originBranches()->shouldBeLike([
            new Branch(new PipelineName('pim-community-dev'), new BranchName('master')),
        ]);
    }
}
