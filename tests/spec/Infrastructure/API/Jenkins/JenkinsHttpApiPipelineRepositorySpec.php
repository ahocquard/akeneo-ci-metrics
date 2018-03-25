<?php

declare(strict_types=1);

namespace spec\App\Infrastructure\API\Jenkins;

use App\Infrastructure\API\Jenkins\JenkinsHttpApiPipelineRepository;
use App\Model\Jenkins\Branch\Branch;
use App\Model\Jenkins\Branch\BranchName;
use App\Model\Jenkins\Pipeline\Pipeline;
use App\Model\Jenkins\Pipeline\PipelineName;
use GuzzleHttp\ClientInterface;
use PhpSpec\ObjectBehavior;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\StreamInterface;

class JenkinsHttpApiPipelineRepositorySpec extends ObjectBehavior
{

    function let(ClientInterface $client) {
        $this->beConstructedWith($client);
    }

    function it_is_initializable()
    {
        $this->shouldHaveType(JenkinsHttpApiPipelineRepository::class);
    }

    function it_returns_a_pipeline_for_a_given_pipeline_name(
        $client,
        ResponseInterface $firstPageResponse,
        ResponseInterface $secondPageResponse,
        StreamInterface $firstPageStream,
        StreamInterface $secondPageStream
    ) {
        $client->request('GET', 'pim-community-dev/branches',[
            'query' => [
                'start' => 0,
                'limit' => 100,
                'tree' => 'name'
             ]
         ])->willReturn($firstPageResponse);
        $client->request('GET', 'pim-community-dev/branches',[
            'query' => [
                'start' => 100,
                'limit' => 100,
                'tree' => 'name'
            ]
        ])->willReturn($secondPageResponse);

        $firstPageResponse->getBody()->willReturn($firstPageStream);
        $secondPageResponse->getBody()->willReturn($secondPageStream);
        $firstPageStream->getContents()->willReturn($this->firstPage());
        $secondPageStream->getContents()->willReturn('[]');

        $pipeline = new Pipeline(
            new PipelineName('pim-community-dev'),
            [
                new Branch(new PipelineName('pim-community-dev'), new BranchName('2.2')),
                new Branch(new PipelineName('pim-community-dev'), new BranchName('PR-7893'))
            ]
        );
        $this->getPipeline(new PipelineName('pim-community-dev'))->shouldBeLike($pipeline);
    }

    private function firstPage(): string
    {
        return <<<'JSON'
            [
                {
                    "_class" : "io.jenkins.blueocean.rest.impl.pipeline.BranchImpl",
                    "name" : "2.2"
                },
                {
                    "_class" : "io.jenkins.blueocean.rest.impl.pipeline.BranchImpl",
                    "name" : "PR-7893"
                }
            ]
JSON;
    }
}
