<?php
namespace Source\Migrations;

require dirname(dirname(__DIR__)) . "/vendor/autoload.php";

use Source\Migrations\Core\DDL;
use Source\Models\TestModel as ModelsTestModel;

/**
 * TestModel Migrations
 * @link 
 * @author Roberto Dorado <robertodorado7@gmail.com>
 * @package Source\Migrations
 */
class TestModel extends DDL
{
    /**
     * TestModel constructor
     */
    public function __construct()
    {
        parent::__construct(ModelsTestModel::class);
    }

    /**
     * Tabela criada no dia 2023-11-04
     *
     * @return void
     */
    public function defineTable()
    {
        $this->setClassProperties();
        $this->setKeysToProperties(["BIGINT AUTO_INCREMENT PRIMARY KEY", "VARCHAR(255) NOT NULL",
        "VARCHAR(255) NOT NULL", "VARCHAR(255) NOT NULL", "VARCHAR(255) NOT NULL"]);
        $this->dropTableIfExists()->createTableQuery();
        // return $this->getQuery(); # Debug da Query DDL
        $this->executeQuery();
    }

    /**
     * Modificação da coluna D para 1000 caracteres no dia 2023-11-05
     *
     * @return void
     */
    public function modifyVarcharColumnD()
    {
        $this->alterTable(["MODIFY COLUMN column_d VARCHAR(1000) NOT NULL"]);
        // return $this->getQuery(); # Debug da Query DDL
        $this->executeQuery();
    }
}

(new TestModel())->modifyVarcharColumnD();