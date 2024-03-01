<?php
namespace Source\Domain\Model;

use Exception;
use PDOException;
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

    /**
     * User constructor
     */
    public function __construct()
    {
        $this->user = new ModelsUser();
    }

    public function updateUserByUuid(array $data)
    {
        if (empty($data)) {
            return json_encode(["data_is_empty" => "data não pode ser vazio"]);
        }

        $userData = $this->user->find("uuid=:uuid", ":uuid={$data['uuid']}")->fetch();
        if (empty($userData)) {
            return json_encode(["user_not_found" => "usuário não encontrado"]);
        }
        
        foreach($data as $key => $value) {
            $userData->$key = $value;
        }
        
        validateModelProperties(ModelsUser::class, $data);
        $userData->setRequiredFields(array_keys($data));

        if (!$userData->save()) {
            if (!empty($userData->fail())) {
                throw new PDOException($userData->fail()->getMessage());
            }else {
                throw new Exception($userData->message()->getText());
            }
        }
        return true;
    }

    public function dropUserByUuid(string $uuid)
    {
        $userData = $this->user->find("uuid=:uuid", ":uuid={$uuid}")->fetch();
        if (empty($userData)) {
            throw new \Exception("usuário não encontrado");
        }
        
        if (!$userData->destroy()) {
            throw new PDOException($userData->fail()->getMessage());
        }
    }

    public function findUserByUuid(string $uuid)
    {
        $userData = $this->user->find("uuid=:uuid", ":uuid={$uuid}")->fetch();
        if (empty($userData)) {
            return json_encode(["user_not_found" => "usuário não encontrado"]);
        }

        if (!empty($userData->getDeleted())) {
            return json_encode(["access_denied" => "acesso negado"]);
        }

        return $userData;
    }

    public function findUserByEmail(string $email, array $columns = [])
    {
        $this->user = new ModelsUser();

        $columns = empty($columns) ? "*" : implode(", ", $columns);
        $data = $this->user
            ->find("user_email=:user_email", ":user_email=" . $email . "", $columns)->fetch();
        
        if (empty($data)) {
            return json_encode(["user_not_exists" => "usuário não existe"]);
        }
        
        if (!empty($data->getDeleted())) {
            return json_encode(["access_denied" => "acesso negado"]);
        }

        return $data;
    }

    public function dropUserById(int $id)
    {
        $user = $this->user->findById($id);

        if (empty($user)) {
            throw new \Exception("usuario nao encontrado");
        }

        if (!$user->destroy()) {
            throw new \PDOException($user->fail()->getMessage());
        }
        return true;
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
        $this->id = $id;
    }

    public function findUserById(array $columns = [])
    {
        $columns = empty($columns) ? "*" : implode(", ", $columns);
        $data = $this->user->findById($this->getId(), $columns);
        
        if (empty($data)) {
            return json_encode(["user_not_found" => "usuário não encontrado"]);
        }

        if (!empty($data->getDeleted())) {
            return json_encode(["access_denied" => "acesso negado"]);
        }

        return $data;
    }

    public function dropUserByEmail(string $userEmail)
    {
        $user = $this->user->find("user_email=:user_email",
            ":user_email=" . $userEmail . "")->fetch();

        if (empty($user)) {
            throw new \Exception("usuario nao encontrado");
        }

        try {
            return $user->destroy();
        } catch (\PDOException $_) {
            throw new \PDOException($user->fail()->getMessage());
        }
    }

    public function login(string $userEmail, string $userPassword)
    {
        if (empty($userEmail) || empty($userPassword)) {
            return json_encode(["invalid_login_data" => "dados iválidos"]);
        }

        $user = $this->user->find("user_email=:user_email",
            ":user_email=" . $userEmail . "")->fetch();
        
        if (empty($user)) {
            return json_encode(["user_not_register" => "usuário não registrado"]);
        }

        if (!empty($user->getDeleted())) {
            return json_encode(["access_denied" => "acesso negado"]);
        }
        
        if (!password_verify($userPassword, $user->user_password)) {
            return json_encode(["user_not_auth" => "usuário não autenticado"]);
        }

        return $user;
        
    }

    public function persistData(array $data)
    {
        if (empty($data)) {
            return json_encode(["invalid_persist_data" => "dados inválidos"]);
        }

        validateModelProperties(ModelsUser::class, $data);

        if (!empty($data["user_email"])) {
            $user = $this->user
                ->find("user_email=:user_email", ":user_email=" . $data["user_email"] . "")->fetch();
            
            if (!empty($user)) {
                return json_encode(["error_user_exists" => "este usuário já foi cadastrado"]);
            }
        }

        foreach ($data as $key => $value) {
            $this->user->$key = $value;
        }

        if (!$this->user->save()) {
            if (!empty($this->user->fail())) {
                throw new PDOException($this->user->fail()->getMessage());
            }else {
                throw new PDOException($this->user->message()->getText());
            }
        }

        $this->setId(Connect::getInstance()->lastInsertId());
        return true;
    }
}