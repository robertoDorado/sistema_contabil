<?php
namespace Source\Models;

use Source\Core\Model;

/**
 * ChartOfAccountModel Models
 * @link 
 * @author Roberto Dorado <robertodorado7@gmail.com>
 * @package Source\Models
 */
class ChartOfAccountModel extends Model
{
    /** @var string Uuid */
    protected string $uuid = "uuid";

    /** @var string Número da conta */
    protected string $accountNumber = "account_number";

    /** @var string Nome da conta */
    protected string $accountName = "account_name";

    /**
     * ChartOfAccountModel constructor
     */
    public function __construct()
    {
        parent::__construct(CONF_DB_NAME . ".chart_of_account_model", ["id"], [
            $this->uuid,
            $this->accountNumber,
            $this->accountName
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
}
