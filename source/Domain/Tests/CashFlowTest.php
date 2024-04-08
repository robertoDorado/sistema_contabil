<?php

namespace Source\Domain\Tests;

use Exception;
use PHPUnit\Framework\TestCase;
use Ramsey\Uuid\Nonstandard\Uuid;
use Source\Domain\Model\CashFlow;
use Source\Domain\Model\CashFlowGroup;
use Source\Domain\Model\Customer;
use Source\Domain\Model\User;
use Source\Models\CashFlow as ModelsCashFlow;

/**
 * CashFlowTest Domain\Tests
 * @link 
 * @author Roberto Dorado <robertodorado7@gmail.com>
 * @package Source\Domain\Tests
 */
class CashFlowTest extends TestCase
{
    /** @var CashFlow Fluxo de caixa */
    private CashFlow $cashFlow;

    /** @var User */
    private User $user;

    /** @var CashFlowGroup */
    private CashFlowGroup $cashFlowGroup;

    /** @var Customer */
    private Customer $customer;

    public function testInvalidPersistCashFlow()
    {
        $this->cashFlow = new CashFlow();
        $response = $this->cashFlow->persistData([]);
        if (empty($response)) {
            $this->assertJsonStringEqualsJsonString(
                json_encode(["error" => "dados inválidos"]),
                $this->cashFlow->message->json()
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

        $this->cashFlowGroup->persistData($cashFlowGroupData);
        $this->cashFlow = new CashFlow();

        $cashFlowData = [
            "uuid" => Uuid::uuid6(),
            "id_user" => $this->user,
            "id_cash_flow_group" => $this->cashFlowGroup,
            "entry" => "2.144,22",
            "history" => "Vendas",
            "entry_type" => 1,
            "created_at" => date("Y-m-d"),
            "updated_at" => date("Y-m-d"),
            "deleted" => 0,
        ];
        $response = $this->cashFlow->persistData($cashFlowData);
        $this->assertTrue($response);
        $this->customer->dropCustomerByUuid();
    }

    public function testPersistDataInvalidEntry()
    {
        $this->cashFlow = new CashFlow();
        $cashFlowData = [
            "uuid" => Uuid::uuid6(),
            "id_user" => new User(),
            "id_cash_flow_group" => new CashFlowGroup(),
            "entry" => "R$ 2.144,22",
            "history" => "Vendas",
            "entry_type" => 1,
            "created_at" => date("Y-m-d"),
            "updated_at" => date("Y-m-d"),
            "deleted" => 0,
        ];

        $response = $this->cashFlow->persistData($cashFlowData);
        if (empty($response)) {
            $this->assertJsonStringEqualsJsonString(
                json_encode(["error" => "valor de entrada inválido"]),
                $this->cashFlow->message->json()
            );
        }
    }

    public function testInvalidCashFlowGroupOnPersistData()
    {
        $this->cashFlow = new CashFlow();
        $this->user = new User();
        $this->user->setId(10000);
        $cashFlowData = [
            "uuid" => Uuid::uuid6(),
            "id_user" => $this->user,
            "id_cash_flow_group" => $this->user,
            "entry" => "2.144,22",
            "history" => "Vendas",
            "entry_type" => 1,
            "created_at" => date("Y-m-d"),
            "updated_at" => date("Y-m-d"),
            "deleted" => 0,
        ];
        $this->expectException(Exception::class);
        $this->expectExceptionMessage("Instância inválida ao persistir o dado");
        $this->cashFlow->persistData($cashFlowData);
    }

    public function testInvalidUserOnPersistData()
    {
        $this->cashFlow = new CashFlow();
        $this->cashFlowGroup = new CashFlowGroup();
        $this->cashFlowGroup->setId(10000);

        $cashFlowData = [
            "uuid" => Uuid::uuid6(),
            "id_user" => $this->cashFlowGroup,
            "id_cash_flow_group" => $this->cashFlowGroup,
            "entry" => "2.144,22",
            "history" => "Vendas",
            "entry_type" => 1,
            "created_at" => date("Y-m-d"),
            "updated_at" => date("Y-m-d"),
            "deleted" => 0,
        ];
        $this->expectException(Exception::class);
        $this->expectExceptionMessage("Instância inválida ao persistir o dado");
        $this->cashFlow->persistData($cashFlowData);
    }

    public function testCalculateBalance()
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
        for ($i = 0; $i < 3; $i++) {
            $this->cashFlowGroup = new CashFlowGroup();
            $cashFlowGroupData = [
                "uuid" => Uuid::uuid6(),
                "id_user" => $this->user,
                "group_name" => "Receitas",
                "created_at" => date("Y-m-d"),
                "updated_at" => date("Y-m-d"),
                "deleted" => 0
            ];

            $this->cashFlowGroup->persistData($cashFlowGroupData);
            $this->cashFlow = new CashFlow();

            $cashFlowData = [
                "uuid" => Uuid::uuid6(),
                "id_user" => $this->user,
                "id_cash_flow_group" => $this->cashFlowGroup,
                "entry" => "2.144,22",
                "history" => "Vendas",
                "entry_type" => 1,
                "created_at" => date("Y-m-d"),
                "updated_at" => date("Y-m-d"),
                "deleted" => 0,
            ];
            $this->cashFlow->persistData($cashFlowData);
        }

        $this->cashFlow = new CashFlow();
        $balance = $this->cashFlow->calculateBalance($this->user);
        $this->assertIsFloat($balance);

        $this->assertEquals(6432.66, $balance);
        $this->customer->dropCustomerByUuid();
    }

    public function testGetId()
    {
        $this->cashFlow = new CashFlow();
        $this->cashFlow->setId(1000);
        $this->assertIsInt($this->cashFlow->getId());
    }

    public function testGetIdIsEmpty()
    {
        $this->cashFlow = new CashFlow();
        $this->cashFlow->setId(0);
        $this->expectException(Exception::class);
        $this->expectExceptionMessage("id não atribuido");
        $this->cashFlow->getId();
    }

    public function testFindCashFlowByUser()
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

        $this->cashFlowGroup->persistData($cashFlowGroupData);
        $this->cashFlow = new CashFlow();

        $cashFlowData = [
            "uuid" => Uuid::uuid6(),
            "id_user" => $this->user,
            "id_cash_flow_group" => $this->cashFlowGroup,
            "entry" => "2.144,22",
            "history" => "Vendas",
            "entry_type" => 1,
            "created_at" => date("Y-m-d"),
            "updated_at" => date("Y-m-d"),
            "deleted" => 0,
        ];
        $this->cashFlow->persistData($cashFlowData);
        $this->cashFlow = new CashFlow();

        $cashFlowData = $this->cashFlow->findCashFlowByUser([], $this->user);
        $this->assertIsArray($cashFlowData);
        if (!empty($cashFlowData)) {
            foreach ($cashFlowData as $object) {
                $this->assertInstanceOf(ModelsCashFlow::class, $object);
            }
        }
        $this->customer->dropCustomerByUuid();
    }

