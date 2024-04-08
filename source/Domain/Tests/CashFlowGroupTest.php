<?php

namespace Source\Domain\Tests;

use Exception;
use PHPUnit\Framework\TestCase;
use Ramsey\Uuid\Nonstandard\Uuid;
use Source\Domain\Model\CashFlowGroup;
use Source\Domain\Model\Customer;
use Source\Domain\Model\User;
use Source\Models\CashFlowGroup as ModelsCashFlowGroup;

/**
 * CashFlowGroupTest Domain\Tests
 * @link 
 * @author Roberto Dorado <robertodorado7@gmail.com>
 * @package Source\Domain\Tests
 */
class CashFlowGroupTest extends TestCase
{
    /** @var CashFlowGroup CashFlowGroup */
    private CashFlowGroup $cashFlowGroup;

    /** @var Customer Customer */
    private Customer $customer;

    /** @var User User */
    private User $user;

    public function testInvalidPersistData()
    {
        $this->cashFlowGroup = new CashFlowGroup();
        $response = $this->cashFlowGroup->persistData([]);
        if (empty($response)) {
            $this->assertJsonStringEqualsJsonString(
                json_encode(["error" => "dados inválidos"]),
                $this->cashFlowGroup->message->json()
            );
        }
    }

    public function testPersistCashFlowGroup()
    {
        $this->customer = new Customer();
        $customerUuid = Uuid::uuid6();
        $this->customer->setUuid($customerUuid);

        $requestPost = [
            "uuid" => $customerUuid,
            "customer_name" => "Sarah Luzia Stefany Gomes",
            "customer_document" => "194.626.014-00",
            "birth_date" => "2005-01-25",
            "customer_gender" => "0",
            "customer_email" => "sarah.luzia.gomes@" . bin2hex(random_bytes(6)) . ".com.br",
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
            "user_email" => $requestPost["customer_email"],
            "user_password" => password_hash("senha123", PASSWORD_DEFAULT),
            "deleted" => 0
        ];

        $this->user->persistData($userData);
        $this->cashFlowGroup = new CashFlowGroup();

        $cashFlowGroupData = [
            "uuid" => Uuid::uuid6(),
            "id_user" => $this->user,
            "group_name" => "Receitas",
            "created_at" => date("Y-m-d"),
            "updated_at" => date("Y-m-d"),
            "deleted" => 0
        ];

        $response = $this->cashFlowGroup->persistData($cashFlowGroupData);
        $this->assertTrue($response);
        $this->customer->dropCustomerByUuid();
    }

    public function testPersistDataInvalidUserInstance()
    {
        $this->cashFlowGroup = new CashFlowGroup();
        $cashFlowGroupData = [
            "uuid" => Uuid::uuid6(),
            "id_user" => new Customer(),
            "group_name" => "Receitas",
            "created_at" => date("Y-m-d"),
            "updated_at" => date("Y-m-d"),
            "deleted" => 0
        ];
        $this->expectException(Exception::class);
        $this->expectExceptionMessage("Instância inválida ao persistir o dado");
        $this->cashFlowGroup->persistData($cashFlowGroupData);
    }

    public function testGetId()
    {
        $this->cashFlowGroup = new CashFlowGroup();
        $this->cashFlowGroup->setId(100);
        $response = $this->cashFlowGroup->getId();
        $this->assertIsInt($response);
        $this->assertEquals(100, $response);
    }

    public function testGetEmptyId()
    {
        $this->cashFlowGroup = new CashFlowGroup();
        $this->cashFlowGroup->setId(0);
        $this->expectException(Exception::class);
        $this->expectExceptionMessage("Id não atribuido");
        $this->cashFlowGroup->getId();
    }

