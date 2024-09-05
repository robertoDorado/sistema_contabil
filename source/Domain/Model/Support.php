<?php
namespace Source\Domain\Model;

use Exception;
use Ramsey\Uuid\Nonstandard\Uuid;
use Source\Domain\Support\Tools;
use Source\Models\Support as ModelsSupport;
use Source\Support\Message;

/**
 * Support Domain\Model
 * @link 
 * @author Roberto Dorado <robertodorado7@gmail.com>
 * @package Source\Domain\Model
 */
class Support
{
    /** @var object */
    private object $data;

    /** @var int Id do usuário */
    private int $id;

    /** @var string Uuid do usuário */
    private string $uuid;

    /** @var string E-mail do usuário */
    private string $email;

    /** @var string Nickname do usuário */
    private string $nickName;

    /** @var ModelsSupport */
    private ModelsSupport $support;

    /**
     * Support constructor
     */
    public function __construct()
    {
        $this->data = new \stdClass();
        $this->email = "";
        $this->nickName = "";
        $this->support = new ModelsSupport();
    }

    public function __get($name)
    {
        return $this->data->$name ?? null;
    }

    public function __set($name, $value)
    {
        $this->data->$name = $value;
    }

    public function findUserSupportByUuid(array $columns = []): ?ModelsSupport
    {
        $columns = empty($columns) ? "*" : implode(", ", $columns);
        return $this->support->find("uuid=:uuid", ":uuid=" . $this->getUuid() . "", $columns)->fetch();
    }

    public function findAllUserSupport(array $columns = []): array
    {
        $columns = empty($columns) ? "*" : implode(", ", $columns);
        $response = $this->support->find("", "", $columns)->fetch(true);

        if (empty($response)) {
            return [];
        }

        return $response;
    }

    public function findUserByEmail(array $columns = []): ?ModelsSupport
    {
        $tools = new Tools($this->support, ModelsSupport::class);
        $response = $tools->findUserByEmail([
            "terms" => [
                "user_email" => ":user_email"
            ],
            "params" => [
                ":user_email" => $this->getEmail()
            ],
            "columns" => $columns
        ]);
        $this->data->message = !empty($tools->message) ? $tools->message : "";
        return $response;
    }

    public function login(string $password): ?ModelsSupport
    {
        $tools = new Tools($this->support, ModelsSupport::class);
        $response = $tools->login([
            "terms_validate_email" => [
                "user_email" => ":user_email"
            ],
            "params_validate_email" => [
                ":user_email" => $this->getEmail()
            ],
            "terms_validate_name" => [
                "user_nick_name" => ":user_nick_name"
            ],
            "params_validate_name" => [
                ":user_nick_name" => $this->getNickName()
            ],
            "password" => $password
        ]);
        $this->data->message = !empty($tools->message) ? $tools->message : "";
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

    public function getEmail(): string
    {
        return $this->email;
    }

    public function setEmail(string $email): void
    {
        if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
            throw new Exception("E-mail inválido");
        }
        $this->email = $email;
    }

    public function getNickName(): string
    {
        return $this->nickName;
    }

    public function setNickName(string $nickName): void
    {
        $this->nickName = $nickName;
    }

    public function persistData(array $data): bool
    {
        $tools = new Tools($this->support, ModelsSupport::class);
        $response = $tools->persistData($data);
        $this->data->message = !empty($tools->message) ? $tools->message : "";

        !empty($response) ? $this->setId($tools->lastId) : null;
        return !empty($response) ? true : false;
    }
}
