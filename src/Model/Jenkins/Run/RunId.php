<?php

declare(strict_types=1);

namespace App\Model\Jenkins\Run;

/**
 * @author Alexandre Hocquard <alexandre.hocquard@akeneo.com>
 */
class RunId
{
    /** @var string */
    private $id;

    public function __construct(string $id)
    {
        $this->id = $id;
    }

    public function value(): string
    {
        return $this->id;
    }
}
