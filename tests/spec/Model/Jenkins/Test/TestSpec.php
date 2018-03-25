<?php

declare(strict_types=1);

namespace spec\App\Model\Jenkins\Test;

use App\Model\Jenkins\Branch\BranchName;
use App\Model\Jenkins\Pipeline\PipelineName;
use App\Model\Jenkins\Run\RunId;
use App\Model\Jenkins\Test\Test;
use App\Model\Jenkins\Test\TestName;
use PhpSpec\ObjectBehavior;

class TestSpec extends ObjectBehavior
{
    function let() {
        $this->beConstructedWith(
            new PipelineName('pim-community-dev'),
            new BranchName('master'),
            new RunId('6'),
            new TestName('Test / phpunit-integration-ce / testOperatorLowerOrEqualThanAllLocales'),
            new \DateTime('2018-01-01'),
            3.6
        );
    }

    function it_is_initializable()
    {
        $this->shouldHaveType(Test::class);
    }

    function it_has_a_pipeline_name()
    {
        $this->pipelineName()->shouldBeLike(new PipelineName('pim-community-dev'));
    }

    function it_has_a_branch_name()
    {
        $this->branchName()->shouldBeLike(new BranchName('master'));
    }

    function it_has_a_run_id()
    {
        $this->runId()->shouldBeLike(new RunId('6'));
    }

    function it_has_a_test_id()
    {
        $this->name()->shouldBeLike(new TestName('Test / phpunit-integration-ce / testOperatorLowerOrEqualThanAllLocales'));
    }

    function it_has_a_duration()
    {
        $this->duration()->shouldReturn(3.6);
    }

    function it_has_an_execution_timestamp()
    {
        $this->executionTimestamp()->shouldReturn(1514761200);
    }

    function it_has_no_type()
    {
        $this->beConstructedWith(
            new PipelineName('pim-community-dev'),
            new BranchName('master'),
            new RunId('6'),
            new TestName('Test testOperatorLowerOrEqualThanAllLocales'),
            new \DateTime('2018-01-01'),
            3.6
        );

        $this->type()->shouldReturn('');
    }

}
