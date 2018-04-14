<?php

declare(strict_types=1);

namespace App\Model\Jenkins\Branch;

/**
 * @author Alexandre Hocquard <alexandre.hocquard@akeneo.com>
 */
class BranchName
{
    /** @var string */
    private $name;

    /**
     * @param string $name
     */
    public function __construct($name)
    {
        $this->name = $name;
    }

    public function value(): string
    {
        return $this->name;
    }
}
