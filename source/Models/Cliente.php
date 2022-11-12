<?php

namespace Source\Models;

use Source\Core\Model;

/**
 * Cliente Models
 * @author André Santos <andre.santos@anexxa.com.br>
 * @package Source\Models
 */
class Cliente extends Model
{
    /**
     * Cliente constructor
     */
    public function __construct()
    {
        parent::__construct("teste.clientes", ["id"], ["nome", "email", "cpf", "telefone"]);
    }
}
