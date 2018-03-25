<?php

declare(strict_types=1);

namespace spec\App\Model\Jenkins\Branch;

use App\Model\Jenkins\Branch\BranchName;
use PhpSpec\ObjectBehavior;

class BranchNameSpec extends ObjectBehavior
{
    function let()
    {
        $this->beConstructedWith('master');
    }

    function it_is_initializable()
    {
        $this->shouldHaveType(BranchName::class);
    }

    function it_has_a_value()
    {
        $this->value()->shouldReturn('master');
    }
}
