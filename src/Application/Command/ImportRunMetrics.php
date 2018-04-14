<?php

declare(strict_types=1);

namespace App\Application\Command;

use App\Model\Jenkins\Pipeline\PipelineName;

/**
 * @author Alexandre Hocquard <alexandre.hocquard@akeneo.com>
 */
class ImportRunMetrics
{
    /** @var PipelineName[] */
    public $pipelineNames;
}
