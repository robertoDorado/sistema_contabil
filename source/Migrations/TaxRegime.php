<?php
namespace Source\Migrations;

use Source\Migrations\Core\DDL;
use Source\Models\TaxRegime as ModelsTaxRegime;

require dirname(dirname(__DIR__)) . "/vendor/autoload.php";
/**
 * TaxRegime Migrations
 * @link 
 * @author Roberto Dorado <robertodorado7@gmail.com>
 * @package Source\Migrations
 */
class TaxRegime
{
    /** @var DDL */
    private DDL $ddl;
    
    /**
     * TaxRegime constructor
     */
    public function __construct()
    {
        $this->ddl = new DDL(ModelsTaxRegime::class);
    }

    public function defineTable()
    {
        $this->ddl->setClassProperties();
        $this->ddl->setProperty('');
        $this->ddl->setProperty('');
        $this->ddl->setProperty('');
        $this->ddl->setKeysToProperties([
            "BIGINT AUTO_INCREMENT PRIMARY KEY",
            "VARCHAR(36) UNIQUE NOT NULL",
            "BIGINT NOT NULL",
            "BIGINT NOT NULL",
            "BIGINT UNIQUE NOT NULL",
            "DATE NOT NULL",
            "DATE NOT NULL",
            "TINYINT(1) NOT NULL",
            "CONSTRAINT fk_tax_regime_user FOREIGN KEY (id_user) REFERENCES user(id) ON UPDATE CASCADE ON DELETE CASCADE",
            "CONSTRAINT fk_tax_regime_company FOREIGN KEY (id_company) REFERENCES company(id) ON DELETE CASCADE ON UPDATE CASCADE",
            "CONSTRAINT fk_tax_regime_tax_regime_model FOREIGN KEY (tax_regime_id) REFERENCES tax_regime_model(id) ON DELETE CASCADE ON UPDATE CASCADE",
        ]);
        $this->ddl->setForeignKeyChecks(0)->dropTableIfExists()->createTableQuery()->setForeignKeyChecks(1)->executeQuery();
    }
}
executeMigrations(TaxRegime::class);