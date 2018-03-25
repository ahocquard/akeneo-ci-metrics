<?php

declare(strict_types=1);

namespace spec\App\Model\Jenkins\Run;

use App\Model\Jenkins\Run\RunId;
use PhpSpec\ObjectBehavior;

class RunIdSpec extends ObjectBehavior
{
    function let()
    {
        $this->beConstructedWith('6');
    }

    function it_is_initializable()
    {
        $this->shouldHaveType(RunId::class);
    }

    function it_has_a_value()
    {
        $this->value()->shouldReturn('6');
    }
}
