<?php
namespace Source\Models;

use Source\Core\Model;

/**
 * OperatingCashFlow Models
 * @link 
 * @author Roberto Dorado <robertodorado7@gmail.com>
 * @package Source\Models
 */
class OperatingCashFlow extends Model
{
    /** @var string Uuid do registro */
    protected string $uuid = "uuid";

    /** @var string Id do grupo de contas */
    protected string $cashFlowGroupId = "cash_flow_group_id";

    /** @var string Nome do grupo */
    protected string $groupName = "group_name";

    /** @var string Soft delete */
    protected string $deleted = "deleted";

    /**
     * OperatingCashFlow constructor
     */
    public function __construct()
    {
        parent::__construct(CONF_DB_NAME . ".operating_cash_flow", ["id"], [
            $this->uuid,
            $this->cashFlowGroupId,
            $this->groupName,
            $this->deleted
        ]);
    }

    public function getDeleted()
    {
        return $this->deleted;
    }

    public function setDeteleted(bool $isDeleted): void
    {
        $delete = $isDeleted ? 1 : 0;
        $this->deleted = $delete;
    }

    public function setUuid(string $uuid)
    {
        $this->uuid = $uuid;
    }

    public function getUuid()
    {
        return $this->uuid;
    }
}
