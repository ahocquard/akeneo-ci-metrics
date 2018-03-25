<?php

declare(strict_types=1);

namespace spec\App\Model\Jenkins\Pipeline;

use App\Model\Jenkins\Pipeline\PipelineName;
use PhpSpec\ObjectBehavior;

class PipelineNameSpec extends ObjectBehavior
{
    function let()
    {
        $this->beConstructedWith('pim-community-dev');
    }

    function it_is_initializable()
    {
        $this->shouldHaveType(PipelineName::class);
    }

    function it_has_a_value()
    {
        $this->value()->shouldReturn('pim-community-dev');
    }
}
