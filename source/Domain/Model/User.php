<?php
namespace Source\Domain\Model;

use ReflectionClass;
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

    /**
     * User constructor
     */
    public function __construct()
    {
        $this->user = new ModelsUser();
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
            throw new \PDOException($user->fail());
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

        $reflectionClass = new ReflectionClass(ModelsUser::class);
        
        $properties = $reflectionClass->getProperties();
        $properties = array_filter($properties, function($property) use ($reflectionClass) {
            if ($property->getDeclaringClass()->getName() == $reflectionClass->getName()) {
                return $property->getName();
            }
        });
        $properties = transformCamelCaseToSnakeCase($properties);

        foreach ($properties as &$value) {
            $value = preg_replace('/^.*\\$([A-Za-z0-9_]+).*/', '$1', trim($value));
        }

        $properties = array_filter($properties, function($property) {
            if (!empty($property)) {
                return $property;
            }
        });
        
        if (!empty($properties)) {
            for ($i=0; $i < count($properties); $i++) { 
                if (empty($data[$properties[$i]])) {
                    throw new \Exception("esta propriedade " . $properties[$i] . " foi passado de maneira incorreta");
                }
            }
        }

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

        return $this->user->save();
    }
}