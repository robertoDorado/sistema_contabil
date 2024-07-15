<?php

namespace Source\Migrations;

use Source\Migrations\Core\DDL;
use Source\Models\Subscription as ModelsSubscription;

require dirname(dirname(__DIR__)) . "/vendor/autoload.php";

/**
 * 005-Subscription Migrations
 * @link 
 * @author Roberto Dorado <robertodorado7@gmail.com>
 * @package Source\Migrations
 */
class Subscription
{
    /** @var DDL Data definition language */
    private DDL $ddl;

    /**
     * 005-Subscription constructor
     */
    public function __construct()
    {
        $this->ddl = new DDL(ModelsSubscription::class);
    }

    public function defineTable()
    {
        $this->ddl->setClassProperties();
        $this->ddl->setProperty('');
        $this->ddl->setKeysToProperties([
            "BIGINT AUTO_INCREMENT PRIMARY KEY",
            "VARCHAR(36) UNIQUE NOT NULL", "VARCHAR(255) NOT NULL", "BIGINT NOT NULL",
            "VARCHAR(255) NOT NULL", "VARCHAR(255) NOT NULL", "DATE NOT NULL", "DATE NOT NULL",
            "DATE NOT NULL", "DATE NOT NULL", "VARCHAR(255) NOT NULL",
            "CONSTRAINT fk_customer_subscription FOREIGN KEY (customer_id) REFERENCES customer(id) ON DELETE CASCADE ON UPDATE CASCADE"
        ]);
        $this->ddl->setForeignKeyChecks(0)->dropTableIfExists()->createTableQuery()->setForeignKeyChecks(1);
        return $this->ddl->executeQuery();
    }
}
executeMigrations(Subscription::class);
