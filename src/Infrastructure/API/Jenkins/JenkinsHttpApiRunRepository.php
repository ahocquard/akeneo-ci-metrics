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
 * @author Alexandre Hocquard <alexandre.hocquard@akeneo.com>
 */
class JenkinsHttpApiRunRepository implements ListableRunRepository
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
    public function listRunsFrom(Branch $branch): array
    {
        $runs = [];
        $start = 0;
        $limit = 100;

        do {
            try {
                $response = $this->client->request(
                    'GET',
                    sprintf('%s/branches/%s/runs', $branch->pipelineName()->value(), $branch->name()->value()),
                    [
                        'query' => [
                            'start' => $start,
                            'limit' => $limit,
                            'tree' => 'id,result,state,durationInMillis,enQueueTime,startTime,endTime,testSummary[failed,skipped,passed,total]'
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
                $summary = $this->summaryForRun($branch, new RunId($rawDataRun['id']));

                $run = new Run(
                    $branch->pipelineName(),
                    $branch->name(),
                    new RunId($rawDataRun['id']),
                    $rawDataRun['result'],
                    $rawDataRun['state'],
                    intval($rawDataRun['durationInMillis']/1000) ?? -1,
                    null !== $rawDataRun['enQueueTime'] ? \DateTime::createFromFormat('Y-m-d\TH:i:s.uO', $rawDataRun['enQueueTime']) : null,
                    null !== $rawDataRun['startTime'] ? \DateTime::createFromFormat('Y-m-d\TH:i:s.uO', $rawDataRun['startTime']) : null,
                    null !== $rawDataRun['endTime'] ? \DateTime::createFromFormat('Y-m-d\TH:i:s.uO', $rawDataRun['endTime']) : null,
                    $summary['failed'] ?? -1,
                    $summary['skipped'] ?? -1,
                    $summary['passed'] ?? -1,
                    $summary['total'] ?? -1
                );

                $runs[] = $run;
            }
            $start += $limit;
        } while (!empty($rawDataRuns));

        return $runs;
    }

    private function summaryForRun(Branch $branch, RunId $runId)
    {
        $data = [
            'failed' => -1,
            'skipped' => -1,
            'passed' => -1,
            'total' => -1
        ];

        try {
            $response = $this->client->request(
                'GET',
                sprintf(
                    '%s/branches/%s/runs/%s/blueTestSummary',
                    $branch->pipelineName()->value(), $branch->name()->value(), $runId->value()
                )
            );

            $body = $response->getBody()->getContents();
            $rawData = json_decode($body, true);
            $data = array_merge($data, $rawData);
        } catch (\Exception $e) {
        }

        return $data;
    }
}
