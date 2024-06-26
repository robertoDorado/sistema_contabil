<?php
namespace Source\Migrations;

require dirname(dirname(__DIR__)) . "/vendor/autoload.php";

use Source\Migrations\Core\DDL;
use Source\Models\InvestmentCashFlow as ModelsInvestmentCashFlow;

/**
 * InvestmentCashFlow Migrations
 * @link 
 * @author Roberto Dorado <robertodorado7@gmail.com>
 * @package Source\Migrations
 */
class InvestmentCashFlow
{
    /** @var DDL */
    private DDL $ddl;

    /**
     * InvestmentCashFlow constructor
     */
    public function __construct()
    {
        $this->ddl = new DDL(ModelsInvestmentCashFlow::class);    
    }

    public function defineTable()
    {
        $this->ddl->setClassProperties();
        $this->ddl->setProperty('');
        $this->ddl->setKeysToProperties([
            "BIGINT AUTO_INCREMENT PRIMARY KEY",
            "BIGINT NOT NULL UNIQUE",
            "TINYINT(1) NOT NULL",
            "CONSTRAINT fk_investment_cash_flow_group FOREIGN KEY (cash_flow_group_id) REFERENCES cash_flow_group(id) ON DELETE CASCADE ON UPDATE CASCADE"
        ]);
        $this->ddl->dropTableIfExists()->createTableQuery();
        return $this->ddl->executeQuery();
    }
}
executeMigrations(InvestmentCashFlow::class);