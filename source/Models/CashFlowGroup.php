<?php
namespace Source\Models;

use Source\Core\Model;

/**
 * CashFlowGroup C:\php-projects\sistema-contabil\source\Models
 * @link 
 * @author Roberto Dorado <robertodorado7@gmail.com>
 * @package Models
 */
class CashFlowGroup extends Model
{
    /** @var string uuid de identificação */
    protected string $uuid = "uuid";

    /** @var string  id do usuário */
    protected string $idUser = "id_user";

    /** @var string nome do grupo */
    protected string $groupName = "group_name";

    /** @var string Data de criação do registro */
    protected string $createdAt = "created_at";

    /** @var string Data de atualização do registro */
    protected string $updatedAt = "updated_at";

    /** @var string Coluna para soft delete do registro */
    protected string $deleted = "deleted";

    /**
     * CashFlowGroup constructor
     */
    public function __construct()
    {
        parent::__construct(CONF_DB_NAME . ".cash_flow_group", ["id"], [
            $this->uuid,
            $this->id_user,
            $this->groupName,
            $this->createdAt,
            $this->updatedAt,
            $this->deleted
        ]);
    }

    public function getDeleted()
    {
        return $this->deleted;
    }

    public function setDeleted(int $delete) 
    {
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
