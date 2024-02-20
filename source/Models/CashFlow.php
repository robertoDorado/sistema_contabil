<?php
namespace Source\Models;

use Source\Core\Model;

/**
 * CashFlow Models
 * @link 
 * @author Roberto Dorado <robertodorado7@gmail.com>
 * @package Source\Models
 */
class CashFlow extends Model
{
    /** @var string Id do usuário */
    private string $idUser = "id_user";

    /** @var string Valor de entrada */
    private string $entry = "entry";

    /** @var string Histórico dos lançamentos */
    private string $history = "history";

    /** @var string Tipo de entrada */
    private string $entryType = "entry_type";

    /** @var string Data de criação do registro */
    private string $createdAt = "created_at";

    /** @var string Data de atualização do registro */
    private string $updatedAt = "updated_at";

    /**
     * ChashFlow constructor
     */
    public function __construct()
    {
        parent::__construct(CONF_DB_NAME . ".cash_flow", ["id"], [
            $this->idUser, 
            $this->entry,
            $this->history,
            $this->entryType,
            $this->createdAt, 
            $this->updatedAt
        ]);
    }
}
