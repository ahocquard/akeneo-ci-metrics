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
}