    public function testUpdateCashFlowGroupByUuid()
    {
        $this->customer = new Customer();
        $customerUuid = Uuid::uuid6();
        $this->customer->setUuid($customerUuid);

        $requestPost = [
            "uuid" => $customerUuid,
            "customer_name" => "Sarah Luzia Stefany Gomes",
            "customer_document" => "194.626.014-00",
            "birth_date" => "2005-01-25",
            "customer_gender" => "0",
            "customer_email" => "sarah.luzia.gomes@" . bin2hex(random_bytes(6)) . ".com.br",
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
            "user_email" => $requestPost["customer_email"],
            "user_password" => password_hash("senha123", PASSWORD_DEFAULT),
            "deleted" => 0
        ];

        $this->user->persistData($userData);
        $this->cashFlowGroup = new CashFlowGroup();
        $cashFlowGroupUuid = Uuid::uuid6();

        $cashFlowGroupData = [
            "uuid" => $cashFlowGroupUuid,
            "id_user" => $this->user,
            "group_name" => "Receitas",
            "created_at" => date("Y-m-d"),
            "updated_at" => date("Y-m-d"),
            "deleted" => 0
        ];

        $this->cashFlowGroup->persistData($cashFlowGroupData);
        $cashFlowGroupData = [
            "uuid" => $cashFlowGroupUuid,
            "id_user" => $this->user,
            "group_name" => "Receitas de vendas"
        ];

        $this->cashFlowGroup = new CashFlowGroup();
        $response = $this->cashFlowGroup->updateCashFlowGroupByUuid($cashFlowGroupData);
        $this->assertTrue($response);

        $this->cashFlowGroup = new CashFlowGroup();
        $this->cashFlowGroup->setUuid($cashFlowGroupUuid);

        $cashFlowGroupData = $this->cashFlowGroup->findCashFlowGroupByUuid();
        $this->assertEquals("Receitas de vendas", $cashFlowGroupData->group_name);
        $this->customer->dropCustomerByUuid();
    }

    public function testUpdateCashFlowGroupByUuidParamIsEmpty()
    {
        $this->cashFlowGroup = new CashFlowGroup();
        $response = $this->cashFlowGroup->updateCashFlowGroupByUuid([]);
        if (empty($response)) {
            $this->assertJsonStringEqualsJsonString(
                json_encode(["error" => "parametro data não pode ser vazio"]),
                $this->cashFlowGroup->message->json()
            );
        }
    }

    public function testUpdateCashFlowGroupByUuidNotFound()
    {
        $this->cashFlowGroup = new CashFlowGroup();
        $response = $this->cashFlowGroup->updateCashFlowGroupByUuid([
            "uuid" => Uuid::uuid6(),
            "group_name" => "teste"
        ]);
        if (empty($response)) {
            $this->assertJsonStringEqualsJsonString(
                json_encode(["error" => "grupo fluxo de caixa não encontrado"]),
                $this->cashFlowGroup->message->json()
            );
        }
    }

    public function testUpdateCashFlowGroupByUuidInvalidInstance()
    {
        $this->customer = new Customer();
        $customerUuid = "1eef5b42-975f-6e18-b69f-0242ac120003";
        $this->customer->setUuid($customerUuid);

        $requestPost = [
            "uuid" => $customerUuid,
            "customer_name" => "Sarah Luzia Stefany Gomes",
            "customer_document" => "194.626.014-00",
            "birth_date" => "2005-01-25",
            "customer_gender" => "0",
            "customer_email" => "sarah.luzia.gomes@" . bin2hex(random_bytes(6)) . ".com.br",
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
            "user_email" => $requestPost["customer_email"],
            "user_password" => password_hash("senha123", PASSWORD_DEFAULT),
            "deleted" => 0
        ];

        $this->user->persistData($userData);
        $this->cashFlowGroup = new CashFlowGroup();
        $cashFlowGroupUuid = Uuid::uuid6();

        $cashFlowGroupData = [
            "uuid" => $cashFlowGroupUuid,
            "id_user" => $this->user,
            "group_name" => "Receitas",
            "created_at" => date("Y-m-d"),
            "updated_at" => date("Y-m-d"),
            "deleted" => 0
        ];

        $this->cashFlowGroup->persistData($cashFlowGroupData);
        $cashFlowGroupData = [
            "uuid" => $cashFlowGroupUuid,
            "id_user" => $this->customer
        ];
        $this->expectException(Exception::class);
        $this->expectExceptionMessage("Instância inválida ao atualizar o dado");
        $this->cashFlowGroup->updateCashFlowGroupByUuid($cashFlowGroupData);
    }

    public function testCleanDataBase()
    {
        $this->customer = new Customer();
        $this->customer->setUuid("1eef5b42-975f-6e18-b69f-0242ac120003");
        $response = $this->customer->dropCustomerByUuid();
        $this->assertTrue($response);
    }

    public function testGetUuid()
    {
        $this->cashFlowGroup = new CashFlowGroup();
        $this->cashFlowGroup->setUuid("1eef5b42-975f-6e18-b69f-0242ac120003");
        $response = $this->cashFlowGroup->getUuid();
        $this->assertEquals("1eef5b42-975f-6e18-b69f-0242ac120003", $response);
        $this->assertIsString($response);
    }

    public function testGetInvalidUuid()
    {
        $this->cashFlowGroup = new CashFlowGroup();
        $this->expectException(Exception::class);
        $this->expectExceptionMessage("uuid inválido");
        $this->cashFlowGroup->setUuid("----------");
    }

