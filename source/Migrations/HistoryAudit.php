<?php

namespace Source\Migrations;

require dirname(dirname(__DIR__)) . "/vendor/autoload.php";

use Source\Migrations\Core\DDL;
use Source\Models\HistoryAudit as ModelsHistoryAudit;

/**
 * HistoryAudit Migrations
 * @link 
 * @author Roberto Dorado <robertodorado7@gmail.com>
 * @package Source\Migrations
 */
class HistoryAudit
{
    /** @var DDL */
    private DDL $ddl;

    /**
     * HistoryAudit constructor
     */
    public function __construct()
    {
        $this->ddl = new DDL(ModelsHistoryAudit::class);
    }

    public function defineTable()
    {
        $this->ddl->setClassProperties();
        $this->ddl->setProperty('');
        $this->ddl->setProperty('');
        $this->ddl->setProperty('');
        $this->ddl->setKeysToProperties([
            "BIGINT AUTO_INCREMENT PRIMARY KEY",
            "VARCHAR(255) UNIQUE NOT NULL",
            "BIGINT NOT NULL",
            "BIGINT NOT NULL",
            "BIGINT NOT NULL",
            "VARCHAR(255) NOT NULL",
            "DECIMAL(10, 2) NOT NULL",
            "DATETIME NOT NULL",
            "TINYINT(1) NOT NULL",
            "CONSTRAINT fk_history_audit_user FOREIGN KEY (id_user) REFERENCES user(id) ON DELETE CASCADE ON UPDATE CASCADE",
            "CONSTRAINT fk_history_audit_report_system FOREIGN KEY (id_report) REFERENCES report_system(id) ON DELETE CASCADE ON UPDATE CASCADE",
            "CONSTRAINT fk_history_audit_company FOREIGN KEY (id_company) REFERENCES company(id) ON DELETE CASCADE ON UPDATE CASCADE"
        ]);
        $this->ddl->dropTableIfExists()->createTableQuery();
        $this->ddl->executeQuery();
    }
}

executeMigrations(HistoryAudit::class);