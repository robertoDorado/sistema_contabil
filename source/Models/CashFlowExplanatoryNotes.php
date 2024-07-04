<?php
namespace Source\Models;

use Source\Core\Model;

/**
 * CashFlowExplanatoryNotes Models
 * @link 
 * @author Roberto Dorado <robertodorado7@gmail.com>
 * @package Source\Models
 */
class CashFlowExplanatoryNotes extends Model
{
    /** @var string Uuid */
    protected string $uuid = "uuid";

    /** @var string id_cash_flow */
    protected string $idCashFlow = "id_cash_flow";

    /** @var string Nota */
    protected string $note = "note";

    /** @var string Deleted */
    protected string $deleted = "deleted";

    /**
     * CashFlowExplanatoryNotes constructor
     */
    public function __construct()
    {
        parent::__construct(CONF_DB_NAME . ".cash_flow_explanatory_notes", ["id"], [
            $this->uuid,
            $this->idCashFlow,
            $this->note,
            $this->deleted
        ]);
    }

    public function setDeleted(string $deleted)
    {
        $this->deleted = $deleted;
    }

    public function getDeleted()
    {
        return $this->deleted;
    }

    public function setUuid(string $uuid)
    {
        $this->uuid = $uuid;
    }

    public function getUuid()
    {
        return $this->uuid;
    }

    public function setNote(string $note)
    {
        $this->note = $note;
    }

    public function getNote()
    {
        return $this->note;
    }
}
