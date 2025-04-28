<?php
namespace Source\Domain\Tests;

use Exception;
use PHPUnit\Framework\TestCase;
use Ramsey\Uuid\Nonstandard\Uuid;
use Source\Domain\Model\Customer;
use Source\Domain\Model\Subscription;
use Source\Models\Subscription as ModelsSubscription;

/**
 * SubscriptionTest Domain\Tests
 * @link 
 * @author Roberto Dorado <robertodorado7@gmail.com>
 * @package Source\Domain\Tests
 */
class SubscriptionTest extends TestCase
{
    /** @var Customer */
    private Customer $customer;

    /** @var Subscription */
    private Subscription $subscription;

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
        $this->subscription = new Subscription();

        $requestPost = [
            "uuid" => Uuid::uuid4(),
            "price_value" => 100.55,
            "subscription_type" => "premium",
            "subscription_id" => "sub_" . bin2hex(random_bytes(16)),
            "customer_id" => $this->customer,
            "charge_id" => "ch_" . bin2hex(random_bytes(16)),
            "product_description" => "sistema_contabil R$ 69,90 1x",
            "period_end" => date("Y-m-d", strtotime("+30 days", strtotime(date("Y-m-d")))),
            "period_start" => date("Y-m-d"),
            "created_at" => date("Y-m-d"),
            "updated_at" => date("Y-m-d"),
            "status" => "active"
        ];

