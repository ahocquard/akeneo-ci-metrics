<?php

declare(strict_types=1);

namespace App\Model\Jenkins\Build;

/**
 * @author    Alexandre Hocquard <alexandre.hocquard@akeneo.com>
 * @copyright 2018 Akeneo SAS (http://www.akeneo.com)
 * @license   http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */
interface SaveableBuildRepository
{
    /**
     * @param Build[] $builds
     */
    public function saveBuilds(array $builds): void;

    /**
     * @param Build $build
     *
     * @return bool
     */
    public function hasBuild(Build $build): bool;
}
