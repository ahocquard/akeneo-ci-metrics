<?php

declare(strict_types=1);

namespace spec\App\Infrastructure\API\Jenkins;

use App\Infrastructure\API\Jenkins\JenkinsHttpApiTestRepository;
use App\Model\Jenkins\Branch\BranchName;
use App\Model\Jenkins\Pipeline\PipelineName;
use App\Model\Jenkins\Run\Run;
use App\Model\Jenkins\Run\RunId;
use App\Model\Jenkins\Test\Test;
use App\Model\Jenkins\Test\TestName;
use GuzzleHttp\ClientInterface;
use PhpSpec\ObjectBehavior;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\StreamInterface;

class JenkinsHttpApiTestRepositorySpec extends ObjectBehavior
{

    function let(ClientInterface $client) {
        $this->beConstructedWith($client);
    }

    function it_is_initializable()
    {
        $this->shouldHaveType(JenkinsHttpApiTestRepository::class);
    }

    function it_list_tests_for_a_given_run(
        $client,
        ResponseInterface $firstPageResponse,
        ResponseInterface $secondPageResponse,
        ResponseInterface $thirdPageResponse,
        StreamInterface $firstPageStream,
        StreamInterface $secondPageStream,
        StreamInterface $thirdPageStream
    ) {
        $client->request('GET', 'pim-community-dev/branches/PR-7845/runs/1/tests',[
            'query' => [
                'start' => 0,
                'limit' => 10000,
                'tree' => 'duration,name'
             ]
         ])->willReturn($firstPageResponse);
        $client->request('GET', 'pim-community-dev/branches/PR-7845/runs/1/tests',[
            'query' => [
                'start' => 10000,
                'limit' => 10000,
                'tree' => 'duration,name'
            ]
        ])->willReturn($secondPageResponse);
        $client->request('GET', 'pim-community-dev/branches/PR-7845/runs/1/tests',[
            'query' => [
                'start' => 20000,
                'limit' => 10000,
                'tree' => 'duration,name'
            ]
        ])->willReturn($thirdPageResponse);

        $firstPageResponse->getBody()->willReturn($firstPageStream);
        $secondPageResponse->getBody()->willReturn($secondPageStream);
        $thirdPageResponse->getBody()->willReturn($thirdPageStream);
        $firstPageStream->getContents()->willReturn($this->firstPage());
        $secondPageStream->getContents()->willReturn($this->secondPage());
        $thirdPageStream->getContents()->willReturn('[]');

        $run = new Run(
            new PipelineName('pim-community-dev'),
            new BranchName('PR-7845'),
            new RunId('1'),
            'ABORTED',
            'FINISHED',
            432031,
            \DateTime::createFromFormat('Y-m-d\TH:i:s.uO', '2018-03-21T09:10:04.431+0000'),
            \DateTime::createFromFormat('Y-m-d\TH:i:s.uO', '2018-03-21T09:10:04.445+0000'),
            \DateTime::createFromFormat('Y-m-d\TH:i:s.uO', '2018-03-26T09:10:36.345+0000'),
            -1,
            -1,
            -1,
            -1
        );

        $test1 = new Test(
            new PipelineName('pim-community-dev'),
            new BranchName('PR-7845'),
            new RunId('1'),
            new TestName('Test / behat-ce / features/localization/mass-action/edit_common_attributes.feature:27 – Edit common localized attributes of many products at once'),
            \DateTime::createFromFormat('Y-m-d\TH:i:s.uO', '2018-03-21T09:10:04.000+0000'),
            83.501
        );

        $test2 = new Test(
            new PipelineName('pim-community-dev'),
            new BranchName('PR-7845'),
            new RunId('1'),
            new TestName('Test / behat-ce / features/localization/mass-action/edit_common_attributes.feature:96 – Edit common localized attributes of many products at once'),
            \DateTime::createFromFormat('Y-m-d\TH:i:s.uO', '2018-03-21T09:10:04.000+0000'),
            76.45
        );

        $this->listTestsFrom($run)->shouldBeLike([$test1, $test2]);
    }

    private function firstPage(): string
    {
        return <<<'JSON'
            [
                {
                   "_class" : "io.jenkins.blueocean.service.embedded.rest.junit.BlueJUnitTestResult",
                   "duration" : 83.501,
                   "name" : "Test / behat-ce / features/localization/mass-action/edit_common_attributes.feature:27 – Edit common localized attributes of many products at once"
                }
            ]
JSON;
    }

    private function secondPage(): string
    {
        return <<<'JSON'
            [
                {
                  "_class" : "io.jenkins.blueocean.service.embedded.rest.junit.BlueJUnitTestResult",
                  "duration" : 76.45,
                  "name" : "Test / behat-ce / features/localization/mass-action/edit_common_attributes.feature:96 – Edit common localized attributes of many products at once"
                }            
            ]
JSON;
    }
}
