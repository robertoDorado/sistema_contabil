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
        $this->ddl->setProperty('');
        $this->ddl->setProperty('');
        $this->ddl->setKeysToProperties([
            "BIGINT AUTO_INCREMENT PRIMARY KEY",
            "VARCHAR(36) UNIQUE NOT NULL",
            "BIGINT NOT NULL",
            "BIGINT NOT NULL",
            "BIGINT NOT NULL",
            "DECIMAL(10, 2) NOT NULL",
            "VARCHAR(255) NOT NULL",
            "TINYINT(1) NOT NULL",
            "DATE NOT NULL",
            "DATE NOT NULL",
            "TINYINT(1) NOT NULL",
            "CONSTRAINT fk_cash_flow_user FOREIGN KEY (id_user) REFERENCES user(id) ON DELETE CASCADE ON UPDATE CASCADE",
            "CONSTRAINT fk_cash_flow_cash_flow_group FOREIGN KEY (id_cash_flow_group) REFERENCES cash_flow_group(id) ON DELETE CASCADE ON UPDATE CASCADE",
            "CONSTRAINT fk_cash_flow_company FOREIGN KEY (id_company) REFERENCES company(id) ON DELETE CASCADE ON UPDATE CASCADE"
        ]);
        $this->ddl->setForeignKeyChecks(0)->dropTableIfExists()->createTableQuery()->setForeignKeyChecks(1);
        $this->ddl->executeQuery();
    }
}
executeMigrations(CashFlow::class);
