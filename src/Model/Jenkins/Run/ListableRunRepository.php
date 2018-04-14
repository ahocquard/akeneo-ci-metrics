<?php

declare(strict_types=1);

namespace App\Model\Jenkins\Run;

use App\Model\Jenkins\Branch\Branch;

/**
 * @author Alexandre Hocquard <alexandre.hocquard@akeneo.com>
 */
interface ListableRunRepository
{
    public function listRunsFrom(Branch $branch): array;
}
