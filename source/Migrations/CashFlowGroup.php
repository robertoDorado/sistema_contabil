<?php
namespace Source\Migrations;

use Source\Migrations\Core\DDL;
use Source\Models\CashFlowGroup as ModelsCashFlowGroup;

require dirname(dirname(__DIR__)) . "/vendor/autoload.php";

/**
 * CashFlowGroup C:\php-projects\sistema-contabil\source\Migrations
 * @link 
 * @author Roberto Dorado <robertodorado7@gmail.com>
 * @package Migrations
 */
class CashFlowGroup
{
    /** @var DDL Data Definition Language */
    private DDL $ddl;

    /**
     * CashFlowGroup constructor
     */
    public function __construct()
    {
        $this->ddl = new DDL(ModelsCashFlowGroup::class);
    }

    public function defineTable()
    {
        $this->ddl->setClassProperties();
        $this->ddl->setKeysToProperties(["BIGINT AUTO_INCREMENT PRIMARY KEY",
        "VARCHAR(36) UNIQUE NOT NULL", "VARCHAR(255) NOT NULL", "DATE NOT NULL", "DATE NOT NULL",
        "TINYINT(1) NOT NULL"]);
        $this->ddl->dropTableIfExists()->createTableQuery();
        $this->ddl->executeQuery();
    }
}
executeMigrations(CashFlowGroup::class);