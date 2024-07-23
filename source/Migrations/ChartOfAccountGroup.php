<?php
namespace Source\Migrations;

require dirname(dirname(__DIR__)) . "/vendor/autoload.php";

use Source\Migrations\Core\DDL;
use Source\Models\ChartOfAccountGroup as ModelsChartOfAccountGroup;

/**
 * ChartOfAccountGroup Migrations
 * @link 
 * @author Roberto Dorado <robertodorado7@gmail.com>
 * @package Source\Migrations
 */
class ChartOfAccountGroup
{
    /** @var DDL */
    private DDL $ddl;

    /**
     * ChartOfAccountGroup constructor
     */
    public function __construct()
    {
        $this->ddl = new DDL(ModelsChartOfAccountGroup::class);
    }

    public function defineTable()
    {
        $this->ddl->setClassProperties();
        $this->ddl->setProperty('');
        $this->ddl->setProperty('');
        $this->ddl->setKeysToProperties([
            "BIGINT AUTO_INCREMENT PRIMARY KEY",
            "VARCHAR(36) UNIQUE NOT NULL",
            "BIGINT NOT NULL",
            "BIGINT NOT NULL",
            "VARCHAR(255) NOT NULL",
            "VARCHAR(255) NOT NULL",
            "TINYINT(1) NOT NULL",
            "CONSTRAINT fk_chart_of_account_group_user FOREIGN KEY (id_user) REFERENCES user(id) ON DELETE CASCADE ON UPDATE CASCADE",
            "CONSTRAINT fk_chart_of_account_group_company FOREIGN KEY (id_company) REFERENCES company(id) ON DELETE CASCADE ON UPDATE CASCADE"
        ]);
        $this->ddl->setForeignKeyChecks(0)->dropTableIfExists()->createTableQuery()->setForeignKeyChecks(1);
        $this->ddl->executeQuery();
    }
}

executeMigrations(ChartOfAccountGroup::class);