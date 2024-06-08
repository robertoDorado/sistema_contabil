<?php
namespace Source\Models;

use Source\Core\Model;
use Source\Domain\Model\User;

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

    /** @var string chave estrangeira da tabela empresa */
    protected string $idCompany = "id_company";

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
            $this->idCompany,
            $this->idCashFlowGroup,
            $this->entry,
            $this->history,
            $this->entryType,
            $this->createdAt, 
            $this->updatedAt,
            $this->deleted
        ]);
    }

    public function findGroupAccountsAgrupped(User $user): array
    {
        $companyId = empty(session()->user->company_id) ? 0 : session()->user->company_id;
        $stmt = $this->read("SELECT cg.group_name, 
        COUNT(cg.group_name) AS total_accounts FROM sistema_contabil.cash_flow c
        INNER JOIN sistema_contabil.cash_flow_group cg ON cg.id = c.id_cash_flow_group
        WHERE cg.id_user=:id_user_cg AND c.id_user=:id_user_c AND c.deleted=0 AND cg.deleted=0
        AND c.id_company=:c_id_company AND cg.id_company=:cg_id_company
        GROUP BY cg.group_name",
        "id_user_cg=" . $user->getId() . "&id_user_c=" 
        . $user->getId() . "&c_id_company=" . $companyId . "&cg_id_company=" . $companyId);

        if ($stmt->rowCount() == 0) {
            return [];
        }

        return $stmt->fetchAll(\PDO::FETCH_OBJ);
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
