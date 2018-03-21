<?php

declare(strict_types=1);

namespace spec\App\Infrastructure\Persistence\API;

use App\Infrastructure\Persistence\API\InfluxDBApiRunRepository;
use App\Model\Jenkins\Pipeline\PipelineName;
use App\Model\Jenkins\Run\Exception\RunSaveException;
use App\Model\Jenkins\Run\Run;
use App\Model\Jenkins\Step\StepUri;
use GuzzleHttp\ClientInterface;
use PhpSpec\ObjectBehavior;
use Prophecy\Argument;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\StreamInterface;

class InfluxDBApiRunRepositorySpec extends ObjectBehavior
{

    function let(ClientInterface $client) {
        $this->beConstructedWith($client, 'akeneo_ci_metrics');
    }

    function it_is_initializable()
    {
        $this->shouldHaveType(InfluxDBApiRunRepository::class);
    }

    function it_save_runs_in_influx_db(
        $client,
        ResponseInterface $response
    ) {

        $run1 = new Run(
            'PR-7845',
            2,
            new PipelineName('pim-community-dev'),
            'UNKNOWN',
            'PAUSED',
            0,
            \DateTime::createFromFormat('Y-m-d\TH:i:s.uO', '2018-03-21T11:15:21.639+0000'),
            \DateTime::createFromFormat('Y-m-d\TH:i:s.uO', '2018-03-21T11:15:21.660+0000'),
            null,
            -1,
            -1,
            -1,
            -1,
            new StepUri('/blue/rest/organizations/jenkins/pipelines/akeneo/pipelines/pim-community-dev/branches/PR-7845/runs/2/steps/')
        );

        $run2 = new Run(
            '2.2',
            28,
            new PipelineName('pim-community-dev'),
            'SUCCESS',
            'FINISHED',
            14046469,
            \DateTime::createFromFormat('Y-m-d\TH:i:s.uO', '2018-03-21T03:20:01.921+0000'),
            \DateTime::createFromFormat('Y-m-d\TH:i:s.uO', '2018-03-21T03:20:02.030+0000'),
            \DateTime::createFromFormat('Y-m-d\TH:i:s.uO', '2018-03-21T07:14:08.499+0000'),
            0,
            0,
            8162,
            8162,
            new StepUri('/blue/rest/organizations/jenkins/pipelines/akeneo/pipelines/pim-community-dev/branches/2.2/runs/28/steps/')
        );

        $body = <<<TEXT
runDuration,identifier=pim-community-dev_PR-7845_2,name="pim-community-dev_PR-7845",state=PAUSED,result=UNKNOWN,pipeline=pim-community-dev,is_pull_request=true duration=0failed_tests=-1,skipped_tests=-1,succeeded_tests=-1,total_tests=-1 1521630921000000000 
runDuration,identifier=pim-community-dev_2.2_28,name="pim-community-dev_2.2",state=FINISHED,result=SUCCESS,pipeline=pim-community-dev,is_pull_request=false duration=14046469failed_tests=0,skipped_tests=0,succeeded_tests=0,total_tests=8162 1521602402000000000 

TEXT;

        $client->request('POST', 'write?db=akeneo_ci_metrics', ['body' => $body])->willreturn($response);
        $response->getStatusCode()->willReturn(200);

        $this->saveRuns([$run1, $run2]);
    }

    function it_throws_exception_when_http_code_not_valid(
        $client,
        ResponseInterface $response,
        StreamInterface $stream
    ) {

        $run1 = new Run(
            'PR-7845',
            2,
            new PipelineName('pim-community-dev'),
            'UNKNOWN',
            'PAUSED',
            0,
            \DateTime::createFromFormat('Y-m-d\TH:i:s.uO', '2018-03-21T11:15:21.639+0000'),
            \DateTime::createFromFormat('Y-m-d\TH:i:s.uO', '2018-03-21T11:15:21.660+0000'),
            null,
            -1,
            -1,
            -1,
            -1,
            new StepUri('/blue/rest/organizations/jenkins/pipelines/akeneo/pipelines/pim-community-dev/branches/PR-7845/runs/2/steps/')
        );

        $body = <<<TEXT
runDuration,identifier=pim-community-dev_PR-7845_2,name="pim-community-dev_PR-7845",state=PAUSED,result=UNKNOWN,pipeline=pim-community-dev,is_pull_request=true duration=0failed_tests=-1,skipped_tests=-1,succeeded_tests=-1,total_tests=-1 1521630921000000000 

TEXT;

        $client->request('POST', 'write?db=akeneo_ci_metrics', ['body' => $body])->willreturn($response);
        $response->getStatusCode()->willReturn(422);
        $response->getBody()->willReturn($stream);
        $stream->getContents()->willReturn('{"error": "An error occured."}');

        $this->shouldThrow(RunSaveException::class)->during('saveRuns', [[$run1]]);
    }
}
