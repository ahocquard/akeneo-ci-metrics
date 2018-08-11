<?php

declare(strict_types=1);

namespace spec\App\Infrastructure\API\Jenkins;

use App\Infrastructure\API\Jenkins\JenkinsHttpApiRunRepository;
use App\Model\Jenkins\Branch\Branch;
use App\Model\Jenkins\Branch\BranchName;
use App\Model\Jenkins\Pipeline\PipelineName;
use App\Model\Jenkins\Run\Run;
use App\Model\Jenkins\Run\RunId;
use GuzzleHttp\ClientInterface;
use PhpSpec\ObjectBehavior;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\StreamInterface;

class JenkinsHttpApiRunRepositorySpec extends ObjectBehavior
{

    function let(ClientInterface $client) {
        $this->beConstructedWith($client);
    }

    function it_is_initializable()
    {
        $this->shouldHaveType(JenkinsHttpApiRunRepository::class);
    }

    function it_list_runs_for_a_given_branch(
        $client,
        ResponseInterface $firstPageResponse,
        ResponseInterface $secondPageResponse,
        ResponseInterface $thirdPageResponse,
        StreamInterface $firstPageStream,
        StreamInterface $secondPageStream,
        StreamInterface $thirdPageStream,
        ResponseInterface $summaryResponse,
        StreamInterface $summaryStream
    ) {
        $client->request('GET', 'pim-community-dev/branches/PR-7845/runs',[
            'query' => [
                'start' => 0,
                'limit' => 100,
                'tree' => 'id,result,state,durationInMillis,enQueueTime,startTime,endTime,testSummary[failed,skipped,passed,total]'
             ]
         ])->willReturn($firstPageResponse);
        $client->request('GET', 'pim-community-dev/branches/PR-7845/runs',[
            'query' => [
                'start' => 100,
                'limit' => 100,
                'tree' => 'id,result,state,durationInMillis,enQueueTime,startTime,endTime,testSummary[failed,skipped,passed,total]'
            ]
        ])->willReturn($secondPageResponse);
        $client->request('GET', 'pim-community-dev/branches/PR-7845/runs',[
            'query' => [
                'start' => 200,
                'limit' => 100,
                'tree' => 'id,result,state,durationInMillis,enQueueTime,startTime,endTime,testSummary[failed,skipped,passed,total]'
            ]
        ])->willReturn($thirdPageResponse);

        $firstPageResponse->getBody()->willReturn($firstPageStream);
        $secondPageResponse->getBody()->willReturn($secondPageStream);
        $thirdPageResponse->getBody()->willReturn($thirdPageStream);
        $firstPageStream->getContents()->willReturn($this->firstPage());
        $secondPageStream->getContents()->willReturn($this->secondPage());
        $thirdPageStream->getContents()->willReturn('[]');

        $client->request('GET', 'pim-community-dev/branches/PR-7845/runs/1/blueTestSummary')->willReturn($summaryResponse);
        $client->request('GET', 'pim-community-dev/branches/PR-7845/runs/2/blueTestSummary')->willReturn($summaryResponse);
        $summaryResponse->getBody()->willReturn($summaryStream);
        $summaryStream->getContents()->willReturn($this->summary());

        $run1 = new Run(
            new PipelineName('pim-community-dev'),
            new BranchName('PR-7845'),
            new RunId('1'),
            'ABORTED',
            'FINISHED',
            432031,
            \DateTime::createFromFormat('Y-m-d\TH:i:s.uO', '2018-03-21T09:10:04.431+0000'),
            \DateTime::createFromFormat('Y-m-d\TH:i:s.uO', '2018-03-21T09:10:04.445+0000'),
            \DateTime::createFromFormat('Y-m-d\TH:i:s.uO', '2018-03-26T09:10:36.345+0000'),
            1,
            0,
            6127,
            6128
        );

        $run2 = new Run(
            new PipelineName('pim-community-dev'),
            new BranchName('PR-7845'),
            new RunId('2'),
            'ABORTED',
            'FINISHED',
            432070,
            \DateTime::createFromFormat('Y-m-d\TH:i:s.uO', '2018-03-21T11:15:21.639+0000'),
            \DateTime::createFromFormat('Y-m-d\TH:i:s.uO', '2018-03-21T11:15:21.660+0000'),
            \DateTime::createFromFormat('Y-m-d\TH:i:s.uO', '2018-03-26T11:16:32.528+0000'),
            1,
            0,
            6127,
            6128
        );

        $this->listRunsFrom(
            new Branch(
                new PipelineName('pim-community-dev'),
                new BranchName('PR-7845')
            ))
            ->shouldBeLike([$run1, $run2]);
    }

    private function firstPage(): string
    {
        return <<<'JSON'
            [
                {
                    "_class" : "io.jenkins.blueocean.rest.impl.pipeline.PipelineRunImpl",
                    "durationInMillis" : 432031900,
                    "enQueueTime" : "2018-03-21T09:10:04.431+0000",
                    "endTime" : "2018-03-26T09:10:36.345+0000",
                    "id" : "1",
                    "result" : "ABORTED",
                    "startTime" : "2018-03-21T09:10:04.445+0000",
                    "state" : "FINISHED"
                }
            ]
JSON;
    }

    private function secondPage(): string
    {
        return <<<'JSON'
            [
                {
                    "_class" : "io.jenkins.blueocean.rest.impl.pipeline.PipelineRunImpl",
                    "durationInMillis" : 432070868,
                    "enQueueTime" : "2018-03-21T11:15:21.639+0000",
                    "endTime" : "2018-03-26T11:16:32.528+0000",
                    "id" : "2",
                    "result" : "ABORTED",
                    "startTime" : "2018-03-21T11:15:21.660+0000",
                    "state" : "FINISHED"
                } 
              
            ]
JSON;
    }

    private function summary(): string
    {
        return <<<'JSON'
            {
              "_class" : "io.jenkins.blueocean.rest.model.BlueTestSummary",
              "_links" : {
                "self" : {
                  "_class" : "io.jenkins.blueocean.rest.hal.Link",
                  "href" : "/blue/rest/organizations/jenkins/pipelines/akeneo/pipelines/pim-community-dev/branches/PR-7845/runs/1/blueTestSummary/"
                }
              },
              "existingFailed" : 0,
              "failed" : 1,
              "fixed" : 0,
              "passed" : 6127,
              "regressions" : 1,
              "skipped" : 0,
              "total" : 6128
            }
JSON;
    }

}
