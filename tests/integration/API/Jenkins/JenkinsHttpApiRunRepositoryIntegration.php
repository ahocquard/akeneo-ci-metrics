<?php

declare(strict_types=1);

namespace tests\integration\Persistence\Sql;

use App\Model\Jenkins\Branch\Branch;
use App\Model\Jenkins\Branch\BranchName;
use App\Model\Jenkins\Pipeline\PipelineName;
use App\Model\Jenkins\Run\ListableRunRepository;
use App\Model\Jenkins\Run\Run;
use App\Model\Jenkins\Run\RunId;
use donatj\MockWebServer\MockWebServer;
use donatj\MockWebServer\Response;
use donatj\MockWebServer\ResponseStack;
use PHPUnit\Framework\Assert;
use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;

/**
 * @author    Alexandre Hocquard <alexandre.hocquard@akeneo.com>
 * @copyright 2018 Akeneo SAS (http://www.akeneo.com)
 * @license   http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */
class JenkinsHttpApiRunRepositoryIntegration extends KernelTestCase
{
    /** @var MockWebServer */
    private $server;

    protected function setUp()
    {
        parent::setUp();
        static::bootKernel(['debug' => false]);

        $this->server = new MockWebServer(8081);
        $this->server->start();

        $this->server->setResponseOfPath(
            '/blue/rest/organizations/jenkins/pipelines/akeneo/pim-community-dev/branches/master/runs',
            new ResponseStack(
                new Response($this->getFirstPage()),
                new Response('[]')
            )
        );
    }

    protected function tearDown() {
        parent::tearDown();
        $this->server->stop();
    }

    public function test_get_runs_for_a_given_branch()
    {
        $repository = $this->getRunRepository();
        $runs = $repository->listRunsFrom(
            new Branch(
                new PipelineName('pim-community-dev'),
                new BranchName('master')
            )
        );

        $run1 = new Run(
            new PipelineName('pim-community-dev'),
            new BranchName('master'),
            new RunId('1'),
            'SUCCESS',
            'FINISHED',
            7427,
            \DateTime::createFromFormat('Y-m-d\TH:i:s.uO', '2018-03-24T03:40:01.948+0000'),
            \DateTime::createFromFormat('Y-m-d\TH:i:s.uO', '2018-03-24T03:40:02.522+000'),
            \DateTime::createFromFormat('Y-m-d\TH:i:s.uO', '2018-03-24T05:43:49.778+0000'),
            0,
            0,
            8174,
            8174
        );
        $run2 = new Run(
            new PipelineName('pim-community-dev'),
            new BranchName('master'),
            new RunId('2'),
            'FAILURE',
            'FINISHED',
            13376,
            \DateTime::createFromFormat('Y-m-d\TH:i:s.uO', '2018-03-30T03:40:02.120+0000'),
            \DateTime::createFromFormat('Y-m-d\TH:i:s.uO', '2018-03-30T03:40:02.523+0000'),
            \DateTime::createFromFormat('Y-m-d\TH:i:s.uO', '2018-03-30T07:22:59.295+0000'),
            1,
            0,
            8173,
            8174
        );


        Assert::assertEquals([$run2, $run1], $runs);
    }

    private function getRunRepository(): ListableRunRepository
    {
        return static::$kernel->getContainer()->get('test.App\Infrastructure\API\Jenkins\JenkinsHttpRunPipelineRepository');
    }

    private function getFirstPage(): string
    {
        return <<<JSON
            [
                {
                    "_class" : "io.jenkins.blueocean.rest.impl.pipeline.PipelineRunImpl",
                    "durationInMillis" : 13376772,
                    "enQueueTime" : "2018-03-30T03:40:02.120+0000",
                    "endTime" : "2018-03-30T07:22:59.295+0000",
                    "id" : "2",
                    "result" : "FAILURE",
                    "startTime" : "2018-03-30T03:40:02.523+0000",
                    "state" : "FINISHED",
                    "testSummary" : {
                        "failed" : 1,
                        "passed" : 8173,
                        "skipped" : 0,
                        "total" : 8174
                    }
                },
                {
                    "_class" : "io.jenkins.blueocean.rest.impl.pipeline.PipelineRunImpl",
                    "durationInMillis" : 7427256,
                    "enQueueTime" : "2018-03-24T03:40:01.948+0000",
                    "endTime" : "2018-03-24T05:43:49.778+0000",
                    "id" : "1",
                    "result" : "SUCCESS",
                    "startTime" : "2018-03-24T03:40:02.522+0000",
                    "state" : "FINISHED",
                    "testSummary" : {
                        "failed" : 0,
                        "passed" : 8174,
                        "skipped" : 0,
                        "total" : 8174
                    }
                }
            ]
JSON;
    }
}
