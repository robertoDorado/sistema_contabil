<?php
namespace Source\Migrations;

require dirname(dirname(__DIR__)) . "/vendor/autoload.php";

use Ramsey\Uuid\Nonstandard\Uuid;
use Source\Core\Connect;
use Source\Migrations\Core\DDL;
use Source\Models\ReportSystem as ModelsReportSystem;

/**
 * ReportSystem Migrations
 * @link 
 * @author Roberto Dorado <robertodorado7@gmail.com>
 * @package Source\Migrations
 */
class ReportSystem
{
    /** @var DDL Data definition language */
    private DDL $ddl;

    /** @var ModelsReportSystem */
    private ModelsReportSystem $model;

    /**
     * ReportSystem constructor
     */
    public function __construct()
    {
        $this->ddl = new DDL(ModelsReportSystem::class);
        $this->model = new ModelsReportSystem();
    }

    public function defineTable()
    {
        $this->ddl->setClassProperties();
        $this->ddl->setKeysToProperties([
            "BIGINT AUTO_INCREMENT PRIMARY KEY",
            "VARCHAR(255) UNIQUE NOT NULL",
            "VARCHAR(255) NOT NULL"
        ]);

        $this->ddl->setForeignKeyChecks(0)->dropTableIfExists()->createTableQuery()->setForeignKeyChecks(1);
        $this->ddl->executeQuery();
        $reportNames = ["balanço patrimonial", "fluxo de caixa", "dre"];

        Connect::getInstance()->beginTransaction();
        foreach ($reportNames as $reportName) {
            $this->model->uuid = Uuid::uuid4();
            $this->model->report_name = $reportName;

            if (!$this->model->save()) {
                Connect::getInstance()->rollBack();
            }
        }
        Connect::getInstance()->commit();
    }
}

executeMigrations(ReportSystem::class);