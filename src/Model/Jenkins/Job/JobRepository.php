<?php

declare(strict_types=1);

namespace App\Model\Jenkins\Job;

/**
 * @author    Alexandre Hocquard <alexandre.hocquard@akeneo.com>
 * @copyright 2018 Akeneo SAS (http://www.akeneo.com)
 * @license   http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */
interface JobRepository
{
    /**
     * @return array
     */
    public function listJobs(): array;
}