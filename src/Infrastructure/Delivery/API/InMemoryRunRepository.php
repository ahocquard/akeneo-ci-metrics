<?php

declare(strict_types=1);

namespace App\Infrastructure\Delivery\API;

use App\Model\Jenkins\Pipeline\PipelineName;
use App\Model\Jenkins\Run\Run;
use App\Model\Jenkins\Run\SaveableRunRepository;

/**
 * @author    Alexandre Hocquard <alexandre.hocquard@akeneo.com>
 * @copyright 2018 Akeneo SAS (http://www.akeneo.com)
 * @license   http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */
class InMemoryRunRepository
{
    public function listRunsFrom(PipelineName $pipelineName): iterable
    {
        // TODO: Implement listRunsFrom() method.
    }

    public function saveRuns(array $runs): void
    {
        // TODO: Implement saveRuns() method.
    }

    public function hasRun(Run $run): bool
    {
        // TODO: Implement hasRun() method.
    }
}
