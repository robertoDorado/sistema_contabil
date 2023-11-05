<?php

namespace Source\Models;

use Source\Core\Model;

/**
 * TestModel Models
 * @author Roberto Dorado <robertodorado7@gmail.com>
 * @package Source\Models
 */
class TestModel extends Model
{
    /** @var string Coluna de exemplo A */
    private string $columnA;

    /** @var string Coluna de exemplo B */
    private string $columnB;

    /** @var string Coluna de exemplo C */
    private string $columnC;

    /** @var string Coluna de exemplo D */
    private string $columnD;

    /**
     * TestModel constructor
     */
    public function __construct()
    {
        // Params: [$entity, $protected_table_columns, $required_table_columns]
        parent::__construct("database.table", ["campos_protegidos"], [$this->columnA, $this->columnB, $this->columnC, $this->columnD]);
    }
}
