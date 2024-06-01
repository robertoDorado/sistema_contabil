<?php
namespace Source\Migrations;

require dirname(dirname(__DIR__)) . "/vendor/autoload.php";

use Source\Migrations\Core\DDL;
use Source\Models\Company as ModelsCompany;

/**
 * Company Migrations
 * @link 
 * @author Roberto Dorado <robertodorado7@gmail.com>
 * @package Source\Migrations
 */
class Company
{
    /** @var DDL Data Definition Language */
    private DDL $ddl;

    /**
     * Company constructor
     */
    public function __construct()
    {
        $this->ddl = new DDL(ModelsCompany::class);
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
            "VARCHAR(255) NOT NULL",
            "VARCHAR(255) NULL",
            "DATE NOT NULL",
            "VARCHAR(255) NULL",
            "VARCHAR(255) NULL",
            "VARCHAR(255) NOT NULL",
            "VARCHAR(255) NOT NULL",
            "VARCHAR(255) NOT NULL",
            "VARCHAR(255) NOT NULL",
            "VARCHAR(255) NOT NULL",
            "VARCHAR(255) NOT NULL",
            "VARCHAR(255) NOT NULL",
            "VARCHAR(255) NOT NULL",
            "DATE NOT NULL",
            "DATE NOT NULL",
            "TINYINT(1) NOT NULL",
            "CONSTRAINT fk_company_user FOREIGN KEY (id_user) REFERENCES user(id) ON DELETE CASCADE ON UPDATE CASCADE"
        ]);
        $this->ddl->setForeignKeyChecks(0)->dropTableIfExists()->createTableQuery()->setForeignKeyChecks(1);
        $this->ddl->executeQuery();
    }
}

executeMigrations(Company::class);