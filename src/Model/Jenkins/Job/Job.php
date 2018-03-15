<?php

declare(strict_types=1);

namespace App\Model\Jenkins\Job;

/**
 * A job in Jenkins represents a repository on Github.
 *
 * @author    Alexandre Hocquard <alexandre.hocquard@akeneo.com>
 * @copyright 2018 Akeneo SAS (http://www.akeneo.com)
 * @license   http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */
class Job
{
    /** @var JobName */
    private $name;

    /** @var array */
    private $pullRequestUris;

    /**
     * @param JobName $name
     * @param array   $pullRequestUris
     */
    public function __construct(JobName $name, array $pullRequestUris)
    {
        $this->name = $name;
        $this->pullRequestUris = $pullRequestUris;
    }

    public function name()
    {
        return $this->name;
    }
}
