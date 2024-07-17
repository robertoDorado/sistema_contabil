<?php
namespace Source\Models;

use Source\Core\Model;

/**
 * ChartOfAccount Models
 * @link 
 * @author Roberto Dorado <robertodorado7@gmail.com>
 * @package Source\Models
 */
class ChartOfAccount extends Model
{
    /** @var string Uuid do fluxo de caixa */
    protected string $uuid = "uuid";

    /** @var string chave estrangeira da tabela empresa */
    protected string $idCompany = "id_company";

    /** @var string id do usuário (chave de relacionamento) */
    protected string $idUser = "id_user";

    /** @var string Número da conta */
    protected string $accountNumber = "account_number";

    /** @var string Nome da conta */
    protected string $accountName = "account_name";

    /** @var string Coluna para soft delete */
    protected string $deleted = "deleted";

    /**
     * ChartOfAccount constructor
     */
    public function __construct()
    {
        parent::__construct(CONF_DB_NAME . ".chart_of_account", ["id"], [
            $this->uuid,
            $this->idCompany,
            $this->idUser,
            $this->accountNumber,
            $this->accountName,
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