    public function testDropCashFlowGroupByUuid()
    {
        $this->customer = new Customer();
        $customerUuid = Uuid::uuid6();
        $this->customer->setUuid($customerUuid);

        $requestPost = [
            "uuid" => $customerUuid,
            "customer_name" => "Sarah Luzia Stefany Gomes",
            "customer_document" => "194.626.014-00",
            "birth_date" => "2005-01-25",
            "customer_gender" => "0",
            "customer_email" => "sarah.luzia.gomes@" . bin2hex(random_bytes(6)) . ".com.br",
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
            "user_email" => $requestPost["customer_email"],
            "user_password" => password_hash("senha123", PASSWORD_DEFAULT),
            "deleted" => 0
        ];

        $this->user->persistData($userData);
        $this->cashFlowGroup = new CashFlowGroup();
        $cashFlowGroupUuid = Uuid::uuid6();

        $cashFlowGroupData = [
            "uuid" => $cashFlowGroupUuid,
            "id_user" => $this->user,
            "group_name" => "Receitas",
            "created_at" => date("Y-m-d"),
            "updated_at" => date("Y-m-d"),
            "deleted" => 0
        ];

        $this->cashFlowGroup->persistData($cashFlowGroupData);
        $this->cashFlowGroup = new CashFlowGroup();
        
        $this->cashFlowGroup->setUuid($cashFlowGroupUuid);
        $response = $this->cashFlowGroup->dropCashFlowGroupByUuid();
        $this->assertTrue($response);
        $this->customer->dropCustomerByUuid();
    }

    public function testDropCashFlowGroupByUuidIsEmpty()
    {
        $this->cashFlowGroup = new CashFlowGroup();
        $this->cashFlowGroup->setUuid("1eef5b42-975f-6e18-b69f-0242ac120003");
        $response = $this->cashFlowGroup->dropCashFlowGroupByUuid();
        if (empty($response)) {
            $this->assertJsonStringEqualsJsonString(
                json_encode(["error" => "o registro não existe"]),
                $this->cashFlowGroup->message->json()
            );
        }
    }

    public function testFindCashFlowGroupNotFound()
    {
        $this->cashFlowGroup = new CashFlowGroup();
        $this->cashFlowGroup->setUuid("1eef5b42-975f-6e18-b69f-0242ac120003");
        $response = $this->cashFlowGroup->findCashFlowGroupByUuid();
        if (empty($response)) {
            $this->assertJsonStringEqualsJsonString(
                json_encode(["error" => "registro não encontrado"]),
                $this->cashFlowGroup->message->json()
            );
        }
    }

    public function testFindCashFlowGroupByUser()
    {
        $this->customer = new Customer();
        $customerUuid = Uuid::uuid6();
        $this->customer->setUuid($customerUuid);

        $requestPost = [
            "uuid" => $customerUuid,
            "customer_name" => "Sarah Luzia Stefany Gomes",
            "customer_document" => "194.626.014-00",
            "birth_date" => "2005-01-25",
            "customer_gender" => "0",
            "customer_email" => "sarah.luzia.gomes@" . bin2hex(random_bytes(6)) . ".com.br",
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
            "user_email" => $requestPost["customer_email"],
            "user_password" => password_hash("senha123", PASSWORD_DEFAULT),
            "deleted" => 0
        ];
        $this->user->persistData($userData);

        for ($i = 0; $i < 2; $i++) {
            $this->cashFlowGroup = new CashFlowGroup();
            $cashFlowGroupUuid = Uuid::uuid6();
    
            $cashFlowGroupData = [
                "uuid" => $cashFlowGroupUuid,
                "id_user" => $this->user,
                "group_name" => "Receitas",
                "created_at" => date("Y-m-d"),
                "updated_at" => date("Y-m-d"),
                "deleted" => 0
            ];
    
            $this->cashFlowGroup->persistData($cashFlowGroupData);
        }
        
        $this->cashFlowGroup = new CashFlowGroup();
        $response = $this->cashFlowGroup->findCashFlowGroupByUser([], $this->user);
        
        $this->assertIsArray($response);
        $this->assertEquals(2, count($response));
        
        if (!empty($response)) {
            foreach ($response as $object) {
                $this->assertInstanceOf(ModelsCashFlowGroup::class, $object);
            }
        }
        
        $this->customer->dropCustomerByUuid();
    }

    public function testFindCashFlowGroupByUserIsEmpty()
    {
        $this->user = new User();
        $this->user->setId(125469325);
        $this->cashFlowGroup = new CashFlowGroup();
        $response = $this->cashFlowGroup->findCashFlowGroupByUser([], $this->user);
        if (empty($response)) {
            $this->assertJsonStringEqualsJsonString(
                json_encode(["error" => "nenhum registro foi encontrado"]),
                $this->cashFlowGroup->message->json()
            );
        }
    }

