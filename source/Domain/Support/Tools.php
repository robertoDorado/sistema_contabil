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

    private function validateData(array $data, bool $isPersistence = true): void
    {
        $flag = !empty($isPersistence) ? "persistir" : "atualizar";
        $this->verifyKeys = [
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

        $dataResponse->setRequiredFields(array_keys($data));
        return $dataResponse->save();
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
                if (!isset($data[$properties[$i]])) {
                    throw new \Exception("esta propriedade " . $properties[$i] . " foi passado de maneira incorreta");
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

        $this->model->save();
        $this->lastId = Connect::getInstance()->lastInsertId();
        return true;
    }
}
