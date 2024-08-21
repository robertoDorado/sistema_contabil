<?php
namespace Source\Domain\Model;

use Exception;
use Ramsey\Uuid\Nonstandard\Uuid;
use Source\Domain\Support\Tools;
use Source\Models\ChartOfAccountGroup as ModelsChartOfAccountGroup;

/**
 * ChartOfAccountGroup Domain\Model
 * @link 
 * @author Roberto Dorado <robertodorado7@gmail.com>
 * @package Source\Domain\Model
 */
class ChartOfAccountGroup
{
    /** @var int Id da tabela cash_flow */
    private int $id;

    /** @var string Uuid do cliente */
    private string $uuid;

    /** @var object|null */
    private object $data;

    /** @var ModelsChartOfAccountGroup */
    private ModelsChartOfAccountGroup $chartOfAccountGroup;

    /**
     * ChartOfAccountGroup constructor
     */
    public function __construct()
    {
        $this->data = new \stdClass();
        $this->chartOfAccountGroup = new ModelsChartOfAccountGroup();
    }

    public function __set($name, $value)
    {
        $this->data->$name = $value;
    }

    public function __get($name)
    {
        return $this->data->$name ?? null;
    }

    public function findChartOfAccountGroupById(array $columns): ?ModelsChartOfAccountGroup
    {
        $columns = empty($columns) ? "*" : implode(", ", $columns);
        return $this->chartOfAccountGroup->findById($this->getId(), $columns);
    }

    public function findChartOfAccountGroupByAccountNumber(array $columns, array $params): ?ModelsChartOfAccountGroup
    {
        $columns = empty($columns) ? "*" : implode(", ", $columns);
        return $this->chartOfAccountGroup->find(
            "account_number=:account_number AND id_company=:id_company AND id_user=:id_user AND deleted=:deleted", 
            ":account_number=" . $params["account_number"] . "&:id_company=" . $params["id_company"] . "&:id_user=" . $params["id_user"] . "&:deleted=" . $params["deleted"] . "", 
            $columns    
        )->fetch();
    }

    public function findChartOfAccountGroupByUuid(array $columns): ?ModelsChartOfAccountGroup
    {
        $columns = empty($columns) ? "*" : implode(", ", $columns);
        return $this->chartOfAccountGroup->find("uuid=:uuid", ":uuid=" . $this->getUuid() . "", $columns)->fetch();
    }

    /** @var ModelsChartOfAccount[] */
    public function findAllChartOfAccountGroup(array $columns = [], array $params): array
    {
        $columns = empty($columns) ? "*" : implode(", ", $columns);
        $response = $this->chartOfAccountGroup->find(
            "id_company=:id_company AND id_user=:id_user AND deleted=:deleted", 
            ":id_company=" . $params["id_company"] . "&:id_user=" . $params["id_user"] . "&:deleted=" . $params["deleted"] . "",
            $columns
        )->fetch(true);

        if (empty($response)) {
            return [];
        }

        return $response;
    }

    public function updateChartOfAccountGroupByUuid(array $data): bool
    {
        $tools = new Tools($this->chartOfAccountGroup, ModelsChartOfAccountGroup::class);
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
        $tools = new Tools($this->chartOfAccountGroup, ModelsChartOfAccountGroup::class);
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
