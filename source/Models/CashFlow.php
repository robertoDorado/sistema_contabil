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
    /** @var string Uuid do fluxo de caixa */
    protected string $uuid = "uuid";

    /** @var string id do usuário (chave de relacionamento) */
    protected string $idUser = "id_user";

    /** @var string id do agrupamento de fluxo de caixa */
    protected string $idCashFlowGroup = "id_cash_flow_group";

    /** @var string Valor de entrada */
    protected string $entry = "entry";

    /** @var string Histórico dos lançamentos */
    protected string $history = "history";

    /** @var string Tipo de entrada */
    protected string $entryType = "entry_type";

    /** @var string Data de criação do registro */
    protected string $createdAt = "created_at";

    /** @var string Data de atualização do registro */
    protected string $updatedAt = "updated_at";

    /** @var string Coluna para soft delete do registro */
    protected string $deleted = "deleted";

    /**
     * ChashFlow constructor
     */
    public function __construct()
    {
        parent::__construct(CONF_DB_NAME . ".cash_flow", ["id"], [
            $this->uuid,
            $this->idUser,
            $this->idCashFlowGroup,
            $this->entry,
            $this->history,
            $this->entryType,
            $this->createdAt, 
            $this->updatedAt,
            $this->deleted
        ]);
    }

    public function getDeleted()
    {
        return $this->deleted;
    }

    public function setDeleted(int $delete) {
        $this->deleted = $delete;
    }

    public function getUuid()
    {
        return $this->uuid;
    }

    public function setUuid(string $uuid)
    {
        $this->uuid = $uuid;
    }

    public function setHistory($history)
    {
        $this->history = $history;
    }

    public function setEntry($entry)
    {
        $this->entry = $entry;
    }

    public function getHistory()
    {
        return $this->history;
    }

    public function getEntry()
    {
        return $this->entry;
    }
}