        $response = $this->subscription->persistData($requestPost);
        $this->assertTrue($response);
        $this->customer->dropCustomerByUuid();
    }

    public function testInvalidPersistData()
    {
        $this->subscription = new Subscription();
        $response = $this->subscription->persistData([]);
        if (empty($response)) {
            $this->assertJsonStringEqualsJsonString(
                json_encode(["error" => "dados inválidos"]),
                $this->subscription->message->json()
            );
        }
    }

    public function testPersistDataInvalidUuid()
    {
        $this->customer = new Customer();
        $this->customer->setId(1425636);

        $this->subscription = new Subscription();
        $requestPost = [
            "uuid" => "-----",
            "subscription_type" => "premium",
            "subscription_id" => "sub_" . bin2hex(random_bytes(16)),
            "price_value" => 100.55,
            "customer_id" => $this->customer,
            "charge_id" => "ch_" . uniqid(),
            "product_description" => "sistema_contabil R$ 69,90 1x",
            "period_end" => date("Y-m-d", strtotime("+30 days", strtotime(date("Y-m-d")))),
            "period_start" => date("Y-m-d"),
            "created_at" => date("Y-m-d"),
            "updated_at" => date("Y-m-d"),
            "status" => "active"
        ];
        $this->expectException(Exception::class);
        $this->expectExceptionMessage("uuid inválido");
        $this->subscription->persistData($requestPost);
    }

    public function testInvalidCustomerInstance()
    {
        $this->subscription = new Subscription();
        $requestPost = [
            "uuid" => Uuid::uuid4(),
            "subscription_type" => "premium",
            "price_value" => 100.55,
            "subscription_id" => "sub_" . bin2hex(random_bytes(16)),
            "customer_id" => new Subscription(),
            "charge_id" => "ch_" . bin2hex(random_bytes(16)),
            "product_description" => "sistema_contabil R$ 69,90 1x",
            "period_end" => date("Y-m-d", strtotime("+30 days", strtotime(date("Y-m-d")))),
            "period_start" => date("Y-m-d"),
            "created_at" => date("Y-m-d"),
            "updated_at" => date("Y-m-d"),
            "status" => "active"
        ];
        $this->expectException(Exception::class);
        $this->expectExceptionMessage("instância do cliente está incorreta");
        $this->subscription->persistData($requestPost);
    }

    public function testInvalidSubscriptionIdOnPersistData()
    {
        $this->customer = new Customer();
        $this->customer->setId(1425636);

        $this->subscription = new Subscription();
        $requestPost = [
            "uuid" => Uuid::uuid4(),
            "subscription_type" => "premium",
            "price_value" => 100.55,
            "subscription_id" => "test_" . bin2hex(random_bytes(16)),
            "customer_id" => $this->customer,
            "charge_id" => "ch_" . bin2hex(random_bytes(16)),
            "product_description" => "sistema_contabil R$ 69,90 1x",
            "period_end" => date("Y-m-d", strtotime("+30 days", strtotime(date("Y-m-d")))),
            "period_start" => date("Y-m-d"),
            "created_at" => date("Y-m-d"),
            "updated_at" => date("Y-m-d"),
            "status" => "active"
        ];
        $this->expectException(Exception::class);
        $this->expectExceptionMessage("subscription_id inválido");
        $this->subscription->persistData($requestPost);
    }

    public function testIvalidChargeIdOnPersistData()
    {
        $this->customer = new Customer();
        $this->customer->setId(1425636);

        $this->subscription = new Subscription();
        $requestPost = [
            "uuid" => Uuid::uuid4(),
            "subscription_type" => "premium",
            "price_value" => 100.55,
            "subscription_id" => "sub_" . bin2hex(random_bytes(16)),
            "customer_id" => $this->customer,
            "charge_id" => "test_" . bin2hex(random_bytes(16)),
            "product_description" => "sistema_contabil R$ 69,90 1x",
            "period_end" => date("Y-m-d", strtotime("+30 days", strtotime(date("Y-m-d")))),
            "period_start" => date("Y-m-d"),
            "created_at" => date("Y-m-d"),
            "updated_at" => date("Y-m-d"),
            "status" => "active"
        ];
        $this->expectException(Exception::class);
        $this->expectExceptionMessage("charge_id inválido");
        $this->subscription->persistData($requestPost);
    }

    public function testInvalidGetId()
    {
        $this->subscription = new Subscription();
        $this->subscription->setId(0);
        $this->expectException(Exception::class);
        $this->expectExceptionMessage("id não atribuido");
        $this->subscription->getId();
    }

    public function testGetId()
    {
        $this->subscription = new Subscription();
        $this->subscription->setId(100);
        $response = $this->subscription->getId();
        $this->assertIsInt($response);
        $this->assertEquals(100, $response);
    }

    public function testSetter()
    {
        $this->subscription = new Subscription();
        $this->subscription->name = "roberto";
        $this->assertIsString($this->subscription->name);
        $this->assertEquals("roberto", $this->subscription->name);
    }

    public function testFindSubsCriptionByCustomerId()
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
        $this->subscription = new Subscription();

        $requestPost = [
            "uuid" => Uuid::uuid4(),
            "price_value" => 100.55,
            "subscription_type" => "premium",
            "subscription_id" => "sub_" . bin2hex(random_bytes(16)),
            "customer_id" => $this->customer,
            "charge_id" => "ch_" . bin2hex(random_bytes(16)),
            "product_description" => "sistema_contabil R$ 69,90 1x",
            "period_end" => date("Y-m-d", strtotime("+30 days", strtotime(date("Y-m-d")))),
            "period_start" => date("Y-m-d"),
            "created_at" => date("Y-m-d"),
            "updated_at" => date("Y-m-d"),
            "status" => "active"
        ];

        $this->subscription->persistData($requestPost);
        $this->subscription = new Subscription();
        $this->customer->setUuid($customerUuid);
        
        $this->subscription->customer_id = $this->customer->getId();
        $response = $this->subscription->findSubsCriptionByCustomerId([]);
        
        $this->assertInstanceOf(ModelsSubscription::class, $response);
        $this->customer->dropCustomerByUuid();
    }

    public function testFindSubsCriptionByCustomerIdIsEmpty()
    {
        $this->customer = new Customer();
        $this->customer->setId(52585);
        $this->subscription = new Subscription();
        $response = $this->subscription->findSubsCriptionByCustomerId([]);
        if (empty($response)) {
            $this->assertJsonStringEqualsJsonString(
                json_encode(["error" => "assinatura não encontrada"]),
                $this->subscription->message->json()
            );
        }
    }

    public function testFindSubscriptionBySubscriptionId()
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
        $this->subscription = new Subscription();

        $requestPost = [
            "uuid" => Uuid::uuid4(),
            "price_value" => 100.55,
            "subscription_type" => "premium",
            "subscription_id" => "sub_123456",
            "customer_id" => $this->customer,
            "charge_id" => "ch_" . bin2hex(random_bytes(16)),
            "product_description" => "sistema_contabil R$ 69,90 1x",
            "period_end" => date("Y-m-d", strtotime("+30 days", strtotime(date("Y-m-d")))),
            "period_start" => date("Y-m-d"),
            "created_at" => date("Y-m-d"),
            "updated_at" => date("Y-m-d"),
            "status" => "active"
        ];

        $response = $this->subscription->persistData($requestPost);
        $this->subscription = new Subscription();
        
        $this->subscription->subscription_id = "sub_123456";
        $response = $this->subscription->findSubsCriptionBySubscriptionId([]);
        $this->assertInstanceOf(ModelsSubscription::class, $response);
        $this->customer->dropCustomerByUuid();
    }

    public function testUpdateSubscriptionBySubscriptionId()
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
        $customer = new Customer();
        
        $customer->email = $requestPost["customer_email"];
        $customerData = $customer->findCustomerByEmail();
        
        $this->subscription = new Subscription();
        $requestPost = [
            "uuid" => Uuid::uuid4(),
            "price_value" => 100.55,
            "subscription_type" => "premium",
            "subscription_id" => "sub_" . bin2hex(random_bytes(16)),
            "customer_id" => $this->customer,
            "charge_id" => "ch_" . bin2hex(random_bytes(16)),
            "product_description" => "sistema_contabil R$ 69,90 1x",
            "period_end" => date("Y-m-d", strtotime("+30 days", strtotime(date("Y-m-d")))),
            "period_start" => date("Y-m-d"),
            "created_at" => date("Y-m-d"),
            "updated_at" => date("Y-m-d"),
            "status" => "active"
        ];

        $this->subscription->persistData($requestPost);
        $this->subscription = new Subscription();
        
        $requestPost["product_description"] = "teste_123";
        $response =  $this->subscription->updateSubscriptionBySubscriptionId($requestPost);
        $this->assertTrue($response);

        $this->subscription = new Subscription();
        $this->subscription->customer_id = $customerData->id;
        
        $response = $this->subscription->findSubsCriptionByCustomerId([]);
        $this->assertEquals("teste_123", $response->product_description);
        $this->customer = new Customer();
        
        $this->customer->setUuid($customerUuid);
        $this->customer->dropCustomerByUuid();
    }

    public function testSubscriptionIdNotFound()
    {
        $this->subscription = new Subscription();
        $this->subscription->subscription_id = "teste";
        
        $response = $this->subscription->findSubsCriptionBySubscriptionId([]);
        $this->assertNull($response);
        
        $this->assertJsonStringEqualsJsonString(json_encode(["error" => "assinatura não encontrada"]),
        $this->subscription->message->json());
    }

    public function testFindSubscriptionByChargeId()
    {
        $this->customer = new Customer();
        $customerUuid = Uuid::uuid4();
        $chargeId = "ch_" . bin2hex(random_bytes(16));

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
        $this->subscription = new Subscription();

        $requestPost = [
            "uuid" => Uuid::uuid4(),
            "price_value" => 100.55,
            "subscription_type" => "premium",
            "subscription_id" => "sub_" . bin2hex(random_bytes(16)),
            "customer_id" => $this->customer,
            "charge_id" => $chargeId,
            "product_description" => "sistema_contabil R$ 69,90 1x",
            "period_end" => date("Y-m-d", strtotime("+30 days", strtotime(date("Y-m-d")))),
            "period_start" => date("Y-m-d"),
            "created_at" => date("Y-m-d"),
            "updated_at" => date("Y-m-d"),
            "status" => "active"
        ];

        $this->subscription->persistData($requestPost);
        $this->subscription = new Subscription();

        $this->subscription->charge_id = $chargeId;
        $responseData = $this->subscription->findSubscriptionByChargeId(["id"]);

        $this->assertNotEmpty($responseData);
        $this->customer = new Customer();
        $this->customer->setUuid($customerUuid);
        $this->customer->dropCustomerByUuid();
    }

    public function testUpdateSubscriptionByChargeId()
    {
        $this->customer = new Customer();
        $customerUuid = Uuid::uuid4();
        $chargeId = "ch_" . bin2hex(random_bytes(16));

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
        $this->subscription = new Subscription();

        $requestPost = [
            "uuid" => Uuid::uuid4(),
            "price_value" => 100.55,
            "subscription_type" => "premium",
            "subscription_id" => "sub_" . bin2hex(random_bytes(16)),
            "customer_id" => $this->customer,
            "charge_id" => $chargeId,
            "product_description" => "sistema_contabil R$ 69,90 1x",
            "period_end" => date("Y-m-d", strtotime("+30 days", strtotime(date("Y-m-d")))),
            "period_start" => date("Y-m-d"),
            "created_at" => date("Y-m-d"),
            "updated_at" => date("Y-m-d"),
            "status" => "active"
        ];

        $this->subscription->persistData($requestPost);
        $this->subscription = new Subscription();
        $this->subscription->updateSubscriptionByChargeId([
            'charge_id' => $chargeId,
            "product_description" => "sistema_financeiro R$ 69,90 1x",
        ]);

        $this->subscription = new Subscription();
        $this->subscription->charge_id = $chargeId;
        $responseData = $this->subscription->findSubscriptionByChargeId(["product_description"]);
        $this->assertEquals("sistema_financeiro R$ 69,90 1x", $responseData->product_description);

        $this->customer = new Customer();
        $this->customer->setUuid($customerUuid);
        $this->customer->dropCustomerByUuid();
    }

    public function testFindAllSubscriptiosByCustomerId()
    {
        $this->customer = new Customer();
        $customerUuid = Uuid::uuid4();
        $chargeId = "ch_" . bin2hex(random_bytes(16));

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
        $this->customer->setUuid($customerUuid);
        $customerData = $this->customer->findCustomerByUuid(["id"]);

        $this->subscription = new Subscription();
        $this->subscription->customer_id = $customerData->id;
        $responseData = $this->subscription->findAllSubscriptiosByCustomerId(["id"]);
        $this->assertEquals([], $responseData);

        $this->subscription = new Subscription();
        $requestPost = [
            "uuid" => Uuid::uuid4(),
            "price_value" => 100.55,
            "subscription_type" => "premium",
            "subscription_id" => "sub_" . bin2hex(random_bytes(16)),
            "customer_id" => $this->customer,
            "charge_id" => $chargeId,
            "product_description" => "sistema_contabil R$ 69,90 1x",
            "period_end" => date("Y-m-d", strtotime("+30 days", strtotime(date("Y-m-d")))),
            "period_start" => date("Y-m-d"),
            "created_at" => date("Y-m-d"),
            "updated_at" => date("Y-m-d"),
            "status" => "active"
        ];

        $this->subscription->persistData($requestPost);
        $this->subscription = new Subscription();
        $this->subscription->customer_id = $customerData->id;
        $responseData = $this->subscription->findAllSubscriptiosByCustomerId(["id"]);
        $this->assertIsArray($responseData);

        $this->customer = new Customer();
        $this->customer->setUuid($customerUuid);
        $this->customer->dropCustomerByUuid();
    }
}
