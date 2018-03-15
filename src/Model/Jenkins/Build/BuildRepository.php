<?php

declare(strict_types=1);

namespace App\Model\Jenkins\Build;

use App\Model\Jenkins\PullRequest\PullRequest;

/**
 * @author    Alexandre Hocquard <alexandre.hocquard@akeneo.com>
 * @copyright 2018 Akeneo SAS (http://www.akeneo.com)
 * @license   http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */
interface BuildRepository
{
    public function listBuildsFrom(PullRequest $pullRequest): array;
}
