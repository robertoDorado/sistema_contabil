<?php
namespace Source\Models;

use Source\Core\Model;

/**
 * BalanceSheetExplanatoryNotes Models
 * @link 
 * @author Roberto Dorado <robertodorado7@gmail.com>
 * @package Source\Models
 */
class BalanceSheetExplanatoryNotes extends Model
{
    /** @var string Uuid */
    protected string $uuid = "uuid";

    /** @var string id_cash_flow */
    protected string $idBalanceSheet = "id_balance_sheet";

    /** @var string Nota */
    protected string $note = "note";

    /** @var string Deleted */
    protected string $deleted = "deleted";

    /**
     * BalanceSheetExplanatoryNotes constructor
     */
    public function __construct()
    {
        parent::__construct(CONF_DB_NAME . ".balance_sheet_explanatory_notes", ["id"], [
            $this->uuid,
            $this->idBalanceSheet,
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
