<?php

declare(strict_types=1);

namespace spec\App\Model\Jenkins\Run;

use App\Model\Jenkins\Branch\BranchName;
use App\Model\Jenkins\Pipeline\PipelineName;
use App\Model\Jenkins\Run\Run;
use App\Model\Jenkins\Run\RunId;
use PhpSpec\ObjectBehavior;

class RunSpec extends ObjectBehavior
{
    function let()
    {
        $this->beConstructedWith(
            new PipelineName('pim-community-dev'),
            new BranchName('master'),
            new RunId('6'),
            'UNKNOWN',
            'PAUSED',
            0,
            \DateTime::createFromFormat('Y-m-d\TH:i:s.uO', '2018-03-21T11:15:21.639+0000'),
            \DateTime::createFromFormat('Y-m-d\TH:i:s.uO', '2018-03-21T11:15:21.660+0000'),
            \DateTime::createFromFormat('Y-m-d\TH:i:s.uO', '2018-03-21T12:15:21.660+0000'),
            10,
            11,
            12,
            13
        );
    }

    function it_is_initializable()
    {
        $this->shouldHaveType(Run::class);
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
        $this->id()->shouldBeLike(new RunId('6'));
    }

    function it_has_a_result()
    {
        $this->result()->shouldReturn('UNKNOWN');
    }

    function it_has_a_state()
    {
        $this->state()->shouldReturn('PAUSED');
    }

    function it_has_a_duration()
    {
        $this->duration()->shouldReturn(0);
    }

    function it_has_a_start_timestamp()
    {
        $this->startTimestamp()->shouldReturn(1521630921);
    }

    function it_has_the_number_of_failed_tests()
    {
        $this->numberOfFailedTests()->shouldReturn(10);
    }

    function it_has_the_number_of_skipped_tests()
    {
        $this->numberOfSkippedTests()->shouldReturn(11);
    }

    function it_has_the_number_of_succeeded_tests()
    {
        $this->numberOfSucceededTests()->shouldReturn(12);
    }

    function it_has_the_total_number_of_tests()
    {
        $this->numberOfTests()->shouldReturn(13);
    }

    function it_is_an_run_of_an_origin_branch()
    {
        $this->isPullRequestRun()->shouldReturn(false);
        $this->isOriginBranchRun()->shouldReturn(true);
    }

    function it_is_a_run_of_a_branch()
    {
        $this->beConstructedWith(
            new PipelineName('pim-community-dev'),
            new BranchName('PR-13337'),
            new RunId('6'),
            'UNKNOWN',
            'PAUSED',
            0,
            \DateTime::createFromFormat('Y-m-d\TH:i:s.uO', '2018-03-21T11:15:21.639+0000'),
            \DateTime::createFromFormat('Y-m-d\TH:i:s.uO', '2018-03-21T11:15:21.660+0000'),
            \DateTime::createFromFormat('Y-m-d\TH:i:s.uO', '2018-03-21T12:15:21.660+0000'),
            10,
            11,
            12,
            13
        );
        $this->isPullRequestRun()->shouldReturn(true);
        $this->isOriginBranchRun()->shouldReturn(false);
    }
}
