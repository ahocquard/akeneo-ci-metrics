<?php

declare(strict_types=1);

namespace App\Model\Jenkins\Pipeline;

/**
 * @author Alexandre Hocquard <alexandre.hocquard@akeneo.com>
 */
interface GettablePipelineRepository
{
    /**
     * @param PipelineName $pipelineName
     *
     * @return Pipeline
     */
    public function getPipeline(PipelineName $pipelineName): Pipeline;
}
