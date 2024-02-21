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
        $this->ddl->setKeysToProperties(["BIGINT AUTO_INCREMENT PRIMARY KEY", "VARCHAR(355) NOT NULL",
        "VARCHAR(355) NOT NULL", "VARCHAR(355) NOT NULL", "VARCHAR(355) NOT NULL"]);
        $this->ddl->setForeignKeyChecks(0)->dropTableIfExists()->createTableQuery()->setForeignKeyChecks(1);
        return $this->ddl->executeQuery();
    }
}
executeMigrations(User::class);