<?php
namespace Source\Migrations;

use Source\Migrations\Core\DDL;
use Source\Models\BalanceSheet as ModelsBalanceSheet;

require dirname(dirname(__DIR__)) . "/vendor/autoload.php";

/**
 * BalanceSheet Migrations
 * @link 
 * @author Roberto Dorado <robertodorado7@gmail.com>
 * @package Source\Migrations
 */
class BalanceSheet
{
    /** @var DDL */
    private DDL $ddl;

    /**
     * BalanceSheet constructor
     */
    public function __construct()
    {
        $this->ddl = new DDL(ModelsBalanceSheet::class);    
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
            "TINYINT(1) NOT NULL",
            "DECIMAL(10, 2) NOT NULL",
            "VARCHAR(1000) NOT NULL",
            "DATE NOT NULL",
            "DATE NOT NULL",
            "TINYINT(1) NOT NULL",
            "CONSTRAINT fk_balance_sheet_user FOREIGN KEY (id_user) REFERENCES user(id) ON DELETE CASCADE ON UPDATE CASCADE",
            "CONSTRAINT fk_balance_sheet_company FOREIGN KEY (id_company) REFERENCES company(id) ON DELETE CASCADE ON UPDATE CASCADE",
            "CONSTRAINT fk_balance_sheet_chart_of_account FOREIGN KEY (id_chart_of_account) REFERENCES chart_of_account(id) ON DELETE CASCADE ON UPDATE CASCADE",
        ]);
        $this->ddl->setForeignKeyChecks(0)->dropTableIfExists()->createTableQuery()->setForeignKeyChecks(1);
        $this->ddl->executeQuery();
    }
}

executeMigrations(BalanceSheet::class);