<?php
namespace Source\Domain\Model;

use Exception;
use Ramsey\Uuid\Nonstandard\Uuid;
use Source\Core\Connect;
use Source\Models\Customer as ModelsCustomer;
use Source\Support\Message;

/**
 * Customer Model
 * @link 
 * @author Roberto Dorado <robertodorado7@gmail.com>
 * @package Domain\Model
 */
class Customer
{
    /** @var ModelsCustomer Model Cliente */
    private ModelsCustomer $customer;

    /** @var int Id do cliente */
    private int $id;

    /** @var string Uuid do cliente */
    private string $uuid;

    /** @var object|null */
    public object $data;

    /**
     * Customer constructor
     */
    public function __construct()
    {
        $this->customer = new ModelsCustomer();
        $this->data = new \stdClass();
    }

    public function __get($name)
    {
        return $this->data->$name ?? null;
    }

    public function __set($name, $value)
    {
        $this->data->$name = $value;
    }

    public function dropCustomerByUuid(): bool
    {
        $customerData = $this->customer
        ->find("uuid=:uuid", ":uuid=" . $this->getUuid() . "")
        ->fetch();

        if (empty($customerData)) {
            throw new Exception("cliente não encontrado");
        }

        return $customerData->destroy();
    }

    public function findCustomerByUuid(array $columns): ?ModelsCustomer
    {
        $columns = empty($columns) ? "*" : implode(", ", $columns);
        $customerData = $this->customer
        ->find("uuid=:uuid", ":uuid=" . $this->getUuid() . "")->fetch();
        
        $message = new Message();
        if (empty($customerData)) {
            $message->error("cliente não encontrado");
            $this->data->message = $message;
            return null;
        }

        return $customerData;
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
            throw new \Exception("id não atribuido");
        }
        return $this->id;
    }

    public function setId(int $id): void
    {
        $this->id = $id;
    }

    public function persistData(array $data): bool
    {
        $message = new Message();
        if (empty($data)) {
            $message->error("dados inválidos");
            $this->data->message = $message;
            return false;
        }

        validateModelProperties(ModelsCustomer::class, $data);
        if (!empty($data["customer_email"])) {
            $customer = $this->customer
            ->find("customer_email=:customer_email", 
            ":customer_email=" . $data["customer_email"] . "")
            ->fetch();
            
            if (!empty($customer)) {
                $message->error("este cliente já foi cadastrado");
                $this->data->message = $message;
                return false;
            }
        }

        $verifyKeys = [
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
            $this->customer->$key = $value;
        }

        $this->customer->save();
        $this->setId(Connect::getInstance()->lastInsertId());
        return true;
    }
}