    public function testFindCashFlowGroupByName()
    {
        $this->customer = new Customer();
        $customerUuid = Uuid::uuid6();
        $this->customer->setUuid($customerUuid);

        $requestPost = [
            "uuid" => $customerUuid,
            "customer_name" => "Sarah Luzia Stefany Gomes",
            "customer_document" => "194.626.014-00",
            "birth_date" => "2005-01-25",
            "customer_gender" => "0",
            "customer_email" => "sarah.luzia.gomes@" . bin2hex(random_bytes(6)) . ".com.br",
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
            "user_email" => $requestPost["customer_email"],
            "user_password" => password_hash("senha123", PASSWORD_DEFAULT),
            "deleted" => 0
        ];

        $this->user->persistData($userData);
        $this->cashFlowGroup = new CashFlowGroup();
        $cashFlowGroupUuid = Uuid::uuid6();

        $cashFlowGroupData = [
            "uuid" => $cashFlowGroupUuid,
            "id_user" => $this->user,
            "group_name" => "Receitas",
            "created_at" => date("Y-m-d"),
            "updated_at" => date("Y-m-d"),
            "deleted" => 0
        ];

        $this->cashFlowGroup->persistData($cashFlowGroupData);
        $this->cashFlowGroup = new CashFlowGroup();
        
        $response = $this->cashFlowGroup->findCashFlowGroupByName("Receitas", $this->user);
        $this->assertInstanceOf(ModelsCashFlowGroup::class, $response);
        $this->customer->dropCustomerByUuid();
    }

    public function testFindCashFlowGroupByNameIsEmpty()
    {
        $this->user = new User();
        $this->user->setId(5263698565485);
        $this->cashFlowGroup = new CashFlowGroup();
        $response = $this->cashFlowGroup->findCashFlowGroupByName("Receitas", $this->user);
        if (empty($response)) {
            $this->assertJsonStringEqualsJsonString(
                json_encode(["error" => "nenhum registro foi encontrado"]),
                $this->cashFlowGroup->message->json()
            );
        }
    }

    public function testFindCashFlowGroupDeletedTrue()
    {
        $this->customer = new Customer();
        $customerUuid = Uuid::uuid6();
        $this->customer->setUuid($customerUuid);

        $requestPost = [
            "uuid" => $customerUuid,
            "customer_name" => "Sarah Luzia Stefany Gomes",
            "customer_document" => "194.626.014-00",
            "birth_date" => "2005-01-25",
            "customer_gender" => "0",
            "customer_email" => "sarah.luzia.gomes@" . bin2hex(random_bytes(6)) . ".com.br",
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
            "user_email" => $requestPost["customer_email"],
            "user_password" => password_hash("senha123", PASSWORD_DEFAULT),
            "deleted" => 0
        ];

        $this->user->persistData($userData);
        for ($i = 0; $i < 2; $i++) {
            $this->cashFlowGroup = new CashFlowGroup();
            $cashFlowGroupUuid = Uuid::uuid6();
    
            $cashFlowGroupData = [
                "uuid" => $cashFlowGroupUuid,
                "id_user" => $this->user,
                "group_name" => "Receitas",
                "created_at" => date("Y-m-d"),
                "updated_at" => date("Y-m-d"),
                "deleted" => 1
            ];
    
            $this->cashFlowGroup->persistData($cashFlowGroupData);
        }

        $response = $this->cashFlowGroup->findCashFlowGroupDeletedTrue([], $this->user);
        $this->assertIsArray($response);
        $this->assertEquals(2, count($response));

        if (!empty($respone)) {
            foreach ($response as $object) {
                $this->assertInstanceOf(ModelsCashFlowGroup::class, $object);
            }
        }

        $this->customer->dropCustomerByUuid();
    }

    public function testFindCashFlowGroupDeletedTrueIsEmpty()
    {
        $this->user = new User();
        $this->user->setId(52596357);
        $this->cashFlowGroup = new CashFlowGroup();
        $response = $this->cashFlowGroup->findCashFlowGroupDeletedTrue([], $this->user);
        if (empty($response)) {
            $this->assertJsonStringEqualsJsonString(
                json_encode(["error" => "não há registros deletados"]),
                $this->cashFlowGroup->message->json()
            );
        }
    }

    public function testSetter()
    {
        $this->cashFlowGroup = new CashFlowGroup();
        $this->cashFlowGroup->name = "roberto";
        $this->assertEquals("roberto", $this->cashFlowGroup->name);
    }
}
