<?php
namespace Source\Domain\Model;

use Exception;
use Ramsey\Uuid\Nonstandard\Uuid;
use Source\Domain\Support\Tools;
use Source\Models\OperatingCashFlow as ModelsOperatingCashFlow;

/**
 * OperatingCashFlow Domain\Model
 * @link 
 * @author Roberto Dorado <robertodorado7@gmail.com>
 * @package Source\Domain\Model
 */
class OperatingCashFlow
{
    /** @var ModelsOperatingCashFlow */
    private ModelsOperatingCashFlow $operatingCashFlow;

    /** @var object */
    private object $data;

    /** @var string */
    private string $uuid;

    /**
     * OperatingCashFlow constructor
     */
    public function __construct()
    {
        $this->operatingCashFlow = new ModelsOperatingCashFlow();
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

    function findOperatingCashFlowByUuid(array $columns): ?ModelsOperatingCashFlow
    {
        $columns = empty($columns) ? "*" : implode(", ", $columns);
        return $this->operatingCashFlow->find("uuid=:uuid", ":uuid=" . $this->getUuid() . "", $columns)->fetch();
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

    public function persistData(array $data): bool
    {
        $tools = new Tools($this->operatingCashFlow, ModelsOperatingCashFlow::class);
        $response = $tools->persistData($data);
        $this->data->message = !empty($tools->message) ? $tools->message : "";

        !empty($response) ? $this->setId($tools->lastId) : null;
        return !empty($response) ? true : false;
    }
}
