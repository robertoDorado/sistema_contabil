<?php

namespace Source\Migrations;

require dirname(dirname(__DIR__)) . "/vendor/autoload.php";

use Source\Migrations\Core\DDL;
use Source\Models\Customer as ModelsCustomer;

/**
 * Customer C:\php-projects\sistema-contabil\source\Migrations
 * @link 
 * @author Roberto Dorado <robertodorado7@gmail.com>
 * @package Migrations
 */
class Customer
{
    private DDL $ddl;

    /**
     * Customer constructor
     */
    public function __construct()
    {
        $this->ddl = new DDL(ModelsCustomer::class);
    }

    public function defineTable()
    {
        $this->ddl->setClassProperties();
        $this->ddl->setKeysToProperties([
            "BIGINT AUTO_INCREMENT PRIMARY KEY",
            "VARCHAR(36) UNIQUE NOT NULL",
            "VARCHAR(255) NOT NULL",
            "VARCHAR(255) NOT NULL",
            "DATE NOT NULL",
            "TINYINT(1) NOT NULL", "VARCHAR(255) UNIQUE NOT NULL", "VARCHAR(255) NOT NULL",
            "VARCHAR(255) NOT NULL", "VARCHAR(255) NOT NULL", "VARCHAR(255) NOT NULL",
            "VARCHAR(255) NOT NULL", "VARCHAR(255) NOT NULL", "VARCHAR(255)", "VARCHAR(255)",
            "DATE NOT NULL", "DATE NOT NULL", "TINYINT(1) NOT NULL"
        ]);
        $this->ddl->setForeignKeyChecks(0)->dropTableIfExists()->createTableQuery()->setForeignKeyChecks(1);
        $this->ddl->executeQuery();
    }
}
executeMigrations(Customer::class);
