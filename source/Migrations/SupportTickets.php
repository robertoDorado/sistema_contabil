<?php

namespace Source\Migrations;

use Source\Migrations\Core\DDL;
use Source\Models\SupportTickets as ModelsSupportTickets;

require dirname(dirname(__DIR__)) . "/vendor/autoload.php";

/**
 * SupportTickets Migrations
 * @link 
 * @author Roberto Dorado <robertodorado7@gmail.com>
 * @package Source\Migrations
 */
class SupportTickets
{
    /** @var DDL */
    private DDL $ddl;

    /**
     * SupportTickets constructor
     */
    public function __construct()
    {
        $this->ddl = new DDL(ModelsSupportTickets::class);
    }

    public function defineTable()
    {
        $this->ddl->setClassProperties();
        $this->ddl->setProperty('');
        $this->ddl->setProperty('');
        $this->ddl->setKeysToProperties(
            [
                "BIGINT AUTO_INCREMENT PRIMARY KEY",
                "VARCHAR(36) UNIQUE NOT NULL",
                "BIGINT NOT NULL",
                "BIGINT NOT NULL",
                "VARCHAR(1000) NOT NULL",
                "VARCHAR(255) NOT NULL",
                "VARCHAR(255) NOT NULL",
                "TINYINT(1) NOT NULL",
                "CONSTRAINT fk_support_tickets_user FOREIGN KEY (id_user) 
                REFERENCES user(id) ON UPDATE CASCADE ON DELETE CASCADE",
                "CONSTRAINT fk_support_tickets_support FOREIGN KEY (id_support) 
                REFERENCES support(id) ON UPDATE CASCADE ON DELETE CASCADE",
            ]
        );
        $this->ddl->setForeignKeyChecks(0)->dropTableIfExists()->createTableQuery()->setForeignKeyChecks(1)->executeQuery();
    }
}
executeMigrations(SupportTickets::class);