<?php

declare(strict_types=1);

namespace App\Model\Jenkins\Pipeline;

use App\Model\Jenkins\Branch\Branch;

/**
 * @author    Alexandre Hocquard <alexandre.hocquard@akeneo.com>
 * @copyright 2018 Akeneo SAS (http://www.akeneo.com)
 * @license   http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */
class Pipeline
{
    /** @var PipelineName */
    private $name;

    /** @var Branch[] */
    private $branches;

    /**
     * @param PipelineName $name
     * @param Branch[]     $branches
     */
    public function __construct(PipelineName $name, array $branches)
    {
        $this->name = $name;
        $this->branches = $branches;
    }

    public function name(): PipelineName
    {
        return $this->name;
    }

    public function branches(): array
    {
        return $this->branches;
    }
}
