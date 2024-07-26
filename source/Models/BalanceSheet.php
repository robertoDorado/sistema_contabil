<?php
namespace Source\Models;

use Source\Core\Model;

/**
 * BalanceSheet Models
 * @link 
 * @author Roberto Dorado <robertodorado7@gmail.com>
 * @package Source\Models
 */
class BalanceSheet extends Model
{
    /** @var string uuid */
    protected string $uuid = "uuid";

    /** @var string id do usuário */
    protected string $idUser = "id_user";

    /** @var string id da empresa */
    protected string $idCompany = "id_company";

    /** @var string id do plano de contas */
    protected string $idChartOfAccount = "id_chart_of_account";

    /** @var string Natureza da conta (Tipo de conta) */
    protected string $accountType = "account_type";

    /** @var string Valor da conta */
    protected string $accountValue = "account_value";

    /** @var string Histórico da conta */
    protected string $historyAccount = "history_account";

    /** @var string Data criada */
    protected string $createdAt = "created_at";

    /** @var string Data de atualização */
    protected string $updatedAt = "updated_at";

    /** @var string Soft delete */
    protected string $deleted = "deleted";

    /**
     * BalanceSheet constructor
     */
    public function __construct()
    {
        parent::__construct(CONF_DB_NAME . ".balance_sheet", ["id"], [
            $this->uuid,
            $this->idUser,
            $this->idCompany,
            $this->idChartOfAccount,
            $this->accountType,
            $this->accountValue,
            $this->historyAccount,
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
}
