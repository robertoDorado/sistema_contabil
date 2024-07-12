<?php

namespace Source\Domain\Model;

use Exception;
use Ramsey\Uuid\Nonstandard\Uuid;
use Source\Domain\Support\Tools;
use Source\Models\HistoryAudit as ModelsHistoryAudit;

/**
 * HistoryAudit Domain\Model
 * @link 
 * @author Roberto Dorado <robertodorado7@gmail.com>
 * @package Source\Domain\Model
 */
class HistoryAudit
{
    /** @var ModelsHistoryAudit */
    private ModelsHistoryAudit $historyAudit;

    /** @var object */
    private object $data;

    /** @var int Id da tabela cash_flow */
    private int $id;

    /** @var string Uuid do cliente */
    private string $uuid;

    /**
     * HistoryAudit constructor
     */
    public function __construct()
    {
        $this->data = new \stdClass();
        $this->historyAudit = new ModelsHistoryAudit();
    }

    public function __set($name, $value)
    {
        $this->data->$name = $value;
    }

    public function __get($name)
    {
        return $this->data->$name ?? null;
    }

    public function findHistoryAndAuditByUuid(array $columns): ?ModelsHistoryAudit
    {
        $columns = empty($columns) ? "*" : implode(", ", $columns);
        return $this->historyAudit->find("uuid=:uuid", ":uuid=" . $this->getUuid() . "", $columns)->fetch();
    }

    /** @var ModelsHistoryAudit[] */
    public function findAllHistoryAndAuditJoinReportSystem(array $columnsH, array $columnsR, User $user, int $companyId, bool $deleted): array
    {
        $columnsH = empty($columnsH) ? "*" : implode(", ", $columnsH);
        $columnsR = empty($columnsR) ? "*" : implode(", ", $columnsR);
        $deleted = !$deleted ? 0 : 1;

        $response = $this->historyAudit->find(
            "id_user=:id_user AND id_company=:id_company AND deleted=:deleted",
            ":id_user=" . $user->getId() . "&:id_company=" . $companyId . "&:deleted=" . $deleted . "",
            $columnsH
        )->join(
            CONF_DB_NAME . ".report_system",
            "id",
            "",
            "",
            $columnsR,
            "id_report",
            CONF_DB_NAME . ".history_audit"
        )->fetch(true);

        if(empty($response)) {
            return [];
        }

        return $response;
    }

    public function getId(): int
    {
        if (empty($this->id)) {
            throw new Exception("id não atribuido");
        }
        return $this->id;
    }

    public function setId(int $id): void
    {
        $this->id = $id;
    }

    public function getUuid(): string
    {
        return $this->uuid;
    }

    public function setUuid(string $uuid): void
    {
        if (!Uuid::isValid($uuid)) {
            throw new Exception("uuid inválido");
        }
        $this->uuid = $uuid;
    }

    public function updateHistoryAuditByUuid(array $data): bool
    {
        $tools = new Tools($this->historyAudit, ModelsHistoryAudit::class);
        $response = $tools->updateData(
            "uuid=:uuid",
            ":uuid={$data['uuid']}",
            $data,
            "registro não encontrado"
        );
        $this->data->message = !empty($tools->message) ? $tools->message : "";
        return !empty($response) ? true : false;
    }

    public function persistData(array $data): bool
    {
        $tools = new Tools($this->historyAudit, ModelsHistoryAudit::class);
        $response = $tools->persistData($data);
        $this->data->message = !empty($tools->message) ? $tools->message : "";

        !empty($response) ? $this->setId($tools->lastId) : null;
        return !empty($response) ? true : false;
    }
}
