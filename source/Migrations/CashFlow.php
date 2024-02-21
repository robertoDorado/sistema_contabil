<?php
namespace Source\Migrations;

use Source\Migrations\Core\DDL;
use Source\Models\CashFlow as ModelsCashFlow;

require dirname(dirname(__DIR__)) . "/vendor/autoload.php";

/**
 * CashFlow Migrations
 * @link 
 * @author Roberto Dorado <robertodorado7@gmail.com>
 * @package Source\Migrations
 */
class CashFlow
{
    /** @var DDL Data Definition Language */
    private DDL $ddl;

    /**
     * CashFlow constructor
     */
    public function __construct()
    {
        $this->ddl = new DDL(ModelsCashFlow::class);
    }

    /**
     * Data da criação 2024-02-19
     *
     * @return void
     */
    public function defineTable()
    {
        $this->ddl->setClassProperties();
        $this->ddl->setProperty('');
        $this->ddl->setKeysToProperties(["BIGINT AUTO_INCREMENT PRIMARY KEY",
        "BIGINT NOT NULL", "DECIMAL(10, 2) NOT NULL", "VARCHAR(255) NOT NULL",
        "TINYINT(1) NOT NULL", "TIMESTAMP DEFAULT CURRENT_TIMESTAMP",
        "TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP",
        "CONSTRAINT fk_cash_flow FOREIGN KEY (id_user) REFERENCES user(id)"]);
        $this->ddl->dropTableIfExists()->createTableQuery();
        $this->ddl->executeQuery();
    }
}
executeMigrations(CashFlow::class);