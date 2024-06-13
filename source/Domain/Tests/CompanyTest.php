<?php

namespace Source\Domain\Tests;

use Exception;
use PHPUnit\Framework\TestCase;
use Ramsey\Uuid\Nonstandard\Uuid;
use Source\Domain\Model\Company;
use Source\Domain\Model\Customer;
use Source\Domain\Model\User;

/**
 * CompanyTest Domain\Tests
 * @link 
 * @author Roberto Dorado <robertodorado7@gmail.com>
 * @package Source\Domain\Tests
 */
class CompanyTest extends TestCase
{
    /** @var Customer */
    private Customer $customer;

    /** @var User */
    private User $user;

    /** @var Company */
    private Company $company;

    public function testExceptionGetId()
    {
        $this->company = new Company();
        $this->company->setId(0);
        $this->expectException(Exception::class);
        $this->expectExceptionMessage("id não atribuido");
        $this->company->getId();
    }

    public function testSetInvalidUuid()
    {
        $this->company = new Company();
        $this->expectException(Exception::class);
        $this->expectExceptionMessage("uuid inválido");
        $this->company->setUuid("--");
    }

    public function testGetUuid()
    {
        $this->company = new Company();
        $this->company->setUuid(Uuid::uuid4());
        $this->assertIsString($this->company->getUuid());
    }

    public function testFindCompanyByUserId()
    {
        $this->customer = new Customer();
        $customerUuid = "1eed7357-6e74-6096-abf0-0242ac120003";
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

        $this->company = new Company();
        $this->company->id_user = $this->user->getId();
        $companyData = $this->company->findCompanyByUserId();
        $this->assertIsObject($companyData);
        $this->assertEquals("Cristiane e Kaique Padaria Ltda", $companyData->company_name);
        $this->customer->dropCustomerByUuid();
    }

    public function testFindAllCompanyByUserId()
    {
        $this->customer = new Customer();
        $customerUuid = "1eed7357-6e74-6096-abf0-0242ac120003";
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

        $this->company = new Company();
        $this->company->id_user = $this->user->getId();
        $companyData = $this->company->findAllCompanyByUserId();
        $this->assertIsArray($companyData);
        $this->assertEquals("Cristiane e Kaique Padaria Ltda", $companyData[0]->company_name);
        $this->assertIsInt($this->company->id_user);
        $this->customer->dropCustomerByUuid();
    }

    public function testFindAllCompanyByUserIdIsEmpty()
    {
        $this->company = new Company();
        $this->company->id_user = 0;
        $companyData = $this->company->findAllCompanyByUserId();
        $this->assertIsArray($companyData);
    }

    public function testFindCompanyByUser()
    {
        $this->customer = new Customer();
        $customerUuid = "1eed7357-6e74-6096-abf0-0242ac120003";
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

        $this->company = new Company();
        $this->company->id_user = $this->user->getId();
        $companyData = $this->company->findCompanyByUser();
        $this->assertIsArray($companyData);
        $this->assertEquals("Cristiane e Kaique Padaria Ltda", $companyData[0]->company_name);
        $this->assertIsInt($this->company->id_user);
        $this->customer->dropCustomerByUuid();
    }

    public function testFindCompanyByUserIsEmpty()
    {
        $this->company = new Company();
        $this->company->id_user = 0;
        $companyData = $this->company->findCompanyByUser();
        $this->assertIsArray($companyData);
    }

    public function testFindCompanyByUuid()
    {
        $this->customer = new Customer();
        $customerUuid = "1eed7357-6e74-6096-abf0-0242ac120003";
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
        $companyUuid = Uuid::uuid4();
        $this->company->persistData([
            "uuid" => $companyUuid,
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

        $this->company = new Company();
        $this->company->setUuid($companyUuid);
        $companyData = $this->company->findCompanyByUuid();
        $this->assertIsObject($companyData);
        $this->assertEquals("Cristiane e Kaique Padaria Ltda", $companyData->company_name);
        $this->customer->dropCustomerByUuid();
    }

    public function testUpdateCompanyByUuid()
    {
        $this->customer = new Customer();
        $customerUuid = "1eed7357-6e74-6096-abf0-0242ac120003";
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
        $companyUuid = Uuid::uuid4();
        $this->company->persistData([
            "uuid" => $companyUuid,
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

        $this->company = new Company();
        $response = $this->company->updateCompanyByUuid([
            "uuid" => $companyUuid,
            "company_state" => "MG"
        ]);

        $this->assertTrue($response);
        $this->company = new Company();
        $this->company->setUuid($companyUuid);
        $companyData = $this->company->findCompanyByUuid();
        $this->assertEquals("MG", $companyData->company_state);
        $this->customer->dropCustomerByUuid();
    }

    public function testFindCompanyById()
    {
        $this->customer = new Customer();
        $customerUuid = "1eed7357-6e74-6096-abf0-0242ac120003";
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
        $companyUuid = Uuid::uuid4();
        $this->company->persistData([
            "uuid" => $companyUuid,
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
        $companyId = $this->company->getId();

        $this->company = new Company();
        $this->company->setId($companyId);
        $companyData = $this->company->findCompanyById();
        $this->assertIsObject($companyData);
        $this->assertEquals("(14) 3858-1464", $companyData->company_phone);
        $this->customer->dropCustomerByUuid();
    }

    public function testFindAllCompanyByUserDeleted()
    {
        $this->customer = new Customer();
        $customerUuid = "1eed7357-6e74-6096-abf0-0242ac120003";
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
        $companyUuid = Uuid::uuid4();
        $this->company->persistData([
            "uuid" => $companyUuid,
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
            "deleted" => 1
        ]);

        $this->company = new Company();
        $this->company->id_user = $this->user->getId();
        $companyData = $this->company->findAllCompanyByUserDeleted([]);
        $this->assertIsArray($companyData);
        $this->assertEquals("Bauru", $companyData[0]->company_city);
        $this->customer->dropCustomerByUuid();
    }

    public function testDropCompanyByUuid()
    {
        $this->customer = new Customer();
        $customerUuid = "1eed7357-6e74-6096-abf0-0242ac120003";
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
        $companyUuid = Uuid::uuid4();
        $this->company->persistData([
            "uuid" => $companyUuid,
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

        $this->company = new Company();
        $this->company->setUuid($companyUuid);
        $response = $this->company->dropCompanyByUuid();
        $this->assertTrue($response);
        $this->customer->dropCustomerByUuid();
    }

    public function testFindAllCompanyByUserDeletedIsEmpty()
    {
        $this->company = new Company();
        $this->company->id_user = 0;
        $companyData = $this->company->findAllCompanyByUserDeleted([]);
        $this->assertIsArray($companyData);
    }
}
