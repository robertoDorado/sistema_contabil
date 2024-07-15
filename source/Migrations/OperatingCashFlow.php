<?php

namespace Source\Migrations;

use Source\Migrations\Core\DDL;
use Source\Models\OperatingCashFlow as ModelsOperatingCashFlow;

require dirname(dirname(__DIR__)) . "/vendor/autoload.php";

/**
 * 007-OperatingCashFlow Migrations
 * @link 
 * @author Roberto Dorado <robertodorado7@gmail.com>
 * @package Source\Migrations
 */
class OperatingCashFlow
{
    /** @var DDL Data definition language */
    private DDL $ddl;

    /**
     * 007-OperatingCashFlow constructor
     */
    public function __construct()
    {
        $this->ddl = new DDL(ModelsOperatingCashFlow::class);
    }

    public function defineTable()
    {
        $this->ddl->setClassProperties();
        $this->ddl->setProperty('');
        $this->ddl->setKeysToProperties([
            "BIGINT AUTO_INCREMENT PRIMARY KEY",
            "BIGINT NOT NULL UNIQUE",
            "TINYINT(1) NOT NULL",
            "CONSTRAINT fk_operating_cash_flow_group FOREIGN KEY (cash_flow_group_id) REFERENCES cash_flow_group(id) ON DELETE CASCADE ON UPDATE CASCADE"
        ]);
        $this->ddl->setForeignKeyChecks(0)->dropTableIfExists()->createTableQuery()->setForeignKeyChecks(1);
        return $this->ddl->executeQuery();
    }
}
executeMigrations(OperatingCashFlow::class);