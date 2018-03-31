<?php

declare(strict_types=1);

namespace tests\integration\Persistence\Sql;

use App\Model\Jenkins\Branch\BranchName;
use App\Model\Jenkins\Pipeline\PipelineName;
use App\Model\Jenkins\Run\Run;
use App\Model\Jenkins\Run\RunId;
use App\Model\Jenkins\Test\ListableTestRepository;
use App\Model\Jenkins\Test\Test;
use App\Model\Jenkins\Test\TestName;
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
class JenkinsHttpApiTestRepositoryIntegration extends KernelTestCase
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
            '/blue/rest/organizations/jenkins/pipelines/akeneo/pim-community-dev/branches/master/runs/1/tests',
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

    public function test_get_tests_for_a_given_run()
    {
        $run = new Run(
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
        $repository = $this->getTestRepository();
        $tests = $repository->listTestsFrom($run);

        $test1 = new Test(
            new PipelineName('pim-community-dev'),
            new BranchName('master'),
            new RunId('1'),
            new TestName('Test / php-cs-fixer / All OK – PHP CS Fixer'),
            \DateTime::createFromFormat('Y-m-d\TH:i:s.uO', '2018-03-24T03:40:02.00+000'),
            0
        );

        $test2 = new Test(
            new PipelineName('pim-community-dev'),
            new BranchName('master'),
            new RunId('1'),
            new TestName('Test / phpspec / it is initializable – spec\\Akeneo\\Component\\Analytics\\ChainedDataCollectorSpec'),
            \DateTime::createFromFormat('Y-m-d\TH:i:s.uO', '2018-03-24T03:40:02.00+000'),
            0.1762120
        );

        Assert::assertEquals([$test1, $test2], $tests);
    }

    private function getTestRepository(): ListableTestRepository
    {
        return static::$kernel->getContainer()->get('test.App\Infrastructure\API\Jenkins\JenkinsHttpTestPipelineRepository');
    }

    private function getFirstPage(): string
    {
        return <<<'JSON'
            [
                {
                    "_class" : "io.jenkins.blueocean.service.embedded.rest.junit.BlueJUnitTestResult",
                    "duration" : 0.0,
                    "name" : "Test / php-cs-fixer / All OK – PHP CS Fixer"
                },
                {
                    "_class" : "io.jenkins.blueocean.service.embedded.rest.junit.BlueJUnitTestResult",
                    "duration" : 0.176212,
                    "name" : "Test / phpspec / it is initializable – spec\\Akeneo\\Component\\Analytics\\ChainedDataCollectorSpec"
                }
            ]
JSON;
    }
}
