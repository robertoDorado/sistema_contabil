<?php
namespace Source\Migrations;

use Source\Migrations\Core\DDL;
use Source\Models\BalanceSheetExplanatoryNotes as ModelsBalanceSheetExplanatoryNotes;

require dirname(dirname(__DIR__)) . "/vendor/autoload.php";

/**
 * BalanceSheetExplanatoryNotes Migrations
 * @link 
 * @author Roberto Dorado <robertodorado7@gmail.com>
 * @package Source\Migrations
 */
class BalanceSheetExplanatoryNotes
{
    /** @var DDL */
    private DDL $ddl;

    /**
     * BalanceSheetExplanatoryNotes constructor
     */
    public function __construct()
    {
        $this->ddl = new DDL(ModelsBalanceSheetExplanatoryNotes::class);
    }

    public function defineTable()
    {
        $this->ddl->setClassProperties();
        $this->ddl->setProperty('');
        $this->ddl->setKeysToProperties([
            "BIGINT AUTO_INCREMENT PRIMARY KEY",
            "VARCHAR(36) UNIQUE NOT NULL",
            "BIGINT NOT NULL",
            "VARCHAR(1000) NOT NULL",
            "TINYINT(1) NOT NULL",
            "CONSTRAINT fk_balance_sheet_explanatory_notes FOREIGN KEY (id_balance_sheet) REFERENCES balance_sheet(id) ON DELETE CASCADE ON UPDATE CASCADE"
        ]);
        $this->ddl->setForeignKeyChecks(0)->dropTableIfExists()->createTableQuery()->setForeignKeyChecks(1);
        $this->ddl->executeQuery();
    }
}

executeMigrations(BalanceSheetExplanatoryNotes::class);