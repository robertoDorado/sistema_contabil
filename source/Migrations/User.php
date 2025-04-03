<?php

namespace Source\Migrations;

use Source\Migrations\Core\DDL;
use Source\Models\User as ModelsUser;

require dirname(dirname(__DIR__)) . "/vendor/autoload.php";

/**
 * User Migrations
 * @link 
 * @author Roberto Dorado <robertodorado7@gmail.com>
 * @package Source\Migrations
 */
class User
{
    /** @var DDL Data Definition Language */
    private DDL $ddl;

    /**
     * User constructor
     */
    public function __construct()
    {
        $this->ddl = new DDL(ModelsUser::class);
    }

    public function setUniqueKeyNickName()
    {
        $this->ddl->alterTable(["ADD UNIQUE (user_nick_name)"]);
        return $this->ddl->executeQuery();
    }

    public function setUniqueKeyEmail()
    {
        $this->ddl->alterTable(["ADD UNIQUE (user_email)"]);
        return $this->ddl->executeQuery();
    }

    /**
     * Criado em 2024-02-17
     *
     * @return void
     */
    public function defineTable()
    {
        $this->ddl->setClassProperties();
        $this->ddl->setProperty('');
        $this->ddl->setKeysToProperties([
            "BIGINT AUTO_INCREMENT PRIMARY KEY",
            "VARCHAR(36) UNIQUE NOT NULL",
            "BIGINT UNIQUE NOT NULL",
            "VARCHAR(255) NOT NULL",
            "VARCHAR(255) NOT NULL",
            "VARCHAR(255) NOT NULL",
            "VARCHAR(255) NOT NULL",
            "TINYINT(1) NOT NULL",
            "CONSTRAINT fk_customer_user FOREIGN KEY (id_customer) 
            REFERENCES customer(id) ON UPDATE CASCADE ON DELETE CASCADE"
        ]);
        $this->ddl->setForeignKeyChecks(0)->dropTableIfExists()->createTableQuery()->setForeignKeyChecks(1);
        $this->ddl->executeQuery();
    }
}
(new User())->setUniqueKeyNickName();
// executeMigrations(User::class);
