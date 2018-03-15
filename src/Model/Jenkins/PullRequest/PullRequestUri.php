<?php

declare(strict_types=1);

namespace App\Model\Jenkins\PullRequest;

/**
 * Represents the API URI of a Pull Request.
 *
 * @author    Alexandre Hocquard <alexandre.hocquard@akeneo.com>
 * @copyright 2018 Akeneo SAS (http://www.akeneo.com)
 * @license   http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */
class PullRequestUri
{
    /** @var string */
    private $uri;

    /**
     * @param string $uri
     */
    public function __construct($uri)
    {
        $this->uri = $uri;
    }
}
