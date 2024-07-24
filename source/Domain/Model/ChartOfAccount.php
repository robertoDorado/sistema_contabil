<?php

namespace Source\Domain\Model;

use Exception;
use Ramsey\Uuid\Nonstandard\Uuid;
use Source\Domain\Support\Tools;
use Source\Models\ChartOfAccount as ModelsChartOfAccount;

/**
 * ChartOfAccount Domain\Model
 * @link 
 * @author Roberto Dorado <robertodorado7@gmail.com>
 * @package Source\Domain\Model
 */
class ChartOfAccount
{
    /** @var int Id da tabela cash_flow */
    private int $id;

    /** @var string Uuid do cliente */
    private string $uuid;

    /** @var object|null */
    private object $data;

    /** @var ModelsChartOfAccount */
    private ModelsChartOfAccount $chartOfAccount;

    /**
     * ChartOfAccount constructor
     */
    public function __construct()
    {
        $this->chartOfAccount = new ModelsChartOfAccount();
        $this->data = new \stdClass();
    }

    public function __set($name, $value)
    {
        $this->data->$name = $value;
    }

    public function __get($name)
    {
        return $this->data->$name ?? null;
    }

    /** @var ModelsChartOfAccount[] */
    public function findAllChartOfAccountJoinChartOfAccountGroup(array $columnsA, array $columnsB, array $params): array
    {
        $columnsA = empty($columnsA) ? "*" : implode(", ", $columnsA);
        $columnsB = empty($columnsB) ? "*" : implode(", ", $columnsB);
        $terms = "id_user=:id_user AND id_company=:id_company AND deleted=:deleted";
        $params = ":id_user={$params['id_user']}&:id_company={$params['id_company']}&:deleted={$params['deleted']}";

        $response = $this->chartOfAccount->find(
            $terms,
            $params,
            $columnsA
        )->join(
            CONF_DB_NAME . ".chart_of_account_group",
            "id",
            $terms,
            $params,
            $columnsB,
            "id_chart_of_account_group",
            CONF_DB_NAME . ".chart_of_account"
        )->fetch(true);

        if (empty($response)) {
            return [];
        }

        return $response;
    }

    /** @var ModelsChartOfAccount[] */
    public function findAllChartOfAccount(array $columns = [], array $params): array
    {
        $columns = empty($columns) ? "*" : implode(", ", $columns);
        $response = $this->chartOfAccount->find(
            "id_company=:id_company AND id_user=:id_user AND deleted=:deleted",
            ":id_company=" . $params["id_company"] . "&:id_user=" . $params["id_user"] . "&:deleted=" . $params["deleted"] . "",
            $columns
        )->fetch(true);

        if (empty($response)) {
            return [];
        }

        return $response;
    }

    public function findChartOfAccountByUuid(array $columns = []): ?ModelsChartOfAccount
    {
        $columns = empty($columns) ? "*" : implode(", ", $columns);
        return $this->chartOfAccount->find("uuid=:uuid", ":uuid=" . $this->getUuid() . "", $columns)->fetch();
    }

    public function updateChartOfAccountByUuid(array $data): bool
    {
        $tools = new Tools($this->chartOfAccount, ModelsChartOfAccount::class);
        $response = $tools->updateData(
            "uuid=:uuid",
            ":uuid={$data['uuid']}",
            $data,
            "registro plano de contas não encontrado"
        );
        $this->data->message = !empty($tools->message) ? $tools->message : "";
        return !empty($response) ? true : false;
    }

    public function persistData(array $data): bool
    {
        $tools = new Tools($this->chartOfAccount, ModelsChartOfAccount::class);
        $response = $tools->persistData($data);
        $this->data->message = !empty($tools->message) ? $tools->message : "";

        !empty($response) ? $this->setId($tools->lastId) : null;
        return !empty($response) ? true : false;
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
}
