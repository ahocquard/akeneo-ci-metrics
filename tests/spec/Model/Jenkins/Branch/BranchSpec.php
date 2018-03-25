<?php

declare(strict_types=1);

namespace spec\App\Model\Jenkins\Branch;

use App\Model\Jenkins\Branch\Branch;
use App\Model\Jenkins\Branch\BranchName;
use App\Model\Jenkins\Pipeline\PipelineName;
use PhpSpec\ObjectBehavior;

class BranchSpec extends ObjectBehavior
{
    function let()
    {
        $this->beConstructedWith(new PipelineName('pim-community-dev'), new BranchName('master'));
    }

    function it_is_initializable()
    {
        $this->shouldHaveType(Branch::class);
    }

    function it_has_a_pipeline_name()
    {
        $this->pipelineName()->shouldBeLike(new PipelineName('pim-community-dev'));
    }

    function it_has_a_branch_name()
    {
        $this->name()->shouldBeLike(new BranchName('master'));
    }

    function it_is_an_origin_branch()
    {
        $this->isOriginBranch()->shouldReturn(true);
        $this->isPullRequestBranch()->shouldReturn(false);
    }

    function it_is_a_pull_request_branch()
    {
        $this->beConstructedWith(new PipelineName('pim-community-dev'), new BranchName('PR-1337'));
        $this->isOriginBranch()->shouldReturn(false);
        $this->isPullRequestBranch()->shouldReturn(true);
    }
}
