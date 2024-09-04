<?php

namespace Source\Migrations;

use Source\Migrations\Core\DDL;
use Source\Models\Support as ModelsSupport;

require dirname(dirname(__DIR__)) . "/vendor/autoload.php";

/**
 * Support Migrations
 * @link 
 * @author Roberto Dorado <robertodorado7@gmail.com>
 * @package Source\Migrations
 */
class Support
{
    /** @var DDL */
    private DDL $ddl;

    /**
     * Support constructor
     */
    public function __construct()
    {
        $this->ddl = new DDL(ModelsSupport::class);
    }

    public function defineTable()
    {
        $this->ddl->setClassProperties();
        $this->ddl->setKeysToProperties(
            [
                "BIGINT AUTO_INCREMENT PRIMARY KEY",
                "VARCHAR(36) UNIQUE NOT NULL",
                "VARCHAR(255) NOT NULL",
                "VARCHAR(255) NOT NULL",
                "VARCHAR(255) UNIQUE NOT NULL",
                "VARCHAR(255) NOT NULL",
                "TINYINT(1) NOT NULL"
            ]
        );
        $this->ddl->setForeignKeyChecks(0)->dropTableIfExists()->createTableQuery()->setForeignKeyChecks(1)->executeQuery();
    }
}
executeMigrations(Support::class);
