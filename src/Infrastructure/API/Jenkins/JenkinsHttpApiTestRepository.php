<?php

declare(strict_types=1);

namespace App\Infrastructure\API\Jenkins;

use App\Model\Jenkins\Branch\Branch;
use App\Model\Jenkins\Branch\BranchName;
use App\Model\Jenkins\Pipeline\Pipeline;
use App\Model\Jenkins\Pipeline\PipelineName;
use App\Model\Jenkins\Pipeline\GettablePipelineRepository;
use App\Model\Jenkins\Run\ListableRunRepository;
use App\Model\Jenkins\Run\Run;
use App\Model\Jenkins\Run\RunId;
use App\Model\Jenkins\Test\ListableTestRepository;
use App\Model\Jenkins\Test\Test;
use App\Model\Jenkins\Test\TestName;
use GuzzleHttp\ClientInterface;

/**
 * @author    Alexandre Hocquard <alexandre.hocquard@akeneo.com>
 * @copyright 2018 Akeneo SAS (http://www.akeneo.com)
 * @license   http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */
class JenkinsHttpApiTestRepository implements ListableTestRepository
{
    /** @var ClientInterface */
    private $client;

    /**
     * @param ClientInterface $client
     */
    public function __construct(ClientInterface $client)
    {
        $this->client = $client;
    }

    /**
     * {@inheritdoc}
     */
    public function listTestsFrom(Run $run): array
    {
        $tests = [];
        $start = 0;
        $limit = 100;

        do {
            try {
                $response = $this->client->request(
                    'GET',
                    sprintf(
                        '%s/branches/%s/runs/%s/tests',
                        $run->pipelineName()->value(),
                        $run->branchName()->value(),
                        $run->id()->value()
                    ),
                    [
                        'query' => [
                            'start' => $start,
                            'limit' => $limit,
                            'tree' => 'duration,name'
                        ]
                    ]
                );
            } catch (\Exception $e) {
                $start += $limit;

                continue;
            }

            $body = $response->getBody()->getContents();

            $rawDataTests = json_decode($body, true);
            foreach ($rawDataTests as $rawDataRun) {
                $test = new Test(
                    $run->pipelineName(),
                    $run->branchName(),
                    $run->id(),
                    new TestName($rawDataRun['name']),
                    new \DateTimeImmutable('@' . $run->startTimestamp()),
                    $rawDataRun['duration']
                );

                $tests[] = $test;
            }
            $start += $limit;
        } while (!empty($rawDataTests));

        return $tests;
    }
}
