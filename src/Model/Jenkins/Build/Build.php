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

    /**
     * @param PullRequest        $pullRequest
     * @param BuildUri           $buildUri
     * @param string             $fullName
     * @param null|string        $result
     * @param string             $rawResponse
     * @param int                $buildNumber
     * @param int                $duration
     * @param \DateTimeInterface $date
     * @param int                $numberFailedTests
     * @param int                $numberSkipTests
     * @param int                $numberSuccessTests
     */
    public function __construct(
        PullRequest $pullRequest,
        BuildUri $buildUri,
        string $fullName,
        ?string $result,
        string $rawResponse,
        int $buildNumber,
        int $duration,
        \DateTimeInterface $date,
        int $numberFailedTests,
        int $numberSkipTests,
        int $numberSuccessTests
    ) {
        $this->pullRequest = $pullRequest;
        $this->buildUri = $buildUri;
        $this->fullName = $fullName;
        $this->result = $result;
        $this->rawResponse = $rawResponse;
        $this->buildNumber = $buildNumber;
        $this->duration = $duration;
        $this->date = $date;
        $this->numberFailedTests = $numberFailedTests;
        $this->numberSkipTests = $numberSkipTests;
        $this->numberSuccessTests = $numberSuccessTests;
    }

    public function isBuildFinished(): bool
    {
        return null !== $this->result;
    }
}