    public function testFindCashFlowByUserIsEmpty()
    {
        $this->user = new User();
        $this->user->setId(100000000000000000);
        $this->cashFlow = new CashFlow();
        $response = $this->cashFlow->findCashFlowByUser([], $this->user);
        if (empty($response)) {
            $this->assertJsonStringEqualsJsonString(
                json_encode(["error" => "nenhum registro foi encontrado"]),
                $this->cashFlow->message->json()
            );
        }
    }

    public function testGetUuid()
    {
        $this->cashFlow = new CashFlow();
        $this->cashFlow->setUuid(Uuid::uuid6());
        $response = $this->cashFlow->getUuid();
        $this->assertIsString($response);
    }

    public function testInvalidUuid()
    {
        $this->cashFlow = new CashFlow();
        $this->expectException(Exception::class);
        $this->expectExceptionMessage("uuid inválido");
        $this->cashFlow->setUuid("");
    }

    public function testFindCashFlowByUuid()
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

        $this->cashFlowGroup->persistData($cashFlowGroupData);
        $this->cashFlow = new CashFlow();

        $cashFlowUuid = Uuid::uuid6();
        $cashFlowData = [
            "uuid" => $cashFlowUuid,
            "id_user" => $this->user,
            "id_cash_flow_group" => $this->cashFlowGroup,
            "entry" => "2.144,22",
            "history" => "Vendas",
            "entry_type" => 1,
            "created_at" => date("Y-m-d"),
            "updated_at" => date("Y-m-d"),
            "deleted" => 0,
        ];

