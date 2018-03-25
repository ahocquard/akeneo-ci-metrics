<?php

declare(strict_types=1);

namespace spec\App\Model\Jenkins\Test;

use App\Model\Jenkins\Run\RunId;
use App\Model\Jenkins\Test\TestName;
use PhpSpec\ObjectBehavior;

class TestNameSpec extends ObjectBehavior
{
    function let()
    {
        $this->beConstructedWith('Test / phpunit-integration-ce / testOperatorLowerOrEqualThanAllLocales');
    }

    function it_is_initializable()
    {
        $this->shouldHaveType(TestName::class);
    }

    function it_has_a_value()
    {
        $this->value()->shouldReturn('Test / phpunit-integration-ce / testOperatorLowerOrEqualThanAllLocales');
    }
}
