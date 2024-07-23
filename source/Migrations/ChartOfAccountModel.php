<?php
namespace Source\Migrations;

require dirname(dirname(__DIR__)) . "/vendor/autoload.php";

use Exception;
use Ramsey\Uuid\Nonstandard\Uuid;
use Source\Core\Connect;
use Source\Migrations\Core\DDL;
use Source\Models\ChartOfAccountModel as ModelsChartOfAccountModel;

/**
 * ChartOfAccountModel Migrations
 * @link 
 * @author Roberto Dorado <robertodorado7@gmail.com>
 * @package Source\Migrations
 */
class ChartOfAccountModel
{
    /** @var  DDL DDL */
    private DDL $ddl;

    /**
     * ChartOfAccountModel constructor
     */
    public function __construct()
    {
        $this->ddl = new DDL(ModelsChartOfAccountModel::class);
    }

    public function defineTable()
    {
        $file = dirname(dirname(__DIR__)) . "/config/chart-of-account-model.txt";
        $chartOfAccountContent = file_get_contents($file);

        if (empty($chartOfAccountContent)) {
            throw new \Exception("arquivo modelo plano de contas está vazio");
        }

        $this->ddl->setClassProperties();
        $this->ddl->setKeysToProperties([
            "BIGINT AUTO_INCREMENT PRIMARY KEY",
            "VARCHAR(36) UNIQUE NOT NULL",
            "VARCHAR(255) NOT NULL",
            "VARCHAR(255) NOT NULL"
        ]);
        $this->ddl->setForeignKeyChecks(0)->dropTableIfExists()->createTableQuery()->setForeignKeyChecks(1);
        $this->ddl->executeQuery();

        $data = [];
        preg_match_all("/[\d\.]+/", $chartOfAccountContent, $data["account_value"]);
        preg_match_all("/[À-ÿA-Za-z\s,\(\)\-\/]+/", $chartOfAccountContent, $data["account_name"]);

        if (empty($data["account_name"][0])) {
            throw new Exception("erro ao tentar capturar os nomes das contas");
        }
        
        if (empty($data["account_value"][0])) {
            throw new Exception("erro ao tentar capturar os valores das contas");
        }
        
        $data["account_name"] = array_reduce($data["account_name"][0], function($acc, $item) {
            $acc[] = $item;
            return $acc;
        }, []);
        
        $data["account_value"] = array_reduce($data["account_value"][0], function($acc, $item) {
            $acc[] = $item;
            return $acc;
        }, []);
        
        $data["account_name"] = array_map(function($item) {
            return trim($item);
        }, $data["account_name"]);
        
        $data["account_name"] = array_filter($data["account_name"], function($item) {
            if (!empty($item)) {
                return $item;
            }
        });

        $data["account_name"] = array_values($data["account_name"]);
        Connect::getInstance()->beginTransaction();
        foreach ($data["account_value"] as $key => $value) {
            $chartOfAccountModel = new ModelsChartOfAccountModel();
            $chartOfAccountModel->uuid = Uuid::uuid4();
            $chartOfAccountModel->account_number = $value;
            $chartOfAccountModel->account_name = $data["account_name"][$key];
            if (!$chartOfAccountModel->save()) {
                Connect::getInstance()->rollBack();
            }
        }
        Connect::getInstance()->commit();
    }
}
executeMigrations(ChartOfAccountModel::class);
