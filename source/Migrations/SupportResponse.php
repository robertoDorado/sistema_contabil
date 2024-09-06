<?php
namespace Source\Migrations;

use Source\Migrations\Core\DDL;
use Source\Models\SupportResponse as ModelsSupportResponse;

require dirname(dirname(__DIR__)) . "/vendor/autoload.php";

/**
 * SupportResponse Migrations
 * @link 
 * @author Roberto Dorado <robertodorado7@gmail.com>
 * @package Source\Migrations
 */
class SupportResponse
{
    private DDL $ddl;

    /**
     * SupportResponse constructor
     */
    public function __construct()
    {
        $this->ddl = new DDL(ModelsSupportResponse::class);
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
                "BIGINT UNIQUE NOT NULL",
                "BIGINT NOT NULL",
                "VARCHAR(1000) NOT NULL",
                "VARCHAR(255) NULL",
                "TINYINT(1) NOT NULL",
                "DATE NOT NULL",
                "DATE NOT NULL",
                "CONSTRAINT fk_support_response_support_tickets FOREIGN KEY (id_support_tickets) 
                REFERENCES support_tickets(id) ON UPDATE CASCADE ON DELETE CASCADE",
                "CONSTRAINT fk_support_response_support FOREIGN KEY (id_support) 
                REFERENCES support(id) ON UPDATE CASCADE ON DELETE CASCADE"
            ]
        );
        $this->ddl->setForeignKeyChecks(0)->dropTableIfExists()->createTableQuery()->setForeignKeyChecks(1)->executeQuery();
    }
}
executeMigrations(SupportResponse::class);