<?php
namespace Source\Models;

use Source\Core\Model;

/**
 * FinancingCashFlow Models
 * @link 
 * @author Roberto Dorado <robertodorado7@gmail.com>
 * @package Source\Models
 */
class FinancingCashFlow extends Model
{
    /** @var string Id do grupo de contas */
    protected string $cashFlowGroupId = "cash_flow_group_id";

    /** @var string Soft delete */
    protected string $deleted = "deleted";

    /**
     * FinancingCashFlow constructor
     */
    public function __construct()
    {
        parent::__construct(CONF_DB_NAME . ".financing_cash_flow", ["id"], [
            $this->cashFlowGroupId,
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
}
