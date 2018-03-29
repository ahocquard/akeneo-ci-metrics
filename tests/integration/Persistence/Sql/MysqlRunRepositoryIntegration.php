<?php

declare(strict_types=1);

namespace tests\integration\Persistence\Sql;

use App\Model\Jenkins\Branch\BranchName;
use App\Model\Jenkins\Pipeline\PipelineName;
use App\Model\Jenkins\Run\Run;
use App\Model\Jenkins\Run\RunId;
use App\Model\Jenkins\Run\RunRepository;
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
class MysqlRunRepositoryIntegration extends KernelTestCase
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
            10,
            20,
            30,
            40
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
            -1,
            -1,
            -1,
            -1
        );

        $repository = $this->getRunRepository();
        $repository->saveRuns([$run1, $run2]);

        $connection = $this->getConnection();
        $sql = <<<SQL
            SELECT * FROM run_metric 
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
                'result' => 'ABORTED',
                'state' => 'FINISHED',
                'duration' => '432031',
                'start_time' => '2018-03-21 09:10:04',
                'failed_tests' => '10',
                'skipped_tests' => '20',
                'succeeded_tests' => '30',
                'total_tests' => '40',
                'is_pull_request' => '1',
            ],
            [
                'pipeline_name' => 'pim-community-dev',
                'branch_name' => 'PR-7845',
                'run_id' => '2',
                'result' => 'ABORTED',
                'state' => 'FINISHED',
                'duration' => '432070',
                'start_time' => '2018-03-21 11:15:21',
                'failed_tests' => '-1',
                'skipped_tests' => '-1',
                'succeeded_tests' => '-1',
                'total_tests' => '-1',
                'is_pull_request' => '1',
            ]
        ];
        $this->assertEquals($expectedResponse, $response);
    }

    public function test_existence_of_a_run_in_mysql()
    {
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
            10,
            20,
            30,
            40
        );

        $repository = $this->getRunRepository();
        $repository->saveRuns([$run1]);

        Assert::assertTrue($repository->hasRun($run1));
    }

    public function test_non_existence_of_a_run_in_mysql()
    {
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
            10,
            20,
            30,
            40
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
            -1,
            -1,
            -1,
            -1
        );

        $repository = $this->getRunRepository();
        $repository->saveRuns([$run1]);

        Assert::assertFalse($repository->hasRun($run2));
    }

    private function getRunRepository(): RunRepository
    {
        return static::$kernel->getContainer()->get('test.App\Infrastructure\Persistence\API\MysqlRunRepository');
    }

    private function getConnection(): Connection
    {
        return static::$kernel->getContainer()->get('test.Doctrine\DBAL\Connection');
    }
}
