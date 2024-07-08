<?php
namespace Source\Models;

use Source\Core\Model;

/**
 * HistoryAudit Models
 * @link 
 * @author Roberto Dorado <robertodorado7@gmail.com>
 * @package Source\Models
 */
class HistoryAudit extends Model
{
    /** @var string Uuid */
    protected string $uuid = "uuid";

    /** @var string Id da empresa */
    protected string $idCompany = "id_company";

    /** @var string Id do usuário */
    protected string $idUser = "id_user";

    /** @var string Id do relatório */
    protected string $idReport = "id_report";

    /** @var string Histórico da transação */
    protected string $historyTransaction = "history_transaction";

    /** @var string Valor da transação */
    protected string $transactionValue = "transaction_value";

    /** @var string Data e Hora da transação */
    protected string $createdAt = "created_at";

    /** @var string Soft delete */
    protected string $deleted = "deleted";

    /**
     * HistoryAudit constructor
     */
    public function __construct()
    {
        parent::__construct(CONF_DB_NAME . ".history_audit", ["id"], [
            $this->uuid,
            $this->idCompany,
            $this->idUser,
            $this->idReport,
            $this->historyTransaction,
            $this->transactionValue,
            $this->createdAt,
            $this->deleted
        ]);
    }

    public function getUuid()
    {
        return $this->uuid;
    }

    public function setUuid(string $uuid)
    {
        $this->uuid = $uuid;
    }

    public function getDeleted()
    {
        return $this->deleted;
    }

    public function setDeleted(int $delete) {
        $this->deleted = $delete;
    }
}
