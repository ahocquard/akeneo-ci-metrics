<?php

declare(strict_types=1);

namespace App\Infrastructure\Persistence\Sql;

use App\Model\Jenkins\Run\Run;
use App\Model\Jenkins\Test\TestRepository;
use Doctrine\DBAL\Connection;
use Doctrine\DBAL\Types\Type;

/**
 * @author    Alexandre Hocquard <alexandre.hocquard@akeneo.com>
 * @copyright 2018 Akeneo SAS (http://www.akeneo.com)
 * @license   http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */
class MysqlTestRepository implements TestRepository
{
    /** @var Connection */
    private $connection;

    /**
     * @param Connection $connection
     */
    public function __construct(Connection $connection)
    {
        $this->connection = $connection;
    }

    /**
     * {@inheritdoc}
     */
    public function saveTests(array $tests): void
    {
        if (empty($tests)) {
            return;
        }

        $sql = <<<SQL
            INSERT INTO test_metric(
                pipeline_name, 
                branch_name, 
                run_id,
                test_name,
                duration,
                execution_time
            )
            VALUES (
                :pipeline_name, 
                :branch_name, 
                :run_id,
                :test_name, 
                :duration, 
                :execution_time
            )
SQL;

        $this->connection->beginTransaction();
        $stmt = $this->connection->prepare($sql);

        foreach ($tests as $test) {
            $stmt->bindValue('pipeline_name', $test->pipelineName()->value(), Type::STRING);
            $stmt->bindValue('branch_name', $test->branchName()->value(), Type::STRING);
            $stmt->bindValue('run_id', $test->runId()->value(), Type::STRING);
            $stmt->bindValue('test_name', $test->name()->value(), Type::STRING);
            $stmt->bindValue('duration', $test->duration(), Type::FLOAT);
            $stmt->bindValue('execution_time', $test->executionTime(), Type::DATETIME);
            $stmt->execute();
        }

        $this->connection->commit();
    }

    /**
     * {@inheritdoc}
     */
    public function hasTestsFor(Run $run): bool
    {
        $sql = <<<SQL
            SELECT 1 
            FROM test_metric 
            WHERE pipeline_name = :pipeline_name
            AND branch_name= :branch_name
            AND run_id = :run_id
            LIMIT 1
SQL;

        $stmt = $this->connection->executeQuery($sql,
            [
                'pipeline_name' => $run->pipelineName()->value(),
                'branch_name' => $run->branchName()->value(),
                'run_id' => $run->id()->value(),
            ]
        );

        $result = $stmt->fetch();

        return false !== $result;
    }
}
