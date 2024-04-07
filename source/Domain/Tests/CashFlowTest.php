<?php

namespace Source\Domain\Tests;

use Exception;
use PHPUnit\Framework\TestCase;
use Ramsey\Uuid\Nonstandard\Uuid;
use Source\Domain\Model\CashFlow;
use Source\Domain\Model\CashFlowGroup;
use Source\Domain\Model\Customer;
use Source\Domain\Model\User;

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
}
