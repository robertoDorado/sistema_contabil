<?php

namespace Source\Domain\Model;

use Exception;
use Ramsey\Uuid\Nonstandard\Uuid;
use Source\Domain\Support\Tools;
use Source\Models\BalanceSheet as ModelsBalanceSheet;

/**
 * BalanceSheet Domain\Model
 * @link 
 * @author Roberto Dorado <robertodorado7@gmail.com>
 * @package Source\Domain\Model
 */
class BalanceSheet
{
    /** @var int Id da tabela balanço patrimonial */
    private int $id;

    /** @var string Uuid do registro */
    private string $uuid;

    /** @var object|null */
    private object $data;

    /** @var ModelsBalanceSheet */
    private ModelsBalanceSheet $balanceSheet;

    /**
     * BalanceSheet constructor
     */
    public function __construct()
    {
        $this->balanceSheet = new ModelsBalanceSheet();
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

    public function updateBalanceSheetDataByUuid(array $data)
    {
        $tools = new Tools($this->balanceSheet, ModelsBalanceSheet::class);
        $response = $tools->updateData(
            "uuid=:uuid",
            ":uuid={$data['uuid']}",
            $data,
            "registro do balanço patrimonial não encontrado"
        );
        $this->data->message = !empty($tools->message) ? $tools->message : "";
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

    public function persistData(array $data): bool
    {
        $tools = new Tools($this->balanceSheet, ModelsBalanceSheet::class);
        $response = $tools->persistData($data);
        $this->data->message = !empty($tools->message) ? $tools->message : "";

        !empty($response) ? $this->setId($tools->lastId) : null;
        return !empty($response) ? true : false;
    }
}