        $this->cashFlow->persistData($cashFlowData);
        $this->cashFlow = new CashFlow();
        
        $this->cashFlow->setUuid($cashFlowUuid);
        $cashFlowData = $this->cashFlow->findCashFlowByUuid();

        $this->assertInstanceOf(ModelsCashFlow::class, $cashFlowData);
        $this->customer->dropCustomerByUuid();
    }

    public function testFindCashFlowByUuidIsEmpty()
    {
        $this->cashFlow = new CashFlow();
        $this->cashFlow->setUuid(Uuid::uuid6());
        $this->cashFlow->findCashFlowByUuid();
        if (empty($reponse)) {
            $this->assertJsonStringEqualsJsonString(
                json_encode(["error" => "o registro fluxo de caixa não existe"]),
                $this->cashFlow->message->json()
            );
        }
    }

    public function testDropCashFlowByUuid()
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

        $this->cashFlowGroup->persistData($cashFlowGroupData);
        $this->cashFlow = new CashFlow();

        $cashFlowUuid = Uuid::uuid6();
        $cashFlowData = [
            "uuid" => $cashFlowUuid,
            "id_user" => $this->user,
            "id_cash_flow_group" => $this->cashFlowGroup,
            "entry" => "2.144,22",
            "history" => "Vendas",
            "entry_type" => 1,
            "created_at" => date("Y-m-d"),
            "updated_at" => date("Y-m-d"),
            "deleted" => 0,
        ];

        $this->cashFlow->persistData($cashFlowData);
        $this->cashFlow = new CashFlow();
        
        $this->cashFlow->setUuid($cashFlowUuid);
        $response = $this->cashFlow->dropCashFlowByUuid();

        $this->assertTrue($response);
        $this->customer->dropCustomerByUuid();
    }

    public function testUpdateCashFlowByUuid()
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

        $this->cashFlowGroup->persistData($cashFlowGroupData);
        $this->cashFlow = new CashFlow();
        $cashFlowUuid = Uuid::uuid6();

        $cashFlowData = [
            "uuid" => $cashFlowUuid,
            "id_user" => $this->user,
            "id_cash_flow_group" => $this->cashFlowGroup,
            "entry" => "2.144,22",
            "history" => "Vendas",
            "entry_type" => 1,
            "created_at" => date("Y-m-d"),
            "updated_at" => date("Y-m-d"),
            "deleted" => 0,
        ];

        $this->cashFlow->persistData($cashFlowData);
        $cashFlowData["history"] = "Receita total do mês";

        $this->cashFlow = new CashFlow();
        $response = $this->cashFlow->updateCashFlowByUuid($cashFlowData);
        
        $this->assertTrue($response);
        $this->cashFlow = new CashFlow();
        
        $this->cashFlow->setUuid($cashFlowUuid);
        $cashFlowData = $this->cashFlow->findCashFlowByUuid();
        
        $this->assertEquals("Receita total do mês", $cashFlowData->getHistory());
        $this->customer->dropCustomerByUuid();
    }

    public function testUpdateCashFlowByUuidEmptyParam()
    {
        $this->cashFlow = new CashFlow();
        $this->cashFlow->setUuid(Uuid::uuid6());
        $response = $this->cashFlow->updateCashFlowByUuid([]);
        if (empty($response)) {
            $this->assertJsonStringEqualsJsonString(
                json_encode(["error" => "parametro data não pode ser vazio"]),
                $this->cashFlow->message->json()
            );
        }
    }

    public function testUpdateCashFlowByUuidNotFound()
    {
        $this->cashFlow = new CashFlow();
        $response = $this->cashFlow->updateCashFlowByUuid(["uuid" => Uuid::uuid6()]);
        if (empty($response)) {
            $this->assertJsonStringEqualsJsonString(
                json_encode(["error" => "registro de fluxo de caixa não encontrado"]),
                $this->cashFlow->message->json()
            );
        }
    }

    public function testUpdateCashFlowByUuidInvalidCashFlowGroup()
    {
        $this->customer = new Customer();
        $customerUuid = "1eed7357-6e74-6096-abf0-0242ac120003";

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

        $this->cashFlowGroup->persistData($cashFlowGroupData);
        $this->cashFlow = new CashFlow();

        $cashFlowUuid = Uuid::uuid6();
        $this->cashFlow->setUuid($cashFlowUuid);

        $cashFlowData = [
            "uuid" => $cashFlowUuid,
            "id_user" => $this->user,
            "id_cash_flow_group" => $this->cashFlowGroup,
            "entry" => "2.144,22",
            "history" => "Vendas",
            "entry_type" => 1,
            "created_at" => date("Y-m-d"),
            "updated_at" => date("Y-m-d"),
            "deleted" => 0,
        ];

        $this->cashFlow->persistData($cashFlowData);
        $this->expectException(Exception::class);
        $this->expectExceptionMessage("Instância inválida ao atualizar o dado");

        $cashFlowData["id_cash_flow_group"] = $this->user;
        $this->cashFlow->updateCashFlowByUuid($cashFlowData);
        $this->customer->dropCustomerByUuid();
    }

    public function testCleanDataBaseA()
    {
        $this->customer = new Customer();
        $this->customer->setUuid("1eed7357-6e74-6096-abf0-0242ac120003");
        $response = $this->customer->dropCustomerByUuid();
        $this->assertTrue($response);
    }

    public function testUpdateCashFlowByUuidInvalidUser()
    {
        $this->customer = new Customer();
        $customerUuid = "1eed7357-6e74-6096-abf0-0242ac120003";

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

        $this->cashFlowGroup->persistData($cashFlowGroupData);
        $this->cashFlow = new CashFlow();

        $cashFlowUuid = Uuid::uuid6();
        $this->cashFlow->setUuid($cashFlowUuid);

        $cashFlowData = [
            "uuid" => $cashFlowUuid,
            "id_user" => $this->user,
            "id_cash_flow_group" => $this->cashFlowGroup,
            "entry" => "2.144,22",
            "history" => "Vendas",
            "entry_type" => 1,
            "created_at" => date("Y-m-d"),
            "updated_at" => date("Y-m-d"),
            "deleted" => 0,
        ];

        $this->cashFlow->persistData($cashFlowData);
        $this->expectException(Exception::class);
        $this->expectExceptionMessage("Instância inválida ao atualizar o dado");

        $cashFlowData["id_user"] = $this->cashFlowGroup;
        $this->cashFlow->updateCashFlowByUuid($cashFlowData);
    }

    public function testCleanDataBaseB()
    {
        $this->customer = new Customer();
        $this->customer->setUuid("1eed7357-6e74-6096-abf0-0242ac120003");
        $response = $this->customer->dropCustomerByUuid();
        $this->assertTrue($response);
    }

    public function testFindCashFlowDataByDate()
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

        $this->cashFlowGroup->persistData($cashFlowGroupData);
        $this->cashFlow = new CashFlow();

        $cashFlowUuid = Uuid::uuid6();
        $this->cashFlow->setUuid($cashFlowUuid);

        $cashFlowData = [
            "uuid" => $cashFlowUuid,
            "id_user" => $this->user,
            "id_cash_flow_group" => $this->cashFlowGroup,
            "entry" => "2.144,22",
            "history" => "Vendas",
            "entry_type" => 1,
            "created_at" => date("Y-m-d"),
            "updated_at" => date("Y-m-d"),
            "deleted" => 0,
        ];

        $this->cashFlow->persistData($cashFlowData);
        $response = $this->cashFlow
        ->findCashFlowDataByDate(date("d/m/Y") . "-" . date("d/m/Y"), $this->user);
        $this->assertIsArray($response);
        if (!empty($response)) {
            foreach ($response as $object) {
                $this->assertInstanceOf(ModelsCashFlow::class, $object);
            }
        }
        $this->customer->dropCustomerByUuid();
    }

    public function testFindCashFlowDataByDateIsInvalid()
    {
        $this->user = new User();
        $this->cashFlow = new CashFlow();
        $this->expectException(Exception::class);
        $this->expectExceptionMessage("parametro dates inválido");
        $this->cashFlow
            ->findCashFlowDataByDate("07/04/2024-07/04/2024-07/04/2024", $this->user);
    }

    public function testFindCashFlowDataByDateIsEmpty()
    {
        $this->user = new User();
        $this->cashFlow = new CashFlow();
        $response = $this->cashFlow->findCashFlowDataByDate("", $this->user);
        $this->assertIsArray($response);
        $this->assertEmpty($response);
    }

    public function testFindGroupAccountsAgrupped()
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

        $this->cashFlowGroup->persistData($cashFlowGroupData);
        $this->cashFlow = new CashFlow();

        $cashFlowUuid = Uuid::uuid6();
        $this->cashFlow->setUuid($cashFlowUuid);

        $cashFlowData = [
            "uuid" => $cashFlowUuid,
            "id_user" => $this->user,
            "id_cash_flow_group" => $this->cashFlowGroup,
            "entry" => "2.144,22",
            "history" => "Vendas",
            "entry_type" => 1,
            "created_at" => date("Y-m-d"),
            "updated_at" => date("Y-m-d"),
            "deleted" => 0,
        ];

        $this->cashFlow->persistData($cashFlowData);
        $response = $this->cashFlow->findGroupAccountsAgrupped($this->user);
        $this->assertIsArray($response);
        $this->assertNotEmpty($response);
        $this->customer->dropCustomerByUuid();
    }

    public function testCashFlowDeletedTrue()
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

        $this->cashFlowGroup->persistData($cashFlowGroupData);
        $this->cashFlow = new CashFlow();

        $cashFlowUuid = Uuid::uuid6();
        $this->cashFlow->setUuid($cashFlowUuid);

        $cashFlowData = [
            "uuid" => $cashFlowUuid,
            "id_user" => $this->user,
            "id_cash_flow_group" => $this->cashFlowGroup,
            "entry" => "2.144,22",
            "history" => "Vendas",
            "entry_type" => 1,
            "created_at" => date("Y-m-d"),
            "updated_at" => date("Y-m-d"),
            "deleted" => 1,
        ];

        $this->cashFlow->persistData($cashFlowData);
        $response = $this->cashFlow->findCashFlowDeletedTrue([], $this->user);
        $this->assertIsArray($response);
        if (!empty($response)) {
            foreach ($response as $object) {
                $this->assertInstanceOf(ModelsCashFlow::class, $object);
            }
        }
        $this->customer->dropCustomerByUuid();
    }

    public function testCashFlowDeletedTrueIsEmpty()
    {
        $this->user = new User();
        $this->cashFlow = new CashFlow();
        $this->user->setId(4525896235);
        $response = $this->cashFlow->findCashFlowDeletedTrue([], $this->user);
        if (empty($response)) {
            $this->assertJsonStringEqualsJsonString(
                json_encode(["error" => "não há registros deletados"]),
                $this->cashFlow->message->json()
            );
        }
    }

    public function testSetterCashFlow()
    {
        $this->cashFlow = new CashFlow();
        $this->cashFlow->name = "roberto";
        $this->assertEquals("roberto", $this->cashFlow->name);
    }
}
