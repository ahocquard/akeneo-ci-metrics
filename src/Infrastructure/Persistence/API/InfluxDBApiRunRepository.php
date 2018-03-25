<?php

declare(strict_types=1);

namespace App\Infrastructure\Persistence\API;

use App\Model\Jenkins\Run\Exception\RunSaveException;
use App\Model\Jenkins\Run\Run;
use App\Model\Jenkins\Run\RunRepository;
use GuzzleHttp\ClientInterface;

/**
 * @author    Alexandre Hocquard <alexandre.hocquard@akeneo.com>
 * @copyright 2018 Akeneo SAS (http://www.akeneo.com)
 * @license   http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */
class InfluxDBApiRunRepository implements RunRepository
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
            $body .= 'run_duration,';
            $body .= sprintf('pipeline=%s,', $run->pipelineName()->value());
            $body .= sprintf('branch=%s,', $run->branchName()->value());
            $body .= sprintf('id=%s,', $run->id()->value());
            $body .= sprintf('state=%s,', $run->state());
            $body .= sprintf('result=%s,', $run->result());
            $body .= sprintf('is_pull_request=%s', $run->isPullRequestRun() ? 'true' : 'false');
            $body .= sprintf(' duration_in_seconds=%s,', $run->duration());
            $body .= sprintf('failed_tests=%s,', $run->numberOfFailedTests());
            $body .= sprintf('skipped_tests=%s,', $run->numberOfSkippedTests());
            $body .= sprintf('succeeded_tests=%s,', $run->numberOfSucceededTests());
            $body .= sprintf('total_tests=%s', $run->numberOfTests());
            $body .= sprintf(' %s %s', $run->startTimestamp() * 1000000000, PHP_EOL);
        }

        $response = $this->client->request(
            'POST',
            'write',
            [
                'body' => $body,
                'query' => [
                    'db' => $this->databaseName
                ]
            ]
        );
        if (204 !== $response->getStatusCode()) {
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
