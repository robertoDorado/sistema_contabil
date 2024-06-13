<?php

namespace Source\Domain\Tests;

use Exception;
use PHPUnit\Framework\TestCase;
use Ramsey\Uuid\Nonstandard\Uuid;
use Source\Domain\Model\CashFlow;
use Source\Domain\Model\CashFlowGroup;
use Source\Domain\Model\Company;
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

    /** @var Company */
    private Company $company;

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

    public function testInvalidUuidOnPersistData()
    {
        $this->user = new User();
        $this->user->setId(32565214);

        $this->cashFlowGroup = new CashFlowGroup();
        $this->cashFlowGroup->setId(639652368);
        
        $this->cashFlow = new CashFlow();
        $cashFlowData = [
            "uuid" => "----------",
            "id_user" => $this->user,
            "id_company" => random_int(1, 1000),
            "id_cash_flow_group" => $this->cashFlowGroup,
            "entry" => "2.144,22",
            "history" => "Vendas",
            "entry_type" => 1,
            "created_at" => date("Y-m-d"),
            "updated_at" => date("Y-m-d"),
            "deleted" => 0,
        ];

        $this->expectException(Exception::class);
        $this->expectExceptionMessage("uuid inválido");
        $this->cashFlow->persistData($cashFlowData);
    }

    public function testPersistData()
    {
        $this->customer = new Customer();
        $customerUuid = Uuid::uuid4();
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
            "uuid" => Uuid::uuid4(),
            "user_full_name" => "Sara Luzia Stefany Gomes",
            "user_nick_name" => "saraLuiza",
            "user_email" => $requestPost["customer_email"],
            "user_password" => password_hash("senha123", PASSWORD_DEFAULT),
            "deleted" => 0
        ];

        $this->user->persistData($userData);
        $this->company = new Company();
        $this->company->persistData([
            "uuid" => Uuid::uuid4(),
            "id_user" => $this->user,
            "company_name" => "Cristiane e Kaique Padaria Ltda",
            "company_document" => "92.530.674/0001-16",
            "state_registration" => "214.647.670.499",
            "opening_date" => date("Y-m-d", strtotime(str_replace("/", "-", "04/10/2019"))),
            "web_site" => "www.cristianeekaiquepadarialtda.com.br",
            "company_email" => "desenvolvimento@cristianeekaiquepadarialtda.com.br",
            "company_zipcode" => "17031-350",
            "company_address" => "Rua Coronel Ivon César Pimentel",
            "company_address_number" => "294",
            "company_neighborhood" => "Parque Paulista",
            "company_city" => "Bauru",
            "company_state" => "SP",
            "company_phone" => "(14) 3858-1464",
            "company_cell_phone" => "(14) 98974-4671",
            "created_at" => date("Y-m-d"),
            "updated_at" => date("Y-m-d"),
            "deleted" => 0
        ]);
        
        $this->cashFlowGroup = new CashFlowGroup();
        $cashFlowGroupData = [
            "uuid" => Uuid::uuid4(),
            "id_user" => $this->user,
            "id_company" => $this->company->getId(),
            "group_name" => "Receitas",
            "created_at" => date("Y-m-d"),
            "updated_at" => date("Y-m-d"),
            "deleted" => 0
        ];

        $this->cashFlowGroup->persistData($cashFlowGroupData);
        $this->cashFlow = new CashFlow();

        $cashFlowData = [
            "uuid" => Uuid::uuid4(),
            "id_user" => $this->user,
            "id_company" => $this->company->getId(),
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

    public function testInvaldKeyOnPersistData()
    {
        $this->user = new User();
        $this->user->setId(rand(1000000, 1000000000));
        $this->cashFlowGroup = new CashFlowGroup();
        $cashFlowGroupData = [
            "uuid" => Uuid::uuid4(),
            "id_company" => random_int(1, 1000),
            "iduser" => $this->user,
            "group_name" => "Receitas",
            "created_at" => date("Y-m-d"),
            "updated_at" => date("Y-m-d"),
            "deleted" => 0
        ];

        $this->expectException(Exception::class);
        $this->expectExceptionMessage("esta propriedade id_user foi passado de maneira incorreta");
        $this->cashFlowGroup->persistData($cashFlowGroupData);
    }

    public function testInvalidCashFlowGroupOnPersistData()
    {
        $this->cashFlow = new CashFlow();
        $this->user = new User();
        $this->user->setId(10000);
        $cashFlowData = [
            "uuid" => Uuid::uuid4(),
            "id_user" => $this->user,
            "id_company" => random_int(1, 1000),
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
            "uuid" => Uuid::uuid4(),
            "id_user" => $this->cashFlowGroup,
            "id_company" => random_int(1, 1000),
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
        $customerUuid = Uuid::uuid4();
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
            "uuid" => Uuid::uuid4(),
            "user_full_name" => "Sara Luzia Stefany Gomes",
            "user_nick_name" => "saraLuiza",
            "user_email" => $requestPost["customer_email"],
            "user_password" => password_hash("senha123", PASSWORD_DEFAULT),
            "deleted" => 0
        ];

        $this->user->persistData($userData);
        $this->company = new Company();
        $this->company->persistData([
            "uuid" => Uuid::uuid4(),
            "id_user" => $this->user,
            "company_name" => "Cristiane e Kaique Padaria Ltda",
            "company_document" => "92.530.674/0001-16",
            "state_registration" => "214.647.670.499",
            "opening_date" => date("Y-m-d", strtotime(str_replace("/", "-", "04/10/2019"))),
            "web_site" => "www.cristianeekaiquepadarialtda.com.br",
            "company_email" => "desenvolvimento@cristianeekaiquepadarialtda.com.br",
            "company_zipcode" => "17031-350",
            "company_address" => "Rua Coronel Ivon César Pimentel",
            "company_address_number" => "294",
            "company_neighborhood" => "Parque Paulista",
            "company_city" => "Bauru",
            "company_state" => "SP",
            "company_phone" => "(14) 3858-1464",
            "company_cell_phone" => "(14) 98974-4671",
            "created_at" => date("Y-m-d"),
            "updated_at" => date("Y-m-d"),
            "deleted" => 0
        ]);

        for ($i = 0; $i < 3; $i++) {
            $this->cashFlowGroup = new CashFlowGroup();
            $cashFlowGroupData = [
                "uuid" => Uuid::uuid4(),
                "id_user" => $this->user,
                "id_company" => $this->company->getId(),
                "group_name" => "Receitas",
                "created_at" => date("Y-m-d"),
                "updated_at" => date("Y-m-d"),
                "deleted" => 0
            ];

            $this->cashFlowGroup->persistData($cashFlowGroupData);
            $this->cashFlow = new CashFlow();

            $cashFlowData = [
                "uuid" => Uuid::uuid4(),
                "id_user" => $this->user,
                "id_company" => $this->company->getId(),
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
        $customerUuid = Uuid::uuid4();
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
            "uuid" => Uuid::uuid4(),
            "user_full_name" => "Sara Luzia Stefany Gomes",
            "user_nick_name" => "saraLuiza",
            "user_email" => $requestPost["customer_email"],
            "user_password" => password_hash("senha123", PASSWORD_DEFAULT),
            "deleted" => 0
        ];

        $this->user->persistData($userData);
        $this->company = new Company();
        $this->company->persistData([
            "uuid" => Uuid::uuid4(),
            "id_user" => $this->user,
            "company_name" => "Cristiane e Kaique Padaria Ltda",
            "company_document" => "92.530.674/0001-16",
            "state_registration" => "214.647.670.499",
            "opening_date" => date("Y-m-d", strtotime(str_replace("/", "-", "04/10/2019"))),
            "web_site" => "www.cristianeekaiquepadarialtda.com.br",
            "company_email" => "desenvolvimento@cristianeekaiquepadarialtda.com.br",
            "company_zipcode" => "17031-350",
            "company_address" => "Rua Coronel Ivon César Pimentel",
            "company_address_number" => "294",
            "company_neighborhood" => "Parque Paulista",
            "company_city" => "Bauru",
            "company_state" => "SP",
            "company_phone" => "(14) 3858-1464",
            "company_cell_phone" => "(14) 98974-4671",
            "created_at" => date("Y-m-d"),
            "updated_at" => date("Y-m-d"),
            "deleted" => 0
        ]);

        $this->cashFlowGroup = new CashFlowGroup();
        $cashFlowGroupData = [
            "uuid" => Uuid::uuid4(),
            "id_user" => $this->user,
            "id_company" => $this->company->getId(),
            "group_name" => "Receitas",
            "created_at" => date("Y-m-d"),
            "updated_at" => date("Y-m-d"),
            "deleted" => 0
        ];

        $this->cashFlowGroup->persistData($cashFlowGroupData);
        $this->cashFlow = new CashFlow();

        $cashFlowData = [
            "uuid" => Uuid::uuid4(),
            "id_user" => $this->user,
            "id_company" => $this->company->getId(),
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

        $cashFlowData = $this->cashFlow->findCashFlowByUser([], $this->user, $this->company->getId());
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
        $response = $this->cashFlow->findCashFlowByUser([], $this->user, random_int(1, 1000));
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
        $this->cashFlow->setUuid(Uuid::uuid4());
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
        $customerUuid = Uuid::uuid4();
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
            "uuid" => Uuid::uuid4(),
            "user_full_name" => "Sara Luzia Stefany Gomes",
            "user_nick_name" => "saraLuiza",
            "user_email" => $requestPost["customer_email"],
            "user_password" => password_hash("senha123", PASSWORD_DEFAULT),
            "deleted" => 0
        ];

        $this->user->persistData($userData);
        $this->company = new Company();
        $this->company->persistData([
            "uuid" => Uuid::uuid4(),
            "id_user" => $this->user,
            "company_name" => "Cristiane e Kaique Padaria Ltda",
            "company_document" => "92.530.674/0001-16",
            "state_registration" => "214.647.670.499",
            "opening_date" => date("Y-m-d", strtotime(str_replace("/", "-", "04/10/2019"))),
            "web_site" => "www.cristianeekaiquepadarialtda.com.br",
            "company_email" => "desenvolvimento@cristianeekaiquepadarialtda.com.br",
            "company_zipcode" => "17031-350",
            "company_address" => "Rua Coronel Ivon César Pimentel",
            "company_address_number" => "294",
            "company_neighborhood" => "Parque Paulista",
            "company_city" => "Bauru",
            "company_state" => "SP",
            "company_phone" => "(14) 3858-1464",
            "company_cell_phone" => "(14) 98974-4671",
            "created_at" => date("Y-m-d"),
            "updated_at" => date("Y-m-d"),
            "deleted" => 0
        ]);

        $this->cashFlowGroup = new CashFlowGroup();
        $cashFlowGroupData = [
            "uuid" => Uuid::uuid4(),
            "id_user" => $this->user,
            "id_company" => $this->company->getId(),
            "group_name" => "Receitas",
            "created_at" => date("Y-m-d"),
            "updated_at" => date("Y-m-d"),
            "deleted" => 0
        ];

        $this->cashFlowGroup->persistData($cashFlowGroupData);
        $this->cashFlow = new CashFlow();

        $cashFlowUuid = Uuid::uuid4();
        $cashFlowData = [
            "uuid" => $cashFlowUuid,
            "id_user" => $this->user,
            "id_company" => $this->company->getId(),
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
        $this->cashFlow->setUuid(Uuid::uuid4());
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
        $customerUuid = Uuid::uuid4();
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
            "uuid" => Uuid::uuid4(),
            "user_full_name" => "Sara Luzia Stefany Gomes",
            "user_nick_name" => "saraLuiza",
            "user_email" => $requestPost["customer_email"],
            "user_password" => password_hash("senha123", PASSWORD_DEFAULT),
            "deleted" => 0
        ];

        $this->user->persistData($userData);
        $this->company = new Company();
        $this->company->persistData([
            "uuid" => Uuid::uuid4(),
            "id_user" => $this->user,
            "company_name" => "Cristiane e Kaique Padaria Ltda",
            "company_document" => "92.530.674/0001-16",
            "state_registration" => "214.647.670.499",
            "opening_date" => date("Y-m-d", strtotime(str_replace("/", "-", "04/10/2019"))),
            "web_site" => "www.cristianeekaiquepadarialtda.com.br",
            "company_email" => "desenvolvimento@cristianeekaiquepadarialtda.com.br",
            "company_zipcode" => "17031-350",
            "company_address" => "Rua Coronel Ivon César Pimentel",
            "company_address_number" => "294",
            "company_neighborhood" => "Parque Paulista",
            "company_city" => "Bauru",
            "company_state" => "SP",
            "company_phone" => "(14) 3858-1464",
            "company_cell_phone" => "(14) 98974-4671",
            "created_at" => date("Y-m-d"),
            "updated_at" => date("Y-m-d"),
            "deleted" => 0
        ]);

        $this->cashFlowGroup = new CashFlowGroup();
        $cashFlowGroupData = [
            "uuid" => Uuid::uuid4(),
            "id_company" => $this->company->getId(),
            "id_user" => $this->user,
            "group_name" => "Receitas",
            "created_at" => date("Y-m-d"),
            "updated_at" => date("Y-m-d"),
            "deleted" => 0
        ];

        $this->cashFlowGroup->persistData($cashFlowGroupData);
        $this->cashFlow = new CashFlow();

        $cashFlowUuid = Uuid::uuid4();
        $cashFlowData = [
            "uuid" => $cashFlowUuid,
            "id_user" => $this->user,
            "id_company" => $this->company->getId(),
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
        $customerUuid = Uuid::uuid4();
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
            "uuid" => Uuid::uuid4(),
            "user_full_name" => "Sara Luzia Stefany Gomes",
            "user_nick_name" => "saraLuiza",
            "user_email" => $requestPost["customer_email"],
            "user_password" => password_hash("senha123", PASSWORD_DEFAULT),
            "deleted" => 0
        ];

        $this->user->persistData($userData);
        $this->company = new Company();
        $this->company->persistData([
            "uuid" => Uuid::uuid4(),
            "id_user" => $this->user,
            "company_name" => "Cristiane e Kaique Padaria Ltda",
            "company_document" => "92.530.674/0001-16",
            "state_registration" => "214.647.670.499",
            "opening_date" => date("Y-m-d", strtotime(str_replace("/", "-", "04/10/2019"))),
            "web_site" => "www.cristianeekaiquepadarialtda.com.br",
            "company_email" => "desenvolvimento@cristianeekaiquepadarialtda.com.br",
            "company_zipcode" => "17031-350",
            "company_address" => "Rua Coronel Ivon César Pimentel",
            "company_address_number" => "294",
            "company_neighborhood" => "Parque Paulista",
            "company_city" => "Bauru",
            "company_state" => "SP",
            "company_phone" => "(14) 3858-1464",
            "company_cell_phone" => "(14) 98974-4671",
            "created_at" => date("Y-m-d"),
            "updated_at" => date("Y-m-d"),
            "deleted" => 0
        ]);

        $this->cashFlowGroup = new CashFlowGroup();
        $cashFlowGroupData = [
            "uuid" => Uuid::uuid4(),
            "id_user" => $this->user,
            "id_company" => $this->company->getId(),
            "group_name" => "Receitas",
            "created_at" => date("Y-m-d"),
            "updated_at" => date("Y-m-d"),
            "deleted" => 0
        ];

        $this->cashFlowGroup->persistData($cashFlowGroupData);
        $this->cashFlow = new CashFlow();
        $cashFlowUuid = Uuid::uuid4();

        $cashFlowData = [
            "uuid" => $cashFlowUuid,
            "id_user" => $this->user,
            "id_company" => $this->company->getId(),
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
        $this->cashFlow->setUuid(Uuid::uuid4());
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
        $response = $this->cashFlow->updateCashFlowByUuid(["uuid" => Uuid::uuid4()]);
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
            "uuid" => Uuid::uuid4(),
            "user_full_name" => "Sara Luzia Stefany Gomes",
            "user_nick_name" => "saraLuiza",
            "user_email" => $requestPost["customer_email"],
            "user_password" => password_hash("senha123", PASSWORD_DEFAULT),
            "deleted" => 0
        ];

        $this->user->persistData($userData);
        $this->company = new Company();
        $this->company->persistData([
            "uuid" => Uuid::uuid4(),
            "id_user" => $this->user,
            "company_name" => "Cristiane e Kaique Padaria Ltda",
            "company_document" => "92.530.674/0001-16",
            "state_registration" => "214.647.670.499",
            "opening_date" => date("Y-m-d", strtotime(str_replace("/", "-", "04/10/2019"))),
            "web_site" => "www.cristianeekaiquepadarialtda.com.br",
            "company_email" => "desenvolvimento@cristianeekaiquepadarialtda.com.br",
            "company_zipcode" => "17031-350",
            "company_address" => "Rua Coronel Ivon César Pimentel",
            "company_address_number" => "294",
            "company_neighborhood" => "Parque Paulista",
            "company_city" => "Bauru",
            "company_state" => "SP",
            "company_phone" => "(14) 3858-1464",
            "company_cell_phone" => "(14) 98974-4671",
            "created_at" => date("Y-m-d"),
            "updated_at" => date("Y-m-d"),
            "deleted" => 0
        ]);

        $this->cashFlowGroup = new CashFlowGroup();
        $cashFlowGroupData = [
            "uuid" => Uuid::uuid4(),
            "id_user" => $this->user,
            "id_company" => $this->company->getId(),
            "group_name" => "Receitas",
            "created_at" => date("Y-m-d"),
            "updated_at" => date("Y-m-d"),
            "deleted" => 0
        ];

        $this->cashFlowGroup->persistData($cashFlowGroupData);
        $this->cashFlow = new CashFlow();

        $cashFlowUuid = Uuid::uuid4();
        $this->cashFlow->setUuid($cashFlowUuid);

        $cashFlowData = [
            "uuid" => $cashFlowUuid,
            "id_user" => $this->user,
            "id_company" => $this->company->getId(),
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
            "uuid" => Uuid::uuid4(),
            "user_full_name" => "Sara Luzia Stefany Gomes",
            "user_nick_name" => "saraLuiza",
            "user_email" => $requestPost["customer_email"],
            "user_password" => password_hash("senha123", PASSWORD_DEFAULT),
            "deleted" => 0
        ];

        $this->user->persistData($userData);
        $this->company = new Company();
        $this->company->persistData([
            "uuid" => Uuid::uuid4(),
            "id_user" => $this->user,
            "company_name" => "Cristiane e Kaique Padaria Ltda",
            "company_document" => "92.530.674/0001-16",
            "state_registration" => "214.647.670.499",
            "opening_date" => date("Y-m-d", strtotime(str_replace("/", "-", "04/10/2019"))),
            "web_site" => "www.cristianeekaiquepadarialtda.com.br",
            "company_email" => "desenvolvimento@cristianeekaiquepadarialtda.com.br",
            "company_zipcode" => "17031-350",
            "company_address" => "Rua Coronel Ivon César Pimentel",
            "company_address_number" => "294",
            "company_neighborhood" => "Parque Paulista",
            "company_city" => "Bauru",
            "company_state" => "SP",
            "company_phone" => "(14) 3858-1464",
            "company_cell_phone" => "(14) 98974-4671",
            "created_at" => date("Y-m-d"),
            "updated_at" => date("Y-m-d"),
            "deleted" => 0
        ]);

        $this->cashFlowGroup = new CashFlowGroup();
        $cashFlowGroupData = [
            "uuid" => Uuid::uuid4(),
            "id_company" => $this->company->getId(),
            "id_user" => $this->user,
            "group_name" => "Receitas",
            "created_at" => date("Y-m-d"),
            "updated_at" => date("Y-m-d"),
            "deleted" => 0
        ];

        $this->cashFlowGroup->persistData($cashFlowGroupData);
        $this->cashFlow = new CashFlow();

        $cashFlowUuid = Uuid::uuid4();
        $this->cashFlow->setUuid($cashFlowUuid);

        $cashFlowData = [
            "uuid" => $cashFlowUuid,
            "id_user" => $this->user,
            "id_company" => $this->company->getId(),
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
        $customerUuid = Uuid::uuid4();
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
            "uuid" => Uuid::uuid4(),
            "user_full_name" => "Sara Luzia Stefany Gomes",
            "user_nick_name" => "saraLuiza",
            "user_email" => $requestPost["customer_email"],
            "user_password" => password_hash("senha123", PASSWORD_DEFAULT),
            "deleted" => 0
        ];

        $this->user->persistData($userData);
        $this->company = new Company();
        $this->company->persistData([
            "uuid" => Uuid::uuid4(),
            "id_user" => $this->user,
            "company_name" => "Cristiane e Kaique Padaria Ltda",
            "company_document" => "92.530.674/0001-16",
            "state_registration" => "214.647.670.499",
            "opening_date" => date("Y-m-d", strtotime(str_replace("/", "-", "04/10/2019"))),
            "web_site" => "www.cristianeekaiquepadarialtda.com.br",
            "company_email" => "desenvolvimento@cristianeekaiquepadarialtda.com.br",
            "company_zipcode" => "17031-350",
            "company_address" => "Rua Coronel Ivon César Pimentel",
            "company_address_number" => "294",
            "company_neighborhood" => "Parque Paulista",
            "company_city" => "Bauru",
            "company_state" => "SP",
            "company_phone" => "(14) 3858-1464",
            "company_cell_phone" => "(14) 98974-4671",
            "created_at" => date("Y-m-d"),
            "updated_at" => date("Y-m-d"),
            "deleted" => 0
        ]);

        $this->cashFlowGroup = new CashFlowGroup();
        $cashFlowGroupData = [
            "uuid" => Uuid::uuid4(),
            "id_user" => $this->user,
            "id_company" => $this->company->getId(),
            "group_name" => "Receitas",
            "created_at" => date("Y-m-d"),
            "updated_at" => date("Y-m-d"),
            "deleted" => 0
        ];

        $this->cashFlowGroup->persistData($cashFlowGroupData);
        $this->cashFlow = new CashFlow();

        $cashFlowUuid = Uuid::uuid4();
        $this->cashFlow->setUuid($cashFlowUuid);

        $cashFlowData = [
            "uuid" => $cashFlowUuid,
            "id_user" => $this->user,
            "id_company" => $this->company->getId(),
            "id_cash_flow_group" => $this->cashFlowGroup,
            "entry" => "2.144,22",
            "history" => "Vendas",
            "entry_type" => 1,
            "created_at" => date("Y-m-d"),
            "updated_at" => date("Y-m-d"),
            "deleted" => 0,
        ];

        $this->cashFlow->persistData($cashFlowData);
        $response = $this->cashFlow->findCashFlowDataByDate(date("d/m/Y") . "-" . date("d/m/Y"), $this->user, [], random_int(1, 1000));

        $this->assertIsArray($response);
        if (!empty($response)) {
            foreach ($response as $object) {
                $this->assertInstanceOf(ModelsCashFlow::class, $object);
            }
        }
        $this->customer->dropCustomerByUuid();
    }

    public function testFindCashFlowDataByDateIsDeleted()
    {
        $this->user = new User();
        $this->user->setId(rand(1000000, 1000000));
        
        $this->cashFlow = new CashFlow();
        $this->cashFlow->findCashFlowDataByDate(date("d/m/Y") . "-" . date("d/m/Y"), $this->user, [], random_int(1, 1000));
        
        $this->assertJsonStringEqualsJsonString(json_encode(["error" => "registro não encontrado"]),
        $this->cashFlow->message->json());
    }

    public function testFindCashFlowDataByDateIsInvalid()
    {
        $this->user = new User();
        $this->cashFlow = new CashFlow();
        $this->expectException(Exception::class);
        $this->expectExceptionMessage("parametro dates inválido");
        $this->cashFlow->findCashFlowDataByDate("07/04/2024-07/04/2024-07/04/2024", $this->user, [], random_int(1, 1000));
    }

    public function testFindCashFlowDataByDateIsEmpty()
    {
        $this->user = new User();
        $this->cashFlow = new CashFlow();
        $response = $this->cashFlow->findCashFlowDataByDate("", $this->user, [], random_int(1, 1000));
        $this->assertIsArray($response);
        $this->assertEmpty($response);
    }

    public function testFindGroupAccountsAgrupped()
    {
        $this->customer = new Customer();
        $customerUuid = Uuid::uuid4();
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
            "uuid" => Uuid::uuid4(),
            "user_full_name" => "Sara Luzia Stefany Gomes",
            "user_nick_name" => "saraLuiza",
            "user_email" => $requestPost["customer_email"],
            "user_password" => password_hash("senha123", PASSWORD_DEFAULT),
            "deleted" => 0
        ];

        $this->user->persistData($userData);
        $this->company = new Company();
        $this->company->persistData([
            "uuid" => Uuid::uuid4(),
            "id_user" => $this->user,
            "company_name" => "Cristiane e Kaique Padaria Ltda",
            "company_document" => "92.530.674/0001-16",
            "state_registration" => "214.647.670.499",
            "opening_date" => date("Y-m-d", strtotime(str_replace("/", "-", "04/10/2019"))),
            "web_site" => "www.cristianeekaiquepadarialtda.com.br",
            "company_email" => "desenvolvimento@cristianeekaiquepadarialtda.com.br",
            "company_zipcode" => "17031-350",
            "company_address" => "Rua Coronel Ivon César Pimentel",
            "company_address_number" => "294",
            "company_neighborhood" => "Parque Paulista",
            "company_city" => "Bauru",
            "company_state" => "SP",
            "company_phone" => "(14) 3858-1464",
            "company_cell_phone" => "(14) 98974-4671",
            "created_at" => date("Y-m-d"),
            "updated_at" => date("Y-m-d"),
            "deleted" => 0
        ]);

        $this->cashFlowGroup = new CashFlowGroup();
        $cashFlowGroupData = [
            "uuid" => Uuid::uuid4(),
            "id_user" => $this->user,
            "id_company" => $this->company->getId(),
            "group_name" => "Receitas",
            "created_at" => date("Y-m-d"),
            "updated_at" => date("Y-m-d"),
            "deleted" => 0
        ];

        $this->cashFlowGroup->persistData($cashFlowGroupData);
        $this->cashFlow = new CashFlow();

        $cashFlowUuid = Uuid::uuid4();
        $this->cashFlow->setUuid($cashFlowUuid);

        $cashFlowData = [
            "uuid" => $cashFlowUuid,
            "id_user" => $this->user,
            "id_company" => $this->company->getId(),
            "id_cash_flow_group" => $this->cashFlowGroup,
            "entry" => "2.144,22",
            "history" => "Vendas",
            "entry_type" => 1,
            "created_at" => date("Y-m-d"),
            "updated_at" => date("Y-m-d"),
            "deleted" => 0,
        ];

        $this->cashFlow->persistData($cashFlowData);
        $response = $this->cashFlow->findGroupAccountsAgrupped($this->user, $this->company->getId());
        $this->assertIsArray($response);
        $this->assertNotEmpty($response);
        $this->customer->dropCustomerByUuid();
    }

    public function testCashFlowDeletedTrue()
    {
        $this->customer = new Customer();
        $customerUuid = Uuid::uuid4();
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
            "uuid" => Uuid::uuid4(),
            "user_full_name" => "Sara Luzia Stefany Gomes",
            "user_nick_name" => "saraLuiza",
            "user_email" => $requestPost["customer_email"],
            "user_password" => password_hash("senha123", PASSWORD_DEFAULT),
            "deleted" => 0
        ];

        $this->user->persistData($userData);
        $this->company = new Company();
        $this->company->persistData([
            "uuid" => Uuid::uuid4(),
            "id_user" => $this->user,
            "company_name" => "Cristiane e Kaique Padaria Ltda",
            "company_document" => "92.530.674/0001-16",
            "state_registration" => "214.647.670.499",
            "opening_date" => date("Y-m-d", strtotime(str_replace("/", "-", "04/10/2019"))),
            "web_site" => "www.cristianeekaiquepadarialtda.com.br",
            "company_email" => "desenvolvimento@cristianeekaiquepadarialtda.com.br",
            "company_zipcode" => "17031-350",
            "company_address" => "Rua Coronel Ivon César Pimentel",
            "company_address_number" => "294",
            "company_neighborhood" => "Parque Paulista",
            "company_city" => "Bauru",
            "company_state" => "SP",
            "company_phone" => "(14) 3858-1464",
            "company_cell_phone" => "(14) 98974-4671",
            "created_at" => date("Y-m-d"),
            "updated_at" => date("Y-m-d"),
            "deleted" => 0
        ]);
        $this->cashFlowGroup = new CashFlowGroup();

        $cashFlowGroupData = [
            "uuid" => Uuid::uuid4(),
            "id_company" => $this->company->getId(),
            "id_user" => $this->user,
            "group_name" => "Receitas",
            "created_at" => date("Y-m-d"),
            "updated_at" => date("Y-m-d"),
            "deleted" => 0
        ];

        $this->cashFlowGroup->persistData($cashFlowGroupData);
        $this->cashFlow = new CashFlow();

        $cashFlowUuid = Uuid::uuid4();
        $this->cashFlow->setUuid($cashFlowUuid);

        $cashFlowData = [
            "uuid" => $cashFlowUuid,
            "id_user" => $this->user,
            "id_company" => $this->company->getId(),
            "id_cash_flow_group" => $this->cashFlowGroup,
            "entry" => "2.144,22",
            "history" => "Vendas",
            "entry_type" => 1,
            "created_at" => date("Y-m-d"),
            "updated_at" => date("Y-m-d"),
            "deleted" => 1,
        ];

        $this->cashFlow->persistData($cashFlowData);
        $response = $this->cashFlow->findCashFlowDeletedTrue([], $this->user, random_int(1, 1000));
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
        $response = $this->cashFlow->findCashFlowDeletedTrue([], $this->user, random_int(1, 1000));
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
