<?php

declare(strict_types=1);

namespace App\Model\Jenkins\PullRequest;

/**
 * Represents a pull request created in Github.
 * Do note that it can be a branch name as well (1.7, 2.0, etc).
 *
 * @author    Alexandre Hocquard <alexandre.hocquard@akeneo.com>
 * @copyright 2018 Akeneo SAS (http://www.akeneo.com)
 * @license   http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */
class PullRequest
{
    /** @var string */
    private $fullName;

    /** @var array */
    private $buildUris;

    /**
     * @param string $fullName
     * @param array  $buildUris
     */
    public function __construct($fullName, array $buildUris)
    {
        $this->fullName = $fullName;
        $this->buildUris = $buildUris;
    }
}
