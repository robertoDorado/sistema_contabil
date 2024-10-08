<?php

namespace Source\Domain\Support;

use Exception;
use Ramsey\Uuid\Nonstandard\Uuid;
use ReflectionClass;
use Source\Core\Connect;
use Source\Core\Model;
use Source\Domain\Model\CashFlowGroup;
use Source\Domain\Model\Customer;
use Source\Domain\Model\User;
use Source\Models\Support;
use Source\Models\User as ModelsUser;
use Source\Support\Message;

/**
 * Tools Domain\Support
 * @link 
 * @author Roberto Dorado <robertodorado7@gmail.com>
 * @package Source\Domain\Support
 */
class Tools
{
    /** @var Message Objeto Message */
    public Message $message;

    /** @var Model Objeto Model */
    private Model $model;

    /** @var string Classe de verifiicação */
    private string $class;

    /** @var int último id  */
    public int $lastId;

    /** @var array Validação de dados */
    private array $verifyKeys;

    /**
     * Tools constructor
     */
    public function __construct(Model $model, string $class)
    {
        $this->message = new Message();
        $this->model = $model;
        $this->class = $class;
    }

    public function findUserByEmail(array $data): ?Model
    {
        $formatData = function (string $key, string $prefix) use ($data) {
            $data[$key] = array_map(function ($item, $key) {
                return $key . "=" . $item;
            }, $data[$key], array_keys($data[$key]));

            return implode("{$prefix}", $data[$key]);
        };

        $data["terms"] = $formatData("terms", " AND ");
        $data["params"] = $formatData("params", "&");

        $data["columns"] = empty($data["columns"]) ? "*" : implode(", ", $data["columns"]);
        $data = $this->model->find($data["terms"], $data["params"], $data["columns"])->fetch();

        if (empty($data)) {
            $this->message->error("usuário não existe");
            return null;
        }

        if (!empty($data->getDeleted())) {
            $this->message->error("acesso negado");
            return null;
        }

        return $data;
    }

    public function login(array $data): ?Model
    {
        $formatData = function (string $key, string $prefix) use ($data) {
            $data[$key] = array_map(function ($item, $key) {
                return $key . "=" . $item;
            }, $data[$key], array_keys($data[$key]));

            return implode("{$prefix}", $data[$key]);
        };

        $data["terms_validate_email"] = $formatData("terms_validate_email", " AND ");
        $data["params_validate_email"] = $formatData("params_validate_email", "&");
        $user = $this->model->find($data["terms_validate_email"], $data["params_validate_email"])->fetch();

        if (empty($user)) {
            $verifyModel = [
                ModelsUser::class => new ModelsUser(),
                Support::class => new Support()
            ];

            if (!empty($verifyModel[$this->class])) {
                $this->model = $verifyModel[$this->class];
            }

            $data["terms_validate_name"] = $formatData("terms_validate_name", " AND ");
            $data["params_validate_name"] = $formatData("params_validate_name", "&");
            $user = $this->model->find($data["terms_validate_name"], $data["params_validate_name"])->fetch();
        }

        if (empty($user)) {
            $this->message->error("usuário não registrado");
            return null;
        }

        if (!empty($user->getDeleted())) {
            $this->message->error("acesso negado");
            return null;
        }

        $response = $this->validatePassword($data["password"], $user);
        if (empty($response)) {
            $this->message->error("usuário não autenticado");
            return null;
        }
        return $user;
    }

    private function validatePassword(string $password, Model $userData): bool
    {
        if (!password_verify($password, $userData->user_password)) {
            return false;
        }
        return true;
    }

