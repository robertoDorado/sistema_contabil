<?php
namespace Source\Domain\Model;

use Exception;
use Ramsey\Uuid\Nonstandard\Uuid;
use Source\Core\Connect;
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

    public function updateUserByCustomerId(array $data)
    {
        $message = new Message();
        if (empty($data)) {
            $message->error("array data não pode estar vazio");
            $this->data->message = $message;
            return false;
        }

        $idCustomer = $data['id_customer']->getId();
        $userData = $this->user
        ->find("id_customer=:id_customer", ":id_customer={$idCustomer}")
        ->fetch();

        if (empty($userData)) {
            $message->error("usuário não encontrado");
            $this->data->message = $message;
            return false;
        }

        $verifyKeys = [
            "id_customer" => function($value) {
                if (!$value instanceof Customer) {
                    throw new Exception("instância do cliente inválida");
                }
                return $value->getId();
            },
            "user_email" => function($value) {
                if (!filter_var($value, FILTER_VALIDATE_EMAIL)) {
                    throw new Exception("e-mail do usuário está inválido");;
                }
                return $value;
            }
        ];

        foreach ($data as $key => &$value) {
            if (!empty($verifyKeys[$key])) {
                $value = $verifyKeys[$key]($value);
            }
            $userData->$key = $value;
        }

        $userData->setRequiredFields(array_keys($data));
        return $userData->save();
    }

    public function updateUserByEmail(array $data): bool
    {
        $message = new Message();
        if (empty($data)) {
            $message->error("array data não pode estar vazio");
            $this->data->message = $message;
            return false;
        }
        
        $userData = $this->user
        ->find("user_email=:user_email", ":user_email={$this->getEmail()}")->fetch();

        if (empty($userData)) {
            $message->error("usuário não encontrado");
            $this->data->message = $message;
            return false;
        }

        $verifyKeys = [
            "id_customer" => function($value) {
                if (!$value instanceof Customer) {
                    throw new Exception("instância do cliente inválida");
                }
                return $value->getId();
            }
        ];

        foreach ($data as $key => &$value) {
            if (!empty($verifyKeys[$key])) {
                $value = $verifyKeys[$key]($value);
            }
            $userData->$key = $value;
        }

        $userData->setRequiredFields(array_keys($data));
        return $userData->save();
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
        $columns = empty($columns) ? "*" : implode(", ", $columns);
        $data = $this->user
        ->find("user_email=:user_email", ":user_email=" . $this->getEmail() . "", $columns)->fetch();
        
        $message = new Message();
        if (empty($data)) {
            $message->error("usuário não existe");
            $this->data->message = $message;
            return null;
        }
        
        if (!empty($data->getDeleted())) {
            $message->error("acesso negado");
            $this->data->message = $message;
            return null;
        }

        return $data;
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
        $user = $this->user->find("user_email=:user_email",
        ":user_email=" . $this->getEmail() . "")->fetch();
        
        $message = new Message();
        if (empty($user)) {
            $user = new ModelsUser();
            $user = $user->find("user_nick_name=:user_nick_name",
            ":user_nick_name=" . $this->getNickName() . "")->fetch();
        }
        
        if (empty($user)) {
            $message->error("usuário não registrado");
            $this->data->message = $message;
            return null;
        }

        if (!empty($user->getDeleted())) {
            $message->error("acesso negado");
            $this->data->message = $message;
            return null;
        }

        $response = $this->validatePassword($password, $user);
        if (empty($response)) {
            $message->error("usuário não autenticado");
            $this->data->message = $message;
            return null;
        }
        return $user;
    }

    private function validatePassword(string $password, ModelsUser $userData): bool
    {
        if (!password_verify($password, $userData->user_password)) {
            return false;
        }
        return true;
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
        $message = new Message();
        if (empty($data)) {
            $message->error("dados inválidos");
            $this->data->message = $message;
            return false;
        }

        validateModelProperties(ModelsUser::class, $data);

        if (!empty($data["user_email"])) {
            $user = $this->user
            ->find("user_email=:user_email", ":user_email=" . $data["user_email"] . "")
            ->fetch();
            
            if (!empty($user)) {
                $message->error("este usuário já foi cadastrado");
                $this->data->message = $message;
                return false;
            }
        }

        $verifyKeys = [
            "id_customer" => function($value) {
                if (!$value instanceof Customer) {
                    throw new Exception("instância do cliente está incorreta");
                }
                return $value->getId();
            },
            "uuid" => function($value) {
                if (!Uuid::isValid($value)) {
                    throw new Exception("uuid inválido");
                }
                return $value;
            }
        ];

        foreach ($data as $key => &$value) {
            if (!empty($verifyKeys[$key])) {
                $value = $verifyKeys[$key]($value);
            }
            $this->user->$key = $value;
        }

        $this->user->save();
        $this->setId(Connect::getInstance()->lastInsertId());
        return true;
    }
}