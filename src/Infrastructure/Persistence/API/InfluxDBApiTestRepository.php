<?php

declare(strict_types=1);

namespace App\Infrastructure\Persistence\API;

use App\Model\Jenkins\Run\Exception\RunSaveException;
use App\Model\Jenkins\Run\Run;
use App\Model\Jenkins\Run\RunRepository;
use App\Model\Jenkins\Test\TestRepository;
use GuzzleHttp\ClientInterface;

/**
 * @author    Alexandre Hocquard <alexandre.hocquard@akeneo.com>
 * @copyright 2018 Akeneo SAS (http://www.akeneo.com)
 * @license   http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */
class InfluxDBApiTestRepository implements TestRepository
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

    public function saveTests(array $tests): void
    {
        $body = '';
        foreach ($tests as $test) {
            $body .= 'test_duration';
            $body .= sprintf('pipeline=%s,', $test->pipelineName()->value());
            $body .= sprintf('branch=%s,', $test->branchName()->value());
            $body .= sprintf('run_id=%s,', $test->runId()->value());
            $body .= sprintf('id=%s,', $test->id());
            $body .= sprintf('type=%s,', $test->type());
            $body .= sprintf(' duration=%s', $test->duration());
            $body .= sprintf(' 0 %s', PHP_EOL);
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
        if (200 !== $response->getStatusCode()) {
            throw new RunSaveException(sprintf('A problem occurred during save of runs: "%s"', $response->getBody()->getContents()));
        }
    }
}
