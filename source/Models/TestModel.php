<?php

namespace Source\Models;

use Source\Core\Model;

/**
 * TestModel Models
 * @author André Santos <andre.santos@anexxa.com.br>
 * @package Source\Models
 */
class TestModel extends Model
{
    /**
     * TestModel constructor
     */
    public function __construct()
    {
        // Params: [$entity, $protected_table_columns, $required_table_columns]
        parent::__construct("database.table", ["campos_protegidos"], ["campos_obrigatorios"]);
    }
}
