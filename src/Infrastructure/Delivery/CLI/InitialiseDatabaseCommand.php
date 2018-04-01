<?php

declare(strict_types=1);

namespace App\Infrastructure\Delivery\CLI;

use App\Application\Command\ImportRunMetrics;
use App\Application\Command\ImportRunMetricsHandler;
use App\Application\Command\ImportTestMetrics;
use App\Application\Command\ImportTestMetricsHandler;
use App\Model\Jenkins\Pipeline\PipelineName;
use Doctrine\DBAL\Connection;
use Psr\Log\LoggerInterface;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Command\LockableTrait;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

/**
 * @author    Alexandre Hocquard <alexandre.hocquard@akeneo.com>
 * @copyright 2018 Akeneo SAS (http://www.akeneo.com)
 * @license   http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */
class InitialiseDatabaseCommand extends Command
{
    use LockableTrait;

    protected static $defaultName = 'akeneo:install-database';

    /** @var LoggerInterface */
    private $logger;

    /** @var Connection */
    private $connection;

    public function __construct(LoggerInterface $logger, Connection $connection)
    {
        parent::__construct();

        $this->connection = $connection;
        $this->logger = $logger;
    }

    /**
     * {@inheritdoc}
     */
    protected function configure()
    {
        $this->setDescription('Initialise the database.');
    }


    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $this->dropRunTable();
        $this->dropTestTable();
        $this->createRunTable();
        $this->createTestTable();
    }

    private function dropTestTable(): void
    {
        $this->logger->info('Drop table "run_metric');
        $sql = 'DROP TABLE IF EXISTS `run_metric`';
        $this->connection->exec($sql);
    }

    private function dropRunTable(): void
    {
        $this->logger->info('Drop table "test_metric');
        $sql = 'DROP TABLE IF EXISTS `test_metric`';
        $this->connection->exec($sql);
    }

    private function createRunTable(): void
    {
        $this->logger->info('Create table "run_table".');
        $sql = <<<SQL
            CREATE TABLE `run_metric` (
                `id` int(11) NOT NULL AUTO_INCREMENT,
                `pipeline_name` varchar(40) COLLATE utf8_unicode_ci NOT NULL,
                `branch_name` varchar(40) COLLATE utf8_unicode_ci NOT NULL,
                `run_id` varchar(5) COLLATE utf8_unicode_ci NOT NULL,
                `state` varchar(255) COLLATE utf8_unicode_ci NOT NULL,
                `result` varchar(255) COLLATE utf8_unicode_ci NOT NULL,
                `is_pull_request` tinyint(1) NOT NULL,
                `duration` mediumint(6) NOT NULL,
                `failed_tests` mediumint(6) DEFAULT NULL,
                `skipped_tests` mediumint(6) DEFAULT NULL,
                `succeeded_tests` mediumint(6) DEFAULT NULL,
                `total_tests` mediumint(6) DEFAULT NULL,
                `start_time` datetime NOT NULL,
                 PRIMARY KEY (`id`),
                 UNIQUE KEY `unique_value` (`pipeline_name`,`branch_name`,`run_id`),
                 KEY `duration_index` (`duration`),
                 KEY `start_time_index` (`start_time`)
            ) ENGINE=InnoDB AUTO_INCREMENT=1355 DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;
SQL;

        $this->connection->exec($sql);
    }

    private function createTestTable(): void
    {
        $this->logger->info('Create table "test_table".');
        $sql = <<<SQL
            CREATE TABLE `test_metric` (
                `id` int(11) NOT NULL AUTO_INCREMENT,
                `pipeline_name` varchar(40) COLLATE utf8_unicode_ci NOT NULL,
                `branch_name` varchar(40) COLLATE utf8_unicode_ci NOT NULL,
                `run_id` varchar(5) COLLATE utf8_unicode_ci NOT NULL,
                `test_name` varchar(400) COLLATE utf8_unicode_ci NOT NULL,
                `type` varchar(400) COLLATE utf8_unicode_ci NOT NULL,
                `duration` float(9,5) NOT NULL,
                `execution_time` datetime NOT NULL,
                 PRIMARY KEY (`id`),
                 KEY `identifier_index` (`pipeline_name`,`branch_name`,`run_id`, `test_name`),
                 KEY `duration_index` (`duration`),
                 KEY `type_index` (`type`),
                 KEY `execution_time_index` (`execution_time`)
           ) ENGINE=InnoDB AUTO_INCREMENT=1355 DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;
SQL;

        $this->connection->exec($sql);
    }
}
