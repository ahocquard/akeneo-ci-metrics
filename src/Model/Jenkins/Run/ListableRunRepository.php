<?php

declare(strict_types=1);

namespace App\Model\Jenkins\Run;

use App\Model\Jenkins\Branch\Branch;

/**
 * @author    Alexandre Hocquard <alexandre.hocquard@akeneo.com>
 * @copyright 2018 Akeneo SAS (http://www.akeneo.com)
 * @license   http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */
interface ListableRunRepository
{
    public function listRunsFrom(Branch $branch): array;
}
