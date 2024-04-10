<?php

namespace Source\Domain\Tests;

use Exception;
use PHPUnit\Framework\TestCase;
use Ramsey\Uuid\Nonstandard\Uuid;
use Source\Domain\Model\Customer;
use Source\Domain\Model\User;
use Source\Models\User as ModelsUser;
use Source\Support\Message;

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

        if (empty($response)) {
            $this->assertJsonStringEqualsJsonString(
                json_encode(["error" => "dados inválidos"]),
                $this->user->message->json()
            );
        }
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
                "customer_email" => "sara.luzia.gomes@" . bin2hex(random_bytes(6)) . ".com.br",
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
            if (empty($response)) {
                $this->assertJsonStringEqualsJsonString(
                    json_encode(["error" => "este usuário já foi cadastrado"]),
                    $this->user->message->json()
                );
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
        $this->customer->setId(1000000000000);
        $this->customer->setUuid(Uuid::uuid6());
        $this->user = new User();

        $userData = [
            "id_customer" => $this->customer,
            "uuid" => "------",
            "user_full_name" => "Sara Luzia Stefany Gomes",
            "user_nick_name" => "saraLuiza",
            "user_email" => "sara.luzia.gomes@lumavale.com.br",
            "user_password" => password_hash("senha123", PASSWORD_DEFAULT),
            "deleted" => 0
        ];

        $this->expectException(Exception::class);
        $this->expectExceptionMessage("uuid inválido");
        $this->user->persistData($userData);
        $this->customer->dropCustomerByUuid();
    }

    public function testGetNickName()
    {
        $this->user = new User();
        $this->user->setNickName("nickName");
        $response = $this->user->getNickName();
        $this->assertIsString($response);
    }

    public function testLoginSetEmail()
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

        $this->user->persistData($userData);
        $this->user->setEmail($userData["user_email"]);
        
        $response = $this->user->login("senha123");
        $this->assertInstanceOf(ModelsUser::class, $response);
        $this->customer->dropCustomerByUuid();
    }

    public function testLoginSetNickName()
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

        $this->user->persistData($userData);
        $this->user->setNickName($userData["user_nick_name"]);
        
        $response = $this->user->login("senha123");
        $this->assertInstanceOf(ModelsUser::class, $response);
        $this->customer->dropCustomerByUuid();
    }

    public function testLoginEmpty()
    {
        $this->user = new User();
        $this->user->setNickName("teste");
        $response = $this->user->login("senha123");
        if (empty($response)) {
            $this->assertJsonStringEqualsJsonString(
                json_encode(["error" => "usuário não registrado"]),
                $this->user->message->json()
            );
        }
    }

    public function testLoginUserDeleted()
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
            "deleted" => 1
        ];

        $this->user->persistData($userData);
        $this->user->setNickName($userData["user_nick_name"]);

        $response = $this->user->login("senha123");
        if (empty($response)) {
            $this->assertJsonStringEqualsJsonString(
                json_encode(["error" => "acesso negado"]),
                $this->user->message->json()
            );
        }

        $this->customer->dropCustomerByUuid();
    }

    public function testLoginUserNotAuth()
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

        $this->user->persistData($userData);
        $this->user->setNickName($userData["user_nick_name"]);

        $response = $this->user->login("senhaErrada");
        if (empty($response)) {
            $this->assertJsonStringEqualsJsonString(
                json_encode(["error" => "usuário não autenticado"]),
                $this->user->message->json()
            );
        }

        $this->customer->dropCustomerByUuid();
    }

    public function testInvalidId()
    {
        $this->user = new User();
        $this->user->setId(0);
        $this->expectException(Exception::class);
        $this->expectExceptionMessage("id não atribuido");
        $this->user->getId();
    }

    public function testGetId()
    {
        $this->user = new User();
        $this->user->setId(1);
        $response = $this->user->getId();
        $this->assertIsInt($response);
    }

    public function testInvalidEmail()
    {
        $this->user = new User();
        $this->expectException(Exception::class);
        $this->expectExceptionMessage("E-mail inválido");
        $this->user->setEmail("emailemail.com.br");
    }

    public function testFindUserByEmailIsEmpty()
    {
        $this->user = new User();
        $this->user->setEmail("email@email.com.br");
        $response = $this->user->findUserByEmail();
        if (empty($response)) {
            $this->assertJsonStringEqualsJsonString(
                json_encode(["error" => "usuário não existe"]),
                $this->user->message->json()
            );
        }
    }

    public function testFindUserByEmailAccessDenied()
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
            "deleted" => 1
        ];

        $this->user->persistData($userData);
        $this->user->setEmail($userData["user_email"]);
        
        $response = $this->user->findUserByEmail([]);
        if (empty($response)) {
            $this->assertJsonStringEqualsJsonString(
                json_encode(["error" => "acesso negado"]),
                $this->user->message->json()
            );
        }
        $this->customer->dropCustomerByUuid();
    }

    public function testFindUserByEmail()
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

        $this->user->persistData($userData);
        $this->user->setEmail($userData["user_email"]);
        $response = $this->user->findUserByEmail([]);
        $this->assertInstanceOf(ModelsUser::class, $response);
        $this->customer->dropCustomerByUuid();
    }

    public function testGetUuid()
    {
        $this->user = new User();
        $this->user->setUuid(Uuid::uuid6());
        $uuid = $this->user->getUuid();
        $this->assertIsString($uuid);
    }

    public function testGetUuidEmpty()
    {
        $this->user = new User();
        $this->expectException(Exception::class);
        $this->expectExceptionMessage("uuid do usuário é inválido");
        $this->user->setUuid("");
    }

    public function testSetter()
    {
        $this->user = new User();
        $this->user->name = "roberto";
        $this->assertEquals("roberto", $this->user->name);
    }
}
