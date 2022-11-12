<?php

namespace Source\Models;

use Source\Core\Model;
/**
 * Pedido Models
 * @link 
 * @author André Santos <andre.santos@anexxa.com.br>
 * @package Source\Models
 */
class Pedido extends Model
{
    /**
     * Pedido constructor
     */
    public function __construct()
    {
        parent::__construct("teste.pedidos", ["id"], ["id_cliente", "id_produto", "quantidade", "valor_total"]);
    }
}
