<?php

declare(strict_types=1);

namespace App\Infrastructure\Delivery\API;

use App\Model\Jenkins\Pipeline\PipelineName;
use App\Model\Jenkins\Run\ListableRunRepository;
use App\Model\Jenkins\Run\Run;
use App\Model\Jenkins\Step\StepUri;
use GuzzleHttp\ClientInterface;

/**
 * @author    Alexandre Hocquard <alexandre.hocquard@akeneo.com>
 * @copyright 2018 Akeneo SAS (http://www.akeneo.com)
 * @license   http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */
class JenkinsApiRunRepository implements ListableRunRepository
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

    public function listRunsFrom(PipelineName $pipelineName): iterable
    {
        $runs = [];
        $start = 0;

        do {
            $response = $this->client->request('GET', sprintf('%s/runs/?start=%s', $pipelineName->value(), $start));
            $body = $response->getBody()->getContents();

            $rawDataRuns = json_decode($body, true);
            foreach ($rawDataRuns as $rawDataRun) {
                $run = new Run(
                    $rawDataRun['pipeline'],
                    (int) $rawDataRun['id'],
                    $pipelineName,
                    $rawDataRun['result'],
                    $rawDataRun['state'],
                    $rawDataRun['durationInMillis'] ?? -1,
                    null !== $rawDataRun['enQueueTime'] ? \DateTime::createFromFormat('Y-m-d\TH:i:s.uO', $rawDataRun['enQueueTime']) : null,
                    null !== $rawDataRun['startTime'] ? \DateTime::createFromFormat('Y-m-d\TH:i:s.uO', $rawDataRun['startTime']) : null,
                    null !== $rawDataRun['endTime'] ? \DateTime::createFromFormat('Y-m-d\TH:i:s.uO', $rawDataRun['endTime']) : null,
                    $rawDataRun['testSummary']['failed'] ?? -1,
                    $rawDataRun['testSummary']['skipped'] ?? -1,
                    $rawDataRun['testSummary']['passed'] ?? -1,
                    $rawDataRun['testSummary']['total'] ?? -1,
                    new StepUri($rawDataRun['_links']['steps']['href'])
                );

                $runs[] = $run;
            }
            $start++;
        } while (!empty($rawDataRuns) && $start < 5);

        return $runs;
    }
}
