<?php

declare(strict_types=1);

namespace App\Model\Jenkins\Pipeline;

/**
 * @author Alexandre Hocquard <alexandre.hocquard@akeneo.com>
 */
class PipelineName
{
    /** @var string */
    private $name;

    public function __construct(string $name)
    {
        $this->name = $name;
    }

    public function value(): string
    {
        return $this->name;
    }
}
