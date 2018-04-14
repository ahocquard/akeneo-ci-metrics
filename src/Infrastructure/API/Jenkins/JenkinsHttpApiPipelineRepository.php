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
class JenkinsHttpApiPipelineRepository implements GettablePipelineRepository
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
                $branches[] = new Branch(
                    $pipelineName,
                    new BranchName($rawDataBranch['name'])
                );
            }
            $start += $limit;
        } while (!empty($rawDataRuns));

        return new Pipeline($pipelineName, $branches);
    }
}
