<?php

declare(strict_types=1);

namespace App\Infrastructure\Persistence\API;

use App\Model\Jenkins\Run\Exception\RunSaveException;
use App\Model\Jenkins\Run\Run;
use App\Model\Jenkins\Run\SaveableRunRepository;
use GuzzleHttp\ClientInterface;

/**
 * @author    Alexandre Hocquard <alexandre.hocquard@akeneo.com>
 * @copyright 2018 Akeneo SAS (http://www.akeneo.com)
 * @license   http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */
class InfluxDBApiRunRepository implements SaveableRunRepository
{
    /** @var ClientInterface */
    private $client;

    /** @var string */
    private $databaseName;

    /**
     * @param ClientInterface $client
     */
    public function __construct(ClientInterface $client, string $databaseName)
    {
        $this->client = $client;
        $this->databaseName = $databaseName;
    }

    /**
     * {@inheritDoc}
     */
    public function saveRuns(array $runs): void
    {
        $body = '';
        foreach ($runs as $run) {
            $body .= 'runDuration,';
            $body .= sprintf('identifier=%s,', $run->identifier());
            $body .= sprintf('name="%s",', $run->name());
            $body .= sprintf('state=%s,', $run->state());
            $body .= sprintf('result=%s,', $run->result());
            $body .= sprintf('pipeline=%s,', $run->pipelineName()->value());
            $body .= sprintf('is_pull_request=%s', $run->isPullRequestRun() ? 'true' : 'false');
            $body .= sprintf(' duration=%s', $run->duration());
            $body .= sprintf('failed_tests=%s,', $run->numberOfFailedTests());
            $body .= sprintf('skipped_tests=%s,', $run->numberOfSkippedTests());
            $body .= sprintf('succeeded_tests=%s,', $run->numberOfSucceededTests());
            $body .= sprintf('total_tests=%s', $run->numberOfTests());
            $body .= sprintf(' %s %s', $run->startTimestamp() * 1000000000, PHP_EOL);
        }

        $response = $this->client->request('POST', sprintf('write?db=%s', $this->databaseName), ['body' => $body]);
        if (200 !== $response->getStatusCode()) {
            throw new RunSaveException(sprintf('A problem occurred during save of runs: "%s"', $response->getBody()->getContents()));
        }
    }

    /**
     * {@inheritDoc}
     */
    public function hasRun(Run $run): bool
    {
        return false;
    }
}
