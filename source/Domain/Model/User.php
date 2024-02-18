<?php
namespace Source\Domain\Model;

use ReflectionClass;
use Source\Models\User as ModelsUser;

require dirname(dirname(dirname(__DIR__))) . "/vendor/autoload.php";

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

    public function persistData(array $data)
    {
        if (empty($data)) {
            echo json_encode(["dados inválidos"]);
            die;
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

        foreach ($data as $key => $value) {
            $this->user->$key = $value;
        }

        return $this->user->save();
    }

    public function register(string $fullName, string $nickName, string $email, string $password)
    {
        $user = $this->user->find("user_email=:user_email", ":user_email={$email}")->fetch();
        if (!empty($user)) {
            echo json_encode(["error_user_exists" => "este usuário já foi cadastrado"]);
            die;
        }
    }
}