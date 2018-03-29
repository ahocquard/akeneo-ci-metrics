<?php

declare(strict_types=1);

namespace App\Infrastructure\Persistence\Sql;

use App\Model\Jenkins\Run\Run;
use App\Model\Jenkins\Run\RunRepository;
use Doctrine\DBAL\Connection;
use Doctrine\DBAL\Types\Type;

/**
 * @author    Alexandre Hocquard <alexandre.hocquard@akeneo.com>
 * @copyright 2018 Akeneo SAS (http://www.akeneo.com)
 * @license   http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */
class MysqlRunRepository implements RunRepository
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
    public function saveRuns(array $runs): void
    {
        if (empty($runs)) {
            return;
        }

        $sql = <<<SQL
            INSERT INTO run_metric(
                pipeline_name, 
                branch_name, 
                run_id,
                state, 
                result, 
                is_pull_request,
                duration,
                failed_tests,
                skipped_tests,
                succeeded_tests,
                total_tests,
                start_time
            )
            VALUES (
                :pipeline_name, 
                :branch_name, 
                :run_id,
                :state, 
                :result, 
                :is_pull_request,
                :duration,
                :failed_tests,
                :skipped_tests,
                :succeeded_tests,
                :total_tests,
                :start_time
            )
SQL;

        $this->connection->beginTransaction();
        $stmt = $this->connection->prepare($sql);

        foreach ($runs as $run) {
            $stmt->bindValue('pipeline_name', $run->pipelineName()->value(), Type::STRING);
            $stmt->bindValue('branch_name', $run->branchName()->value(), Type::STRING);
            $stmt->bindValue('run_id', $run->id()->value(), Type::STRING);
            $stmt->bindValue('state', $run->state(), Type::STRING);
            $stmt->bindValue('result', $run->result(), Type::STRING);
            $stmt->bindValue('is_pull_request', $run->isPullRequestRun(), Type::BOOLEAN);
            $stmt->bindValue('duration', $run->duration(), Type::INTEGER);
            $stmt->bindValue('failed_tests', $run->numberOfFailedTests(), Type::INTEGER);
            $stmt->bindValue('skipped_tests', $run->numberOfSkippedTests(), Type::INTEGER);
            $stmt->bindValue('succeeded_tests', $run->numberOfSucceededTests(), Type::INTEGER);
            $stmt->bindValue('total_tests', $run->numberOfTests(), Type::INTEGER);
            $stmt->bindValue('start_time', $run->startTime(), Type::DATETIME);
            $stmt->execute();
        }

        $this->connection->commit();
    }

    /**
     * {@inheritdoc}
     */
    public function hasRun(Run $run): bool
    {
        $sql = <<<SQL
            SELECT 1 
            FROM run_metric 
            WHERE pipeline_name = :pipeline_name
            AND branch_name= :branch_name
            AND run_id = :run_id
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
