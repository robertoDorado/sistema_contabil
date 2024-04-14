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
        $this->ddl->setProperty('');
        $this->ddl->setKeysToProperties(["BIGINT AUTO_INCREMENT PRIMARY KEY",
        "VARCHAR(36) UNIQUE NOT NULL", "BIGINT NOT NULL", "VARCHAR(255) NOT NULL", 
        "DATE NOT NULL", "DATE NOT NULL", "TINYINT(1) NOT NULL", 
        "CONSTRAINT fk_cash_flow_group_user FOREIGN KEY (id_user) REFERENCES user(id) ON DELETE CASCADE ON UPDATE CASCADE"]);
        $this->ddl->setForeignKeyChecks(0)->dropTableIfExists()->createTableQuery()->setForeignKeyChecks(1);
        $this->ddl->executeQuery();
    }
}
executeMigrations(CashFlowGroup::class);