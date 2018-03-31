<?php

declare(strict_types=1);

namespace tests\integration\Persistence\Sql;

use App\Model\Jenkins\Pipeline\GettablePipelineRepository;
use App\Model\Jenkins\Pipeline\PipelineName;
use donatj\MockWebServer\MockWebServer;
use donatj\MockWebServer\Response;
use donatj\MockWebServer\ResponseStack;
use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;

/**
 * @author    Alexandre Hocquard <alexandre.hocquard@akeneo.com>
 * @copyright 2018 Akeneo SAS (http://www.akeneo.com)
 * @license   http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */
class JenkinsHttpApiPipelineRepositoryIntegration extends KernelTestCase
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
            '/blue/rest/organizations/jenkins/pipelines/akeneo/pim-community-dev/branches',
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

    public function test_get_a_pipeline()
    {
        $repository = $this->getPipelineRepository();
        $pipeline = $repository->getPipeline(new PipelineName('pim-community-dev'));

        $this->assertEquals('master', $pipeline->branches()[0]->name()->value());
        $this->assertEquals('pim-community-dev', $pipeline->branches()[0]->pipelineName()->value());
        $this->assertEquals('PR-7893', $pipeline->branches()[1]->name()->value());
        $this->assertEquals('pim-community-dev', $pipeline->branches()[1]->pipelineName()->value());
    }

    private function getPipelineRepository(): GettablePipelineRepository
    {
        return static::$kernel->getContainer()->get('test.App\Infrastructure\API\Jenkins\JenkinsHttpApiPipelineRepository');
    }

    private function getFirstPage(): string
    {
        return <<<JSON
            [
                {
                    "_class" : "io.jenkins.blueocean.rest.impl.pipeline.BranchImpl",
                    "name" : "master"
                },
                {
                    "_class" : "io.jenkins.blueocean.rest.impl.pipeline.BranchImpl",
                    "name" : "PR-7893"
                }
            ]
JSON;

    }
}
