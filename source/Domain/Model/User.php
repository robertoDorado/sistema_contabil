<?php

namespace Source\Domain\Model;

use Exception;
use Ramsey\Uuid\Nonstandard\Uuid;
use Source\Domain\Support\Tools;
use Source\Models\User as ModelsUser;
use Source\Support\Message;

/**
 * User Domain\Model
 * @link 
 * @author Roberto Dorado <robertodorado7@gmail.com>
 * @package Source\Domain\Model
 */
class User
{
    /** @var ModelsUser Model do usuário */
    private ModelsUser $user;

    /** @var int Id do usuário */
    private int $id;

    /** @var string Uuid do usuário */
    private string $uuid;

    /** @var string E-mail do usuário */
    private string $email;

    /** @var string Nickname do usuário */
    private string $nickName;

    /** @var object|null */
    public object $data;

    /**
     * User constructor
     */
    public function __construct()
    {
        $this->user = new ModelsUser();
        $this->email = "";
        $this->nickName = "";
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

    /** @var ModelsUser[] */
    public function findAllUserJoinCustomerJoinSubscription(array $data): array
    {
        $data["user_columns"] = empty($data["user_columns"]) ? "*" : implode(", ", $data["user_columns"]);
        $data["customer_columns"] = empty($data["customer_columns"]) ? "*" : implode(", ", $data["customer_columns"]);
        $data["subscription_columns"] = empty($data["subscription_columns"]) ? "*" : implode(", ", $data["subscription_columns"]);

        $response = $this->user->find(
            "", 
            "",
            $data["user_columns"]
        )->join(
            CONF_DB_NAME . ".customer",
            "id",
            "",
            "",
            $data["customer_columns"],
            "id_customer",
            CONF_DB_NAME . ".user"
        )->leftJoin(
            CONF_DB_NAME . ".subscription",
            "customer_id",
            "",
            "",
            $data["subscription_columns"],
            "id",
            CONF_DB_NAME . ".customer"
        )->fetch(true);

        if (empty($response)) {
            return [];
        }

        return $response;
    }

    public function updateUserByCustomerId(array $data)
    {
        $tools = new Tools($this->user, ModelsUser::class);
        $response = $tools->updateData(
            "id_customer=:id_customer",
            ":id_customer={$data['id_customer']->getId()}",
            $data,
            "usuário não encontrado"
        );
        $this->data->message = !empty($tools->message) ? $tools->message : "";
        return !empty($response) ? true : false;
    }

    public function updateUserByEmail(array $data): bool
    {
        $tools = new Tools($this->user, ModelsUser::class);
        $response = $tools->updateData(
            "user_email=:user_email",
            ":user_email={$this->getEmail()}",
            $data,
            "usuário não encontrado"
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
            throw new Exception("uuid do usuário é inválido");
        }
        $this->uuid = $uuid;
    }

    public function findUserByEmail(array $columns = []): ?ModelsUser
    {
        $tools = new Tools($this->user, ModelsUser::class);
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

    public function login(string $password): ?ModelsUser
    {
        $tools = new Tools($this->user, ModelsUser::class);
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
        if (!empty($data["user_email"])) {
            $user = $this->user
                ->find("user_email=:user_email", ":user_email=" . $data["user_email"] . "")
                ->fetch();

            $message = new Message();
            if (!empty($user)) {
                $message->error("este usuário já foi cadastrado");
                $this->data->message = $message;
                return false;
            }
        }

        $tools = new Tools($this->user, ModelsUser::class);
        $response = $tools->persistData($data);
        $this->data->message = !empty($tools->message) ? $tools->message : "";

        !empty($response) ? $this->setId($tools->lastId) : null;
        return !empty($response) ? true : false;
    }
}
