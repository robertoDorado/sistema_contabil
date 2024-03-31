<?php
namespace Source\Domain\Model;

use Exception;
use Ramsey\Uuid\Nonstandard\Uuid;
use Source\Core\Connect;
use Source\Models\Customer as ModelsCustomer;

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

    /**
     * Customer constructor
     */
    public function __construct()
    {
        $this->customer = new ModelsCustomer();
    }

    public function dropCustomerByUuid(Customer $customer)
    {
        $customerData = $this->customer
        ->find("uuid=:uuid", ":uuid=" . $customer->getUuid() . "")
        ->fetch();

        if (empty($customerData)) {
            throw new Exception("cliente não encontrado");
        }

        return $customerData->destroy();
    }

    public function findCustomerByUuid(array $columns, Customer $customer)
    {
        $columns = empty($columns) ? "*" : implode(", ", $columns);
        $customerData = $this->customer
        ->find("uuid=:uuid", ":uuid=" . $customer->getUuid() . "")->fetch();
        
        if (empty($customerData)) {
            return json_encode(["error" => "cliente não encontrado"]);
        }

        return $customerData;
    }

    public function getUuid()
    {
        if (empty($this->uuid)) {
            throw new Exception("uuid não pode estar vazio");
        }
        return $this->uuid;
    }

    public function setUuid(string $uuid)
    {
        if (!Uuid::isValid($uuid)) {
            throw new Exception("uuid inválido");
        }
        $this->uuid = $uuid;
    }

    public function dropCustomerById(Customer $customer)
    {
        $customer = $this->customer->findById($customer->getId());
        if (empty($customer)) {
            throw new Exception("cliente nao encontrado");
        }
        return $customer->destroy();
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

    public function persistData(array $data)
    {
        if (empty($data)) {
            return json_encode(["error" => "dados inválidos"]);
        }

        validateModelProperties(ModelsCustomer::class, $data);
        if (!empty($data["customer_email"])) {
            $customer = $this->customer
            ->find("customer_email=:customer_email", 
            ":customer_email=" . $data["customer_email"] . "")
            ->fetch();
            
            if (!empty($customer)) {
                return json_encode(["error" => "este cliente já foi cadastrado"]);
            }
        }

        foreach ($data as $key => $value) {
            $this->customer->$key = $value;
        }

        $this->customer->save();
        $this->setId(Connect::getInstance()->lastInsertId());
        return true;
    }
}
