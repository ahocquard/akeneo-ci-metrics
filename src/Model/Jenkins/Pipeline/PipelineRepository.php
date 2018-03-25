<?php

declare(strict_types=1);

namespace App\Model\Jenkins\Pipeline;

/**
 * @author    Alexandre Hocquard <alexandre.hocquard@akeneo.com>
 * @copyright 2018 Akeneo SAS (http://www.akeneo.com)
 * @license   http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */
interface PipelineRepository
{
    /**
     * @param PipelineName $pipelineName
     *
     * @return Pipeline
     */
    public function getPipeline(PipelineName $pipelineName): Pipeline;
}
