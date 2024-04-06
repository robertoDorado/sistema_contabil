<?php

namespace Source\Domain\Tests;

use Exception;
use PHPUnit\Framework\TestCase;
use Ramsey\Uuid\Nonstandard\Uuid;
use Source\Domain\Model\Customer;
use Source\Domain\Model\User;

/**
 * UserTest Domain\Tests
 * @link 
 * @author Roberto Dorado <robertodorado7@gmail.com>
 * @package Source\Domain\Tests
 */
class UserTest extends TestCase
{
    /** @var User Usuário */
    private User $user;

    /** @var Customer Customer */
    private Customer $customer;

    public function testInvalidPersistData()
    {
        $this->user = new User();
        $response = $this->user->persistData([]);
        $this->assertJsonStringEqualsJsonString(
            json_encode(["invalid_persist_data" => "dados inválidos"]),
            $response
        );
    }

    public function testPersistData()
    {
        $this->customer = new Customer();
        $customerUuid = Uuid::uuid6();
        $this->customer->setUuid($customerUuid);

        $requestPost = [
            "uuid" => $customerUuid,
            "customer_name" => "Sara Luzia Stefany Gomes",
            "customer_document" => "193.626.014-00",
            "birth_date" => "2005-01-26",
            "customer_gender" => "0",
            "customer_email" => "sara.luzia.gomes@lumavale.com.br",
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
        $this->user = new User();
        $userData = [
            "id_customer" => $this->customer,
            "uuid" => Uuid::uuid6(),
            "user_full_name" => $requestPost["customer_name"],
            "user_nick_name" => "saraLuiza",
            "user_email" => $requestPost["customer_email"],
            "user_password" => password_hash("senha123", PASSWORD_DEFAULT),
            "deleted" => 0
        ];

        $this->assertTrue($this->user->persistData($userData));
        $this->customer->dropCustomerByUuid();
    }

    public function testPersistDataInvalidId()
    {
        $this->user = new User();
        $userData = [
            "id_customer" => $this->user,
            "uuid" => Uuid::uuid6(),
            "user_full_name" => "Sara Luzia Stefany Gomes",
            "user_nick_name" => "saraLuiza",
            "user_email" => "sara.luzia.gomes@lumavale.com.br",
            "user_password" => password_hash("senha123", PASSWORD_DEFAULT),
            "deleted" => 0
        ];

        $this->expectException(Exception::class);
        $this->expectExceptionMessage("instância do cliente está incorreta");
        $this->assertTrue($this->user->persistData($userData));
    }

    public function testUserAlreadyExists()
    {
        $customers = [];
        for ($i = 0; $i < 2; $i++) {
            $this->customer = new Customer();
            $customerUuid = Uuid::uuid6();

            $requestPost = [
                "uuid" => $customerUuid,
                "customer_name" => "Sara Luzia Stefany Gomes",
                "customer_document" => "193.626.014-00",
                "birth_date" => "2005-01-26",
                "customer_gender" => "0",
                "customer_email" => "sara.luzia.gomes@". bin2hex(random_bytes(6)) .".com.br",
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
            $this->user = new User();
            $userData = [
                "id_customer" => $this->customer,
                "uuid" => Uuid::uuid6(),
                "user_full_name" => "Sara Luzia Stefany Gomes",
                "user_nick_name" => "saraLuiza",
                "user_email" => "sara.luzia.gomes@lumavale.com.br",
                "user_password" => password_hash("senha123", PASSWORD_DEFAULT),
                "deleted" => 0
            ];

            $response = $this->user->persistData($userData);
            if (is_string($response) && json_decode($response) != null) {
                $this->assertJsonStringEqualsJsonString(
                    json_encode(["error_user_exists" => "este usuário já foi cadastrado"]),
                    $response
                );
            }
            array_push($customers, $customerUuid);
        }

        if (!empty($customers)) {
            foreach($customers as $customerUuid) {
                $this->customer = new Customer();
                $this->customer->setUuid($customerUuid);
                $this->customer->dropCustomerByUuid();
            }
        }
        
    }
}
