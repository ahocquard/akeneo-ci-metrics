<?php

declare(strict_types=1);

namespace App\Model\Jenkins\Test;

use App\Model\Jenkins\Run\Run;

/**
 * @author Alexandre Hocquard <alexandre.hocquard@akeneo.com>
 */
interface ListableTestRepository
{
    public function listTestsFrom(Run $run): array;
}
