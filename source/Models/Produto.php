<?php

namespace Source\Models;

use Source\Core\Model;

/**
 * Produto Models
 * @link 
 * @author André Santos <andre.santos@anexxa.com.br>
 * @package Source\Models
 */
class Produto extends Model 
{
    /**
     * Produto constructor
     */
    public function __construct()
    {
        parent::__construct('teste.produtos', ['id'], ['produto', 'valor_unitario']);
    }
}
