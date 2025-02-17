<?php

namespace Source\Migrations;

use Source\Migrations\Core\DDL;
use Source\Models\SubscriptionCancellation as ModelsSubscriptionCancellation;

require dirname(dirname(__DIR__)) . "/vendor/autoload.php";

/**
 * SubscriptionCancellation Migrations
 * @link 
 * @author Roberto Dorado <robertodorado7@gmail.com>
 * @package Source\Migrations
 */
class SubscriptionCancellation
{
    /** @var DDL */
    private DDL $ddl;

    /**
     * SubscriptionCancellation constructor
     */
    public function __construct()
    {
        $this->ddl = new DDL(ModelsSubscriptionCancellation::class);
    }

    public function defineTable()
    {
        $this->ddl->setClassProperties();
        $this->ddl->setProperty('');
        $this->ddl->setKeysToProperties(
            [
                "BIGINT AUTO_INCREMENT PRIMARY KEY",
                "VARCHAR(36) UNIQUE NOT NULL",
                "BIGINT NOT NULL",
                "VARCHAR(1000) NOT NULL",
                "DATE NOT NULL",
                "DATE NOT NULL",
                "TINYINT(1) NOT NULL",
                "CONSTRAINT fk_customer_subscription_cancellation FOREIGN KEY (id_customer) REFERENCES customer(id) ON DELETE CASCADE ON UPDATE CASCADE"
            ]
        );
        $this->ddl->setForeignKeyChecks(0)->dropTableIfExists()->createTableQuery()->setForeignKeyChecks(1)->executeQuery();
    }
}

executeMigrations(SubscriptionCancellation::class);