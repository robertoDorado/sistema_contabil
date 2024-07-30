<?php
namespace Source\Migrations;

require dirname(dirname(__DIR__)) . "/vendor/autoload.php";

use Source\Migrations\Core\DDL;
use Source\Models\ChartOfAccount as ModelsChartOfAccount;

/**
 * ChartOfAccount Migrations
 * @link 
 * @author Roberto Dorado <robertodorado7@gmail.com>
 * @package Source\Migrations
 */
class ChartOfAccount
{
    /** @var DDL */
    protected DDL $ddl;

    /**
     * ChartOfAccount constructor
     */
    public function __construct()
    {
        $this->ddl = new DDL(ModelsChartOfAccount::class);
    }

    public function alterTable()
    {
        $this->ddl->alterTable([
            "MODIFY account_number VARCHAR(255) UNIQUE NOT NULL"
        ]);
        $this->ddl->executeQuery();
    }

    public function defineTable()
    {
        $this->ddl->setClassProperties();
        $this->ddl->setProperty('');
        $this->ddl->setProperty('');
        $this->ddl->setProperty('');
        $this->ddl->setKeysToProperties([
            "BIGINT AUTO_INCREMENT PRIMARY KEY",
            "VARCHAR(36) UNIQUE NOT NULL",
            "BIGINT NOT NULL",
            "BIGINT NOT NULL",
            "BIGINT NOT NULL",
            "VARCHAR(255) NOT NULL",
            "VARCHAR(255) NOT NULL",
            "TINYINT(1) NOT NULL",
            "CONSTRAINT fk_chart_of_account_user FOREIGN KEY (id_user) REFERENCES user(id) ON DELETE CASCADE ON UPDATE CASCADE",
            "CONSTRAINT fk_chart_of_account_company FOREIGN KEY (id_company) REFERENCES company(id) ON DELETE CASCADE ON UPDATE CASCADE",
            "CONSTRAINT fk_chart_of_account_chart_of_account_group FOREIGN KEY (id_chart_of_account_group) REFERENCES chart_of_account_group(id) ON DELETE CASCADE ON UPDATE CASCADE"
        ]);
        $this->ddl->setForeignKeyChecks(0)->dropTableIfExists()->createTableQuery()->setForeignKeyChecks(1);
        $this->ddl->executeQuery();
    }
}
executeMigrations(ChartOfAccount::class);