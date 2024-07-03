<?php
namespace Source\Migrations;

require dirname(dirname(__DIR__)) . "/vendor/autoload.php";

use Source\Migrations\Core\DDL;
use Source\Models\CashFlowExplanatoryNotes as ModelsCashFlowExplanatoryNotes;

/**
 * CashFlowExplanatoryNotes Migrations
 * @link 
 * @author Roberto Dorado <robertodorado7@gmail.com>
 * @package Source\Migrations
 */
class CashFlowExplanatoryNotes
{
    /** @var DDL */
    protected DDL $ddl;

    /**
     * CashFlowExplanatoryNotes constructor
     */
    public function __construct()
    {
        $this->ddl = new DDL(ModelsCashFlowExplanatoryNotes::class);
    }

    public function changeNoteColumnToThousandCharacters()
    {
        $this->ddl->alterTable(["MODIFY note VARCHAR(1000) NOT NULL"]);
        $this->ddl->executeQuery();
    }

    public function defineTable()
    {
        $this->ddl->setClassProperties();
        $this->ddl->setProperty('');
        $this->ddl->setKeysToProperties([
            "BIGINT AUTO_INCREMENT PRIMARY KEY",
            "VARCHAR(36) UNIQUE NOT NULL",
            "BIGINT NOT NULL",
            "VARCHAR(255) NOT NULL",
            "CONSTRAINT fk_cash_flow_explanatory_notes FOREIGN KEY (id_cash_flow) REFERENCES cash_flow(id) ON DELETE CASCADE ON UPDATE CASCADE"
        ]);
        $this->ddl->setForeignKeyChecks(0)->dropTableIfExists()->createTableQuery()->setForeignKeyChecks(1);
        $this->ddl->executeQuery();
    }
}
executeMigrations(CashFlowExplanatoryNotes::class);