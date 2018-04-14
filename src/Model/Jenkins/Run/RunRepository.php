<?php

declare(strict_types=1);

namespace App\Model\Jenkins\Run;

use App\Model\Jenkins\Run\Exception\RunSaveException;

/**
 * @author Alexandre Hocquard <alexandre.hocquard@akeneo.com>
 */
interface RunRepository
{
    /**
     * @param Run[] $runs
     */
    public function saveRuns(array $runs): void;

    /**
     * @param Run $run
     *
     * @throws RunSaveException
     *
     * @return bool
     */
    public function hasRun(Run $run): bool;

}