    private function validateData(array $data, bool $isPersistence = true): void
    {
        $flag = !empty($isPersistence) ? "persistir" : "atualizar";
        $this->verifyKeys = [
            "state_registration" => function ($value) {
                if (!empty($value)) {
                    if (!preg_match("/^\d{3}\.\d{3}\.\d{3}\.\d{3}$/", $value)) {
                        http_response_code(400);
                        echo json_encode(["error" => "inscrição estadual inválido"]);
                        die;
                    }
                }

                return $value;
            },

            "company_document" => function ($value) {
                if (!preg_match("/^\d{2}\.\d{3}\.\d{3}\/\d{4}-\d{2}$/", $value)) {
                    http_response_code(400);
                    echo json_encode(["error" => "cnpj inválido"]);
                    die;
                }
                return $value;
            },

            "uuid" => function ($value) {
                if (!Uuid::isValid($value)) {
                    throw new Exception("uuid inválido");
                }
                return $value;
            },

            "id_cash_flow_group" => function ($value) use ($flag) {
                if (!$value instanceof CashFlowGroup) {
                    throw new Exception("Instância inválida ao {$flag} o dado");
                }
                return $value->getId();
            },

            "id_user" => function ($value) use ($flag) {
                if (!$value instanceof User) {
                    throw new Exception("Instância inválida ao {$flag} o dado");
                }
                return $value->getId();
            },

            "entry" => function (string $value) use ($data) {
                $launchValue = convertCurrencyRealToFloat($value);
                $launchValue = empty($data['entry_type']) ? ($launchValue * -1) : $launchValue;
                return $launchValue;
            },

            "customer_id" => function ($value) {
                if (!$value instanceof Customer) {
                    throw new Exception("instância do cliente está incorreta");
                }
                return $value->getId();
            },

            "subscription_id" => function ($value) {
                if (!preg_match("/^sub_/", $value)) {
                    throw new Exception("subscription_id inválido");
                }
                return $value;
            },

            "charge_id" => function ($value) {
                if (!preg_match("/^ch_/", $value)) {
                    throw new Exception("charge_id inválido");
                }
                return $value;
            },

            "id_customer" => function ($value) {
                if (!$value instanceof Customer) {
                    throw new Exception("instância do cliente está incorreta");
                }
                return $value->getId();
            }
        ];
    }

    public function updateData(string $terms, string $params, array $data, string $errorMessageNotFoundData): bool
    {
        if (empty($data)) {
            $this->message->error("parametro data não pode ser vazio");
            return false;
        }

        $dataResponse = $this->model->find($terms, $params)->fetch();
        if (empty($dataResponse)) {
            $this->message->error($errorMessageNotFoundData);
            return false;
        }

        $this->validateData($data, false);
        foreach ($data as $key => &$value) {
            if (!empty($this->verifyKeys[$key])) {
                $value = $this->verifyKeys[$key]($value);
            }
            $dataResponse->$key = $value;
        }

        try {
            $dataResponse->setRequiredFields(array_keys($data));
            return $dataResponse->save();
        } catch (\PDOException $th) {
            http_response_code(400);
            echo json_encode(["error" => "erro ao tentar atualizar o registro"]);
            $response = file_put_contents(CONF_ERROR_PATH, $th->getMessage() . PHP_EOL, FILE_APPEND);
            if (!$response) {
                throw new Exception("Erro ao tentar gravar o erro no arquivo de log");
            }
            die;
        }
    }

    private function validateModelProperties(string $class, array $data): void
    {
        $reflectionClass = new ReflectionClass($class);

        $properties = $reflectionClass->getProperties();
        $properties = array_filter($properties, function ($property) use ($reflectionClass) {
            if ($property->getDeclaringClass()->getName() == $reflectionClass->getName()) {
                return $property->getName();
            }
        });

        $properties = transformCamelCaseToSnakeCase($properties);

        foreach ($properties as &$value) {
            $value = preg_replace('/^.*\\$([A-Za-z0-9_]+).*/', '$1', trim($value));
        }

        $properties = array_filter($properties, function ($property) {
            if (!empty($property)) {
                return $property;
            }
        });

        if (!empty($properties)) {
            for ($i = 0; $i < count($properties); $i++) {
                $isNullable = $this->model->verifyIfColumnIsNullable($properties[$i]);
                if (!$isNullable) {
                    if (!isset($data[$properties[$i]])) {
                        throw new \Exception("esta propriedade " . $properties[$i] . " foi passado de maneira incorreta");
                    }
                }
            }
        }
    }

    public function persistData(array $data): bool
    {
        if (empty($data)) {
            $this->message->error("dados inválidos");
            return false;
        }

        $this->validateModelProperties($this->class, $data);
        $this->validateData($data);

        foreach ($data as $key => &$value) {
            if (!empty($this->verifyKeys[$key])) {
                $value = $this->verifyKeys[$key]($value);
            }
            $this->model->$key = $value;
        }

        try {
            $this->model->save();
            $this->lastId = Connect::getInstance()->lastInsertId();
            return true;
        } catch (\PDOException $th) {
            http_response_code(400);
            $response = file_put_contents(CONF_ERROR_PATH, $th->getMessage() . PHP_EOL, FILE_APPEND);
            if (!$response) {
                throw new Exception("Erro ao tentar gravar o erro no arquivo de log");
            }
            die;
        }
    }
}
