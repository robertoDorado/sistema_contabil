<?php
namespace Source\Models;

use Source\Core\Model;

/**
 * ReportSystem Models
 * @link 
 * @author Roberto Dorado <robertodorado7@gmail.com>
 * @package Source\Models
 */
class ReportSystem extends Model
{
    /** @var string Uuid */
    protected string $uuid = "uuid";

    /** @var string Nome do relatório */
    protected string $reportName = "report_name";

    /**
     * ReportSystem constructor
     */
    public function __construct()
    {
        parent::__construct(CONF_DB_NAME . ".report_system", ["id"], [
            $this->uuid,
            $this->reportName,
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
