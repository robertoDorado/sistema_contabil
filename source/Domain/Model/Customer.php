<?php
namespace Source\Domain\Model;

use Exception;
use Ramsey\Uuid\Nonstandard\Uuid;
use Source\Domain\Support\Tools;
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

    public function findCustomerById(): ?ModelsCustomer
    {
        $id = empty($this->data->customer_id) ? 0 : $this->data->customer_id;
        $customerData = $this->customer->findById($id);

        $message = new Message();
        if (empty($customerData)) {
            $message->error("cliente não encontrado");
            $this->data->message = $message;
            return null;    
        }

        if (!empty($customerData->getDeleted())) {
            $message->error("este cliente foi deletado");
            $this->data->message = $message;
            return null; 
        }

        return $customerData;
    }

    public function updateCustomerById(array $data): bool
    {
        $tools = new Tools($this->customer, ModelsCustomer::class);
        $response = $tools->updateData("id=:id", ":id={$data['id']}", $data, "cliente não encontrado");
        $this->data->message = !empty($tools->message) ? $tools->message : "";
        return !empty($response) ? true : false;
    }

    public function updateCustomerByEmail(array $data): bool
    {
        $tools = new Tools($this->customer, ModelsCustomer::class);
        $response = $tools->updateData("customer_email=:customer_email",
        ":customer_email={$data["customer_email"]}", $data, "cliente não encontrado");
        $this->data->message = !empty($tools->message) ? $tools->message : "";
        return !empty($response) ? true : false;
    }

    public function findCustomerByEmail(): ?ModelsCustomer
    {
        if (empty($this->data->email)) {
            throw new Exception("e-mail do cliente não pode estar vazio");
        }

        if (!filter_var($this->data->email, FILTER_VALIDATE_EMAIL)) {
            throw new Exception("tipo de e-mail inválido");
        }

        $customerData = $this->customer
        ->find("customer_email=:customer_email", ":customer_email={$this->data->email}")->fetch();

        if (empty($customerData)) {
            $message = new Message();
            $message->error("cliente não encontrado");
            $this->data->message = $message;
            return null;
        }

        return $customerData;
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
        if (!empty($data["customer_email"])) {
            $customer = $this->customer
            ->find("customer_email=:customer_email", 
            ":customer_email=" . $data["customer_email"] . "")
            ->fetch();
            
            if (!empty($customer)) {
                $message = new Message();
                $message->error("este cliente já foi cadastrado");
                $this->data->message = $message;
                return false;
            }
        }

        $tools = new Tools($this->customer, ModelsCustomer::class);
        $response = $tools->persistData($data);
        $this->data->message = !empty($tools->message) ? $tools->message : "";

        !empty($response) ? $this->setId($tools->lastId) : null;
        return !empty($response) ? true : false;
    }
}
