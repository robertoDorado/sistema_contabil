<?php

namespace Source\Domain\Model;

use Exception;
use Ramsey\Uuid\Nonstandard\Uuid;
use Source\Domain\Support\Tools;
use Source\Models\ChartOfAccountModel as ModelsChartOfAccountModel;

/**
 * ChartOfAccountModel Domain\Model
 * @link 
 * @author Roberto Dorado <robertodorado7@gmail.com>
 * @package Source\Domain\Model
 */
class ChartOfAccountModel
{
    /** @var ModelsChartOfAccountModel */
    private ModelsChartOfAccountModel $chartOfAccountModel;

    /** @var int Id da tabela cash_flow */
    private int $id;

    /** @var string Uuid do cliente */
    private string $uuid;

    /** @var object|null */
    private object $data;

    /**
     * ChartOfAccountModel constructor
     */
    public function __construct()
    {
        $this->chartOfAccountModel = new ModelsChartOfAccountModel();
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

    /** @var ModelsChartOfAccountModel[] */
    public function findAllChartOfAccountModel(array $columns, bool $onlyData): array
    {
        $tools = new Tools($this->chartOfAccountModel, ModelsChartOfAccountModel::class);
        return $tools->findAllData($columns, $onlyData);
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
