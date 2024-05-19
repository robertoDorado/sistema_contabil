<?php

namespace Source\Domain\Tests;

use Exception;
use PHPUnit\Framework\TestCase;
use Ramsey\Uuid\Nonstandard\Uuid;
use Source\Domain\Model\Customer;
use Source\Models\Customer as ModelsCustomer;

/**
 * CustomerTest Domain\Tests
 * @link 
 * @author Roberto Dorado <robertodorado7@gmail.com>
 * @package Source\Domain\Tests
 */
class CustomerTest extends TestCase
{
    /** @var Customer Customer */
    private Customer $customer;

    public function testPersistInvalidData()
    {
        $this->customer = new Customer();
        $response = $this->customer->persistData([]);
        if (empty($response)) {
            $this->assertJsonStringEqualsJsonString(
                json_encode(["error" => "dados inválidos"]),
                $this->customer->message->json()
            );
        }
    }

    public function testPersistDataCustomerAlreadyExists()
    {
        $customers = [];
        for ($i = 0; $i < 2; $i++) {
            $this->customer = new Customer();
            $customerUuid = Uuid::uuid4();

            $requestPost = [
                "uuid" => $customerUuid,
                "customer_name" => "Sarah Luzia Stefany Gomes",
                "customer_document" => "194.626.014-00",
                "birth_date" => "2005-01-25",
                "customer_gender" => "0",
                "customer_email" => "sarah.luzia.gomes@lumavalee.com.br",
                "customer_zipcode" => "52191-261",
                "customer_address" => "Rua Juarina",
                "customer_number" => 624,
                "customer_neighborhood" => "Nova Descoberta",
                "customer_city" => "Recife",
                "customer_state" => "PE",
                "customer_phone" => "(81) 3799-9446",
                "cell_phone" => "(81) 99548-0856",
                "created_at" => date("Y-m-d"),
                "updated_at" => date("Y-m-d"),
                "deleted" => 0,
            ];

            $response = $this->customer->persistData($requestPost);
            if (empty($response)) {
                $this->assertJsonStringEqualsJsonString(
                    json_encode(["error" => "este cliente já foi cadastrado"]),
                    $this->customer->message->json()
                );
                continue;
            }

            array_push($customers, $customerUuid);
        }

        if (!empty($customers)) {
            foreach ($customers as $customerUuid) {
                $this->customer = new Customer();
                $this->customer->setUuid($customerUuid);
                $this->customer->dropCustomerByUuid();
            }
        }
    }

    public function testInvalidUuidPersistData()
    {
        $this->customer = new Customer();
        $requestPost = [
            "uuid" => "-----",
            "customer_name" => "Sarah Luzia Stefany Gomes",
            "customer_document" => "194.626.014-00",
            "birth_date" => "2005-01-25",
            "customer_gender" => "0",
            "customer_email" => "sarah.luzia.gomes@lumavalee.com.br",
            "customer_zipcode" => "52191-261",
            "customer_address" => "Rua Juarina",
            "customer_number" => 624,
            "customer_neighborhood" => "Nova Descoberta",
            "customer_city" => "Recife",
            "customer_state" => "PE",
            "customer_phone" => "(81) 3799-9446",
            "cell_phone" => "(81) 99548-0856",
            "created_at" => date("Y-m-d"),
            "updated_at" => date("Y-m-d"),
            "deleted" => 0,
        ];
        $this->expectException(Exception::class);
        $this->expectExceptionMessage("uuid inválido");
        $this->customer->persistData($requestPost);
    }

    public function testInvalidId()
    {
        $this->customer = new Customer();
        $this->customer->setId(0);
        $this->expectException(Exception::class);
        $this->expectExceptionMessage("id não atribuido");
        $this->customer->getId();
    }

    public function testInvalidUuid()
    {
        $this->customer = new Customer();
        $this->expectException(Exception::class);
        $this->expectExceptionMessage("uuid inválido");
        $this->customer->setUuid("--");
    }

    public function testFindCustomerByUuid()
    {
        $this->customer = new Customer();
        $customerUuid = Uuid::uuid4();

        $requestPost = [
            "uuid" => $customerUuid,
            "customer_name" => "Sarah Luzia Stefany Gomes",
            "customer_document" => "194.626.014-00",
            "birth_date" => "2005-01-25",
            "customer_gender" => "0",
            "customer_email" => "sarah.luzia.gomes@lumavalee.com.br",
            "customer_zipcode" => "52191-261",
            "customer_address" => "Rua Juarina",
            "customer_number" => 624,
            "customer_neighborhood" => "Nova Descoberta",
            "customer_city" => "Recife",
            "customer_state" => "PE",
            "customer_phone" => "(81) 3799-9446",
            "cell_phone" => "(81) 99548-0856",
            "created_at" => date("Y-m-d"),
            "updated_at" => date("Y-m-d"),
            "deleted" => 0,
        ];

        $this->customer->persistData($requestPost);
        $this->customer = new Customer();
        
        $this->customer->setUuid($customerUuid);
        $response = $this->customer->findCustomerByUuid([]);
        
        $this->assertInstanceOf(ModelsCustomer::class, $response);
        $this->customer->dropCustomerByUuid();
    }

    public function testEmptyCustomer()
    {
        $this->customer = new Customer();
        $this->customer->setUuid(Uuid::uuid4());
        $response = $this->customer->findCustomerByUuid([]);
        if (empty($response)) {
            $this->assertJsonStringEqualsJsonString(
                json_encode(["error" => "cliente não encontrado"]),
                $this->customer->message->json()
            );
        }
    }

    public function testDropCustomerNotFound()
    {
        $this->customer = new Customer();
        $this->customer->setUuid(Uuid::uuid4());
        $this->expectException(Exception::class);
        $this->expectExceptionMessage("cliente não encontrado");
        $this->customer->dropCustomerByUuid();
    }

    public function testSetCustomer()
    {
        $this->customer = new Customer();
        $this->customer->name = "roberto";
        $this->assertEquals("roberto", $this->customer->name);
    }

    public function testFindCustomerByEmailIsEmpty()
    {
        $this->customer = new Customer();
        $this->customer->email = "";
        $this->expectException(Exception::class);
        $this->expectExceptionMessage("e-mail do cliente não pode estar vazio");
        $this->customer->findCustomerByEmail();
    }

    public function testFindCustomerByEmailInvalidEmail()
    {
        $this->customer = new Customer();
        $this->customer->email = "teste_123";
        $this->expectException(Exception::class);
        $this->expectExceptionMessage("tipo de e-mail inválido");
        $this->customer->findCustomerByEmail();
    }

    public function testFindCustomerByEmailNotExists()
    {
        $this->customer = new Customer();
        $this->customer->email = "meuemail@naoexiste.com";
        $this->customer->findCustomerByEmail();
        $this->assertJsonStringEqualsJsonString(json_encode(["error" => "cliente não encontrado"]), 
        $this->customer->message->json());
    }
}
