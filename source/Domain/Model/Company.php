<?php
namespace Source\Domain\Model;

use Exception;
use Ramsey\Uuid\Nonstandard\Uuid;
use Source\Domain\Support\Tools;
use Source\Models\Company as ModelsCompany;
use Source\Support\Message;

/**
 * Company Domain\Model
 * @link 
 * @author Roberto Dorado <robertodorado7@gmail.com>
 * @package Source\Domain\Model
 */
class Company
{
    /** @var ModelsCompany Modelo tabela empresa */
    private ModelsCompany $company;

    /** @var int Id da tabela cash_flow */
    private int $id;

    /** @var string Uuid do cliente */
    private string $uuid;

    /** @var object|null */
    private object $data;

    /**
     * Company constructor
     */
    public function __construct()
    {
        $this->data = new \stdClass();
        $this->company = new ModelsCompany();
    }

    public function __set($name, $value)
    {
        $this->data->$name = $value;
    }

    public function __get($name)
    {
        return $this->data->$name ?? null;
    }

    public function findCompanyByUserId(array $columns = [], bool $fetchAll = false): ?ModelsCompany
    {
        if (empty($this->data->id_user)) {
            $message = new Message();
            $message->error("atributo id_user é obrigatório");
            $this->data->message = $message;
            return null;
        }

        $columns = !empty($columns) ? implode(", ", $columns) : "";
        return $this->company->find("id_user=:id_user", ":id_user=" . $this->data->id_user . "", $columns)
        ->fetch($fetchAll);
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
        $tools = new Tools($this->company, ModelsCompany::class);
        $response = $tools->persistData($data);
        $this->data->message = !empty($tools->message) ? $tools->message : "";

        !empty($response) ? $this->setId($tools->lastId) : null;
        return !empty($response) ? true : false;
    }
}
