<?php

declare(strict_types=1);

namespace App\Model\Jenkins\Build;

use App\Model\Jenkins\PullRequest\PullRequest;

/**
 * @author    Alexandre Hocquard <alexandre.hocquard@akeneo.com>
 * @copyright 2018 Akeneo SAS (http://www.akeneo.com)
 * @license   http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */
class Build
{
    /** @var PullRequest */
    private $pullRequest;

    /** @var BuildUri */
    private $buildUri;

    /** @var string */
    private $fullName;

    /** @var string */
    private $result;

    /** @var string */
    private $rawResponse;

    /** @var int */
    private $buildNumber;

    /** @var int */
    private $duration;

    /** @var \DateTimeInterface */
    private $date;

    /** @var int */
    private $numberFailedTests;

    /** @var int */
    private $numberSkipTests;

    /** @var int */
    private $numberSuccessTests;
}
