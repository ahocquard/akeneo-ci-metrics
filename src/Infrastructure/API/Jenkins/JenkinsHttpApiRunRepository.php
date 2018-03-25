<?php

declare(strict_types=1);

namespace App\Infrastructure\API\Jenkins;

use App\Model\Jenkins\Branch\Branch;
use App\Model\Jenkins\Branch\BranchName;
use App\Model\Jenkins\Pipeline\Pipeline;
use App\Model\Jenkins\Pipeline\PipelineName;
use App\Model\Jenkins\Pipeline\PipelineRepository;
use App\Model\Jenkins\Run\Run;
use App\Model\Jenkins\Run\RunId;
use App\Model\Jenkins\Test\Test;
use App\Model\Jenkins\Test\TestId;
use GuzzleHttp\ClientInterface;

/**
 * @author    Alexandre Hocquard <alexandre.hocquard@akeneo.com>
 * @copyright 2018 Akeneo SAS (http://www.akeneo.com)
 * @license   http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */
class JenkinsHttpApiRunRepository implements PipelineRepository
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
    public function getPipeline(PipelineName $pipelineName): Pipeline
    {
        $branches = [];
        $start = 0;
        $limit = 100;

        do {
            $response = $this->client->request(
                'GET',
                sprintf('%s/branches', $pipelineName->value()),
                [
                    'query' => [
                        'start' => $start,
                        'limit' => $limit,
                        'tree' => 'name'
                    ]
                ]
            );
            $body = $response->getBody()->getContents();

            $rawDataBranches = json_decode($body, true);
            foreach ($rawDataBranches as $rawDataBranch) {
                $branchName = new BranchName($rawDataBranch['name']);
                $branches[] = new Branch(
                    $pipelineName,
                    $branchName,
                    $this->listRunsFrom($pipelineName, $branchName)
                );
                break;
            }
            $start += $limit;
        } while (!empty($rawDataRuns));

        return new Pipeline($pipelineName, $branches);
    }

    private function listRunsFrom(PipelineName $pipelineName, BranchName $branchName): array
    {
        $runs = [];
        $start = 0;
        $limit = 100;

        do {
            try {
                $response = $this->client->request(
                    'GET',
                    sprintf('%s/branches/%s/runs', $pipelineName->value(), $branchName->value()),
                    [
                        'query' => [
                            'start' => $start,
                            'limit' => $limit
                        ]
                    ]
                );
            } catch (\Exception $e) {
                $start += $limit;

                continue;
            }

            $body = $response->getBody()->getContents();

            $rawDataRuns = json_decode($body, true);
            foreach ($rawDataRuns as $rawDataRun) {
                $runId = new RunId($rawDataRun['id']);

                $run = new Run(
                    $pipelineName,
                    $branchName,
                    $runId,
                    $this->listTestsFrom($pipelineName, $branchName, $runId),
                    $rawDataRun['result'],
                    $rawDataRun['state'],
                    intval($rawDataRun['durationInMillis']/1000) ?? -1,
                    null !== $rawDataRun['enQueueTime'] ? \DateTime::createFromFormat('Y-m-d\TH:i:s.uO', $rawDataRun['enQueueTime']) : null,
                    null !== $rawDataRun['startTime'] ? \DateTime::createFromFormat('Y-m-d\TH:i:s.uO', $rawDataRun['startTime']) : null,
                    null !== $rawDataRun['endTime'] ? \DateTime::createFromFormat('Y-m-d\TH:i:s.uO', $rawDataRun['endTime']) : null,
                    $rawDataRun['testSummary']['failed'] ?? -1,
                    $rawDataRun['testSummary']['skipped'] ?? -1,
                    $rawDataRun['testSummary']['passed'] ?? -1,
                    $rawDataRun['testSummary']['total'] ?? -1
                );

                $runs[] = $run;
            }
            $start += $limit;
        } while (!empty($rawDataRuns));

        return $runs;
    }

    private function listTestsFrom(PipelineName $pipelineName, BranchName $branchName, RunId $runId): array
    {
        $tests = [];
        $start = 0;
        $limit = 100;

        do {
            try {
                $response = $this->client->request(
                    'GET',
                    sprintf('%s/branches/%s/runs/%s/tests', $pipelineName->value(), $branchName->value(), $runId->value()),
                    [
                        'query' => [
                            'start' => $start,
                            'limit' => $limit,
                            'tree' => 'duration,id'
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
                    $pipelineName,
                    $branchName,
                    $runId,
                    new TestId($rawDataRun['id']),
                    $rawDataRun['duration']
                );

                $tests[] = $test;
            }
            $start += $limit;
        } while (!empty($rawDataTests));

        return $tests;
    }
}
