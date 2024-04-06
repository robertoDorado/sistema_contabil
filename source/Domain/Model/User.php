<?php
namespace Source\Domain\Model;

use Exception;
use PDOException;
use Ramsey\Uuid\Nonstandard\Uuid;
use Source\Core\Connect;
use Source\Models\User as ModelsUser;

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

    /**
     * User constructor
     */
    public function __construct()
    {
        $this->user = new ModelsUser();
    }

    public function getUuid()
    {
        if (empty($this->uuid)) {
            throw new Exception("uuid do usuário não pode estar vazio");
        }
        return $this->uuid;
    }

    public function setUuid(string $uuid)
    {
        if (!Uuid::isValid($uuid)) {
            throw new Exception("uuid do usuário é inválido");
        }
        $this->uuid = $uuid;
    }

    public function findUserByEmail(array $columns = [])
    {
        $columns = empty($columns) ? "*" : implode(", ", $columns);
        $data = $this->user
        ->find("user_email=:user_email", ":user_email=" . $this->getEmail() . "", $columns)->fetch();
        
        if (empty($data)) {
            return json_encode(["user_not_exists" => "usuário não existe"]);
        }
        
        if (!empty($data->getDeleted())) {
            return json_encode(["access_denied" => "acesso negado"]);
        }

        return $data;
    }

    public function getEmail()
    {
        if (empty($this->email)) {
            throw new Exception("E-mail do usuário não encontrado");
        }
        return $this->email;
    }

    public function setEmail(string $email)
    {
        if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
            throw new Exception("E-mail inválido");
        }
        $this->email = $email;
    }

    public function getId()
    {
        if (empty($this->id)) {
            throw new \Exception("id não atribuido");
        }
        return $this->id;
    }

    public function setId(int $id)
    {
        if (!filter_var($id, FILTER_VALIDATE_INT)) {
            throw new Exception("valor do id precisa ser do tipo inteiro");
        }
        $this->id = $id;
    }

    public function login(string $password)
    {
        $user = $this->user->find("user_email=:user_email",
            ":user_email=" . $this->getEmail() . "")->fetch();
        
        if (empty($user)) {
            $user = new ModelsUser();
            $user = $user->find("user_nick_name=:user_nick_name",
            ":user_nick_name=" . $this->getNickName() . "")->fetch();
        }
        
        if (empty($user)) {
            return json_encode(["error" => "usuário não registrado"]);
        }

        if (!empty($user->getDeleted())) {
            return json_encode(["error" => "acesso negado"]);
        }

        if ($this->validatePassword($password, $user)) {
            return $user;
        }
    }

    private function validatePassword(string $password, mixed $userData)
    {
        if (!password_verify($password, $userData->user_password)) {
            return json_encode(["error" => "usuário não autenticado"]);
        }
        return true;
    }

    public function getNickName()
    {
        if (empty($this->nickName)) {
            throw new Exception("nickname não foi atribuido");
        }
        return $this->nickName;
    }

    public function setNickName(string $nickName)
    {
        $this->nickName = $nickName;
    }

    public function persistData(array $data)
    {
        if (empty($data)) {
            return json_encode(["invalid_persist_data" => "dados inválidos"]);
        }

        validateModelProperties(ModelsUser::class, $data);

        if (!empty($data["user_email"])) {
            $user = $this->user
            ->find("user_email=:user_email", ":user_email=" . $data["user_email"] . "")
            ->fetch();
            
            if (!empty($user)) {
                return json_encode(["error_user_exists" => "este usuário já foi cadastrado"]);
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