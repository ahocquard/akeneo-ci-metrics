<?php

declare(strict_types=1);

namespace tests\integration\Persistence\Sql;

use App\Model\Jenkins\Branch\BranchName;
use App\Model\Jenkins\Pipeline\PipelineName;
use App\Model\Jenkins\Run\Run;
use App\Model\Jenkins\Run\RunId;
use App\Model\Jenkins\Test\Test;
use App\Model\Jenkins\Test\TestName;
use App\Model\Jenkins\Test\TestRepository;
use Doctrine\DBAL\Driver\Connection;
use PHPUnit\Framework\Assert;
use Symfony\Bundle\FrameworkBundle\Console\Application;
use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;
use Symfony\Component\Console\Input\ArrayInput;
use Symfony\Component\Console\Output\NullOutput;

/**
 * @author    Alexandre Hocquard <alexandre.hocquard@akeneo.com>
 * @copyright 2018 Akeneo SAS (http://www.akeneo.com)
 * @license   http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */
class MysqlTestRepositoryIntegration extends KernelTestCase
{
    protected function setUp()
    {
        parent::setUp();
        static::bootKernel(['debug' => false]);

        $application = new Application(static::$kernel);
        $application->setAutoExit(false);

        $input = new ArrayInput(['command' => 'akeneo:install-database']);
        $application->run($input, new NullOutput());
    }

    public function test_persistence_of_a_run_in_mysql()
    {
        $test1 = new Test(
            new PipelineName('pim-community-dev'),
            new BranchName('PR-7845'),
            new RunId('1'),
            new TestName('Test / behat-ce / test_1'),
            \DateTime::createFromFormat('Y-m-d\TH:i:s.uO', '2018-03-21T09:10:04.431+0000'),
            0.0002
        );

        $test2 = new Test(
            new PipelineName('pim-community-dev'),
            new BranchName('PR-7845'),
            new RunId('1'),
            new TestName('Test / behat-ce / test_2'),
            \DateTime::createFromFormat('Y-m-d\TH:i:s.uO', '2018-03-21T09:10:05.431+0000'),
            1.879
        );

        $repository = $this->getTestRepository();
        $repository->saveTests([$test1, $test2]);

        $connection = $this->getConnection();
        $sql = <<<SQL
            SELECT * FROM test_metric 
SQL;

        $stmt = $connection->query($sql);
        $response = $stmt->fetchAll();
        unset($response[0]['id']);
        unset($response[1]['id']);

        $expectedResponse = [
            [
                'pipeline_name' => 'pim-community-dev',
                'branch_name' => 'PR-7845',
                'run_id' => '1',
                'duration' => '0.0002',
                'execution_time' => '2018-03-21 09:10:04',
                'test_name' => 'Test / behat-ce / test_1',
                'type' => 'behat-ce',
            ],
            [
                'pipeline_name' => 'pim-community-dev',
                'branch_name' => 'PR-7845',
                'run_id' => '1',
                'duration' => '1.879',
                'execution_time' => '2018-03-21 09:10:05',
                'test_name' => 'Test / behat-ce / test_2',
                'type' => 'behat-ce',
            ]
        ];
        $this->assertEquals($expectedResponse, $response);
    }

    public function test_existence_of_a_run_in_mysql()
    {
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
            10,
            20,
            30,
            40
        );

        $test = new Test(
            new PipelineName('pim-community-dev'),
            new BranchName('PR-7845'),
            new RunId('1'),
            new TestName('Test / behat-ce / test_1'),
            \DateTime::createFromFormat('Y-m-d\TH:i:s.uO', '2018-03-21T09:10:04.431+0000'),
            0.0002
        );

        $repository = $this->getTestRepository();
        $repository->saveTests([$test]);

        Assert::assertTrue($repository->hasTestsFor($run));
    }

    public function test_non_existence_of_a_run_in_mysql()
    {
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
            10,
            20,
            30,
            40
        );

        $test = new Test(
            new PipelineName('pim-community-dev'),
            new BranchName('PR-1337'),
            new RunId('1'),
            new TestName('Test / behat-ce / test_1'),
            \DateTime::createFromFormat('Y-m-d\TH:i:s.uO', '2018-03-21T09:10:04.431+0000'),
            1.5
        );

        $repository = $this->getTestRepository();
        $repository->saveTests([$test]);

        Assert::assertFalse($repository->hasTestsFor($run));
    }

    private function getTestRepository(): TestRepository
    {
        return static::$kernel->getContainer()->get('test.App\Infrastructure\Persistence\API\MysqlTestRepository');
    }

    private function getConnection(): Connection
    {
        return static::$kernel->getContainer()->get('test.Doctrine\DBAL\Connection');
    }
}
