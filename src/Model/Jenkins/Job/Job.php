<?php

declare(strict_types=1);

namespace App\Model\Jenkins\Job;

use App\Model\Jenkins\PullRequest\PullRequestUri;

/**
 * A job in Jenkins represents a repository on Github.
 * A job is linked to several pull requests.
 *
 * @author    Alexandre Hocquard <alexandre.hocquard@akeneo.com>
 * @copyright 2018 Akeneo SAS (http://www.akeneo.com)
 * @license   http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */
class Job
{
    /** @var JobName */
    private $fullName;

    /** @var PullRequestUri[] */
    private $pullRequestUris;

    public function __construct(JobName $fullName, array $pullRequestUris)
    {
        $this->fullName = $fullName;
        $this->pullRequestUris = $pullRequestUris;
    }

    public function fullName(): JobName
    {
        return $this->fullName;
    }

    public function pullRequestUris(): array
    {
        return $this->pullRequestUris;
    }
}
