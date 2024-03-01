<?php
namespace Source\Domain\Tests;

use Exception;
use PHPUnit\Framework\TestCase;
use Ramsey\Uuid\Uuid;
use Source\Domain\Model\CashFlow;
use Source\Domain\Model\User;

/**
 * CashFlowTest Domain\Tests
 * @link 
 * @author Roberto Dorado <robertodorado7@gmail.com>
 * @package Source\Domain\Tests
 */
class CashFlowTest extends TestCase
{
    /** @var CashFlow Modelo de dominio fluxo de caixa */
    private CashFlow $cashFlow;

    /** @var User Modelo de dominio usuário */
    private User $user;

    public function testPersistDataColumnException()
    {
        $this->cashFlow = new CashFlow();
        $this->user = new User();
        $cashFlowData = [
            "uuid" => Uuid::uuid6(),
            "id_user" => $this->user,
            "entry" => 100.55,
            "history" => "venda realizada no dia" . date("d/m/Y"),
            "entry_typee" => 1,
            "created_at" => date("Y-m-d"),
            "updated_at" => date("Y-m-d"),
            "deleted" => 0
        ];

        $this->expectException(Exception::class);
        $this->expectExceptionMessage("esta propriedade entry_type foi passado de maneira incorreta");
        $this->cashFlow->persistData($cashFlowData);
    }

    public function testEntryPersistDataException()
    {
        $this->cashFlow = new CashFlow();
        $this->user = new User();
        $cashFlowData = [
            "uuid" => Uuid::uuid6(),
            "id_user" => $this->user,
            "entry" => "---",
            "history" => "venda realizada no dia" . date("d/m/Y"),
            "entry_type" => 1,
            "created_at" => date("Y-m-d"),
            "updated_at" => date("Y-m-d"),
            "deleted" => 0
        ];

        $this->expectException(Exception::class);
        $this->expectExceptionMessage("valor de entrada inválido");
        $this->cashFlow->persistData($cashFlowData);
    }

    public function testInvalidInstance()
    {
        $this->cashFlow = new CashFlow();
        $cashFlowData = [
            "uuid" => Uuid::uuid6(),
            "id_user" => $this->cashFlow,
            "entry" => "445.77",
            "history" => "venda realizada no dia" . date("d/m/Y"),
            "entry_type" => 1,
            "created_at" => date("Y-m-d"),
            "updated_at" => date("Y-m-d"),
            "deleted" => 0
        ];

        $this->expectException(Exception::class);
        $this->expectExceptionMessage("Instância inválida ao persistir o dado");
        $this->cashFlow->persistData($cashFlowData);
    }

    public function testInvalidPersistData()
    {
        $this->cashFlow = new CashFlow();
        $response = $this->cashFlow->persistData([]);
        $this->assertJsonStringEqualsJsonString(json_encode(["invalid_persist_data" => "dados inválidos"]),
        $response);
    }

    public function testPersistDataIsTrue()
    {
        $this->cashFlow = new CashFlow();
        $this->user = new User();
        $userData = [
            "uuid" => Uuid::uuid6(),
            "user_full_name" => "teste fulano de tal 2",
            "user_nick_name" => "fulanoDeTal2",
            "user_email" => "testefulano2@gmail.com",
            "user_password" => password_hash("minhasenha1234", PASSWORD_DEFAULT),
            "created_at" => date("Y-m-d"),
            "updated_at" => date("Y-m-d"),
            "deleted" => 0
        ];
        $this->user->persistData($userData);
        $this->user = new User();
        
        $userData = $this->user->findUserByEmail($userData["user_email"]);
        $this->user->setId($userData->id);

        $cashFlowData = [
            "uuid" => Uuid::uuid6(),
            "id_user" => $this->user,
            "entry" => "1.750,45",
            "history" => "venda realizada no dia " . date("d/m/Y"),
            "entry_type" => 1,
            "created_at" => date("Y-m-d"),
            "updated_at" => date("Y-m-d"),
            "deleted" => 0
        ];

        $response = $this->cashFlow->persistData($cashFlowData);
        $this->assertTrue($response, "erro ao persistir o registro do fluxo de caixa");
        $this->user = new User();
        $this->cashFlow->dropCashFlowById($this->cashFlow->getId());
        $this->user->dropUserById($userData->id);
    }

    public function testCalculateBalancePositive()
    {
        $cashFlowIds = [];
        $this->user = new User();

        $userData = [
            "uuid" => Uuid::uuid6(),
            "user_full_name" => "teste fulano de tal 2",
            "user_nick_name" => "fulanoDeTal2",
            "user_email" => "testefulano2@gmail.com",
            "user_password" => password_hash("minhasenha1234", PASSWORD_DEFAULT),
            "created_at" => date("Y-m-d"),
            "updated_at" => date("Y-m-d"),
            "deleted" => 0
        ];

        $this->user->persistData($userData);
        $userId = $this->user->getId();

        for ($i = 0; $i < 3; $i++) {
            $this->cashFlow = new CashFlow();

            $cashFlowData = [
                "uuid" => Uuid::uuid6(),
                "id_user" => $this->user,
                "entry" => "1.750,45",
                "history" => "venda realizada no dia " . date("d/m/Y"),
                "entry_type" => 1,
                "created_at" => date("Y-m-d"),
                "updated_at" => date("Y-m-d"),
                "deleted" => 0
            ];
            $this->cashFlow->persistData($cashFlowData);
            array_push($cashFlowIds, $this->cashFlow->getId());
        }

        $balance = $this->cashFlow->calculateBalance($this->user);
        $this->assertEquals(5251.35, $balance);

        if (!empty($cashFlowIds)) {
            for ($i = 0; $i < count($cashFlowIds); $i++) {
                $this->cashFlow = new CashFlow();
                $this->cashFlow->dropCashFlowById($cashFlowIds[$i]);
            }
        }

        $this->user = new User();
        $this->user->dropUserById($userId);
    }

    public function testCalculateBalanceNegative()
    {
        $cashFlowIds = [];
        $this->user = new User();

        $userData = [
            "uuid" => Uuid::uuid6(),
            "user_full_name" => "teste fulano de tal 2",
            "user_nick_name" => "fulanoDeTal2",
            "user_email" => "testefulano2@gmail.com",
            "user_password" => password_hash("minhasenha1234", PASSWORD_DEFAULT),
            "created_at" => date("Y-m-d"),
            "updated_at" => date("Y-m-d"),
            "deleted" => 0
        ];

        $this->user->persistData($userData);
        $userId = $this->user->getId();

        for ($i = 0; $i < 3; $i++) {
            $this->cashFlow = new CashFlow();

            $cashFlowData = [
                "uuid" => Uuid::uuid6(),
                "id_user" => $this->user,
                "entry" => "1.750,45",
                "history" => "venda realizada no dia " . date("d/m/Y"),
                "entry_type" => 0,
                "created_at" => date("Y-m-d"),
                "updated_at" => date("Y-m-d"),
                "deleted" => 0
            ];
            $this->cashFlow->persistData($cashFlowData);
            array_push($cashFlowIds, $this->cashFlow->getId());
        }

        $balance = $this->cashFlow->calculateBalance($this->user);
        $this->assertEquals((5251.35 * -1), $balance);

        if (!empty($cashFlowIds)) {
            for ($i = 0; $i < count($cashFlowIds); $i++) {
                $this->cashFlow = new CashFlow();
                $this->cashFlow->dropCashFlowById($cashFlowIds[$i]);
            }
        }

        $this->user = new User();
        $this->user->dropUserById($userId);
    }

    public function testFindCashFlowByIdNotFound()
    {
        $this->cashFlow = new CashFlow();
        $this->cashFlow->setId(100000000000000000);
        $response = $this->cashFlow->findCashFlowById();
        $this->assertNull($response);
    }

    public function testFindCashFlowById()
    {
        $this->cashFlow = new CashFlow();
        $this->user = new User();
        $userData = [
            "uuid" => Uuid::uuid6(),
            "user_full_name" => "teste fulano de tal 2",
            "user_nick_name" => "fulanoDeTal2",
            "user_email" => "testefulano2@gmail.com",
            "user_password" => password_hash("minhasenha1234", PASSWORD_DEFAULT),
            "deleted" => 0
        ];
        $this->user->persistData($userData);
        
        $cashFlowData = [
            "uuid" => Uuid::uuid6(),
            "id_user" => $this->user,
            "entry" => "1.750,45",
            "history" => "venda realizada no dia " . date("d/m/Y"),
            "entry_type" => 1,
            "created_at" => date("2024-01-14"),
            "updated_at" => date("2024-01-14"),
            "deleted" => 0
        ];

        $this->cashFlow->persistData($cashFlowData);
        $this->cashFlow->setId($this->cashFlow->getId());
       
        $response = $this->cashFlow->findCashFlowById();
        $this->assertIsObject($response, 'erro ao retornar cashflow pelo id');
       
        $this->cashFlow = new CashFlow();
        $this->cashFlow->dropCashFlowById($response->id);
        $this->user = new User();
        $this->user->dropUserByEmail($userData["user_email"]);
    }

    public function testGetIdIsEmpty()
    {
        $this->cashFlow = new CashFlow();
        $this->cashFlow->setId(0);
        $this->expectException(Exception::class);
        $this->expectExceptionMessage("id não atribuido");
        $this->cashFlow->getId();
    }

    public function testGetIdValue()
    {
        $this->cashFlow = new CashFlow();
        $this->cashFlow->setId(10);
        $this->assertEquals(10, $this->cashFlow->getId());
    }

    public function testFindCashFlowByUserIsEmpty()
    {
        $this->user = new User();
        $this->user->setId(1000000);
        $this->cashFlow = new CashFlow();
        $response = $this->cashFlow->findCashFlowByUser([], $this->user);
        $this->assertJsonStringEqualsJsonString(json_encode(
            ["cash_flow_empty" => "nenhum registro foi encontrado"]),
        $response);
    }

    public function testFindCashFlowByUser()
    {
        $this->user = new User();
        $userData = [
            "uuid" => Uuid::uuid6(),
            "user_full_name" => "teste fulano de tal 2",
            "user_nick_name" => "fulanoDeTal2",
            "user_email" => "testefulano2@gmail.com",
            "user_password" => password_hash("minhasenha1234", PASSWORD_DEFAULT),
            "deleted" => 0
        ];
        
        $this->user->persistData($userData);
        $userId = $this->user->getId();

        $this->cashFlow = new CashFlow();
        $cashFlowData = [
            "uuid" => Uuid::uuid6(),
            "id_user" => $this->user,
            "entry" => "1.750,45",
            "history" => "venda realizada no dia " . date("d/m/Y"),
            "entry_type" => 1,
            "created_at" => date("Y-m-d"),
            "updated_at" => date("Y-m-d"),
            "deleted" => 0
        ];
        
        $this->cashFlow->persistData($cashFlowData);
        $cashFlowId = $this->cashFlow->getId();

        $this->cashFlow = new CashFlow();
        $this->assertIsArray($this->cashFlow->findCashFlowByUser([], $this->user));

        $this->cashFlow = new CashFlow();
        $this->cashFlow->dropCashFlowById($cashFlowId);
        
        $this->user = new User();
        $this->user->dropUserById($userId);
    }

    public function testFindCashFlowByUuid()
    {
        $this->user = new User();
        $userData = [
            "uuid" => Uuid::uuid6(),
            "user_full_name" => "teste fulano de tal 2",
            "user_nick_name" => "fulanoDeTal2",
            "user_email" => "testefulano2@gmail.com",
            "user_password" => password_hash("minhasenha1234", PASSWORD_DEFAULT),
            "deleted" => 0
        ];
        
        $this->user->persistData($userData);
        $userId = $this->user->getId();

        $this->cashFlow = new CashFlow();
        $cashFlowData = [
            "uuid" => "1eed7357-6e74-6096-abf0-0242ac120003",
            "id_user" => $this->user,
            "entry" => "1.750,45",
            "history" => "venda realizada no dia " . date("d/m/Y"),
            "entry_type" => 1,
            "created_at" => date("Y-m-d"),
            "updated_at" => date("Y-m-d"),
            "deleted" => 0
        ];
        
        $this->cashFlow->persistData($cashFlowData);
        $uuid = $cashFlowData["uuid"];

        $this->cashFlow = new CashFlow();
        $response = $this->cashFlow->findCashFlowByUuid($uuid);
        $this->assertIsObject($response);
        
        $this->cashFlow = new CashFlow();
        $this->cashFlow->dropCashFlowByUuid($uuid);
        
        $this->user = new User();
        $this->user->dropUserById($userId);
    }

    public function testFindCashFlowByUuidNotFound()
    {
        $this->cashFlow = new CashFlow();
        $response = $this->cashFlow->findCashFlowByUuid("1eed7357-6e74-6096-abf0-0242ac120003");
        $this->assertJsonStringEqualsJsonString($response, 
            json_encode(["empty_cash_flow" => "o registro fluxo de caixa não existe"]));
    }

    public function testUpdateCashFlowByUuid()
    {
        $this->user = new User();
        $userData = [
            "uuid" => Uuid::uuid6(),
            "user_full_name" => "teste fulano de tal 2",
            "user_nick_name" => "fulanoDeTal2",
            "user_email" => "testefulano2@gmail.com",
            "user_password" => password_hash("minhasenha1234", PASSWORD_DEFAULT),
            "deleted" => 0
        ];
        
        $this->user->persistData($userData);
        $userId = $this->user->getId();

        $this->cashFlow = new CashFlow();
        $cashFlowData = [
            "uuid" => Uuid::uuid6(),
            "id_user" => $this->user,
            "entry" => "1.750,45",
            "history" => "venda realizada no dia " . date("d/m/Y"),
            "entry_type" => 0,
            "created_at" => date("Y-m-d"),
            "updated_at" => date("Y-m-d"),
            "deleted" => 0
        ];
        $this->cashFlow->persistData($cashFlowData);

        $this->cashFlow = new CashFlow();
        $cashFlowData['deleted'] = 1;
        
        $response = $this->cashFlow->updateCashFlowByUuid($cashFlowData);
        $this->assertTrue($response);
        
        $this->user = new User();
        $this->user->dropUserById($userId);
    }

    public function testUpdateCashFlowByUuidInstanceUserError()
    {
        $this->user = new User();
        $userData = [
            "uuid" => "1eed7357-6e74-6096-abf0-0242ac120003",
            "user_full_name" => "teste fulano de tal 2",
            "user_nick_name" => "fulanoDeTal2",
            "user_email" => "testefulano2@gmail.com",
            "user_password" => password_hash("minhasenha1234", PASSWORD_DEFAULT),
            "deleted" => 0
        ];
        
        $this->user->persistData($userData);

        $this->cashFlow = new CashFlow();
        $cashFlowData = [
            "uuid" => "1eed7357-6e74-6096-abf0-0242ac120003",
            "id_user" => $this->user,
            "entry" => "1.750,45",
            "history" => "venda realizada no dia " . date("d/m/Y"),
            "entry_type" => 0,
            "created_at" => date("Y-m-d"),
            "updated_at" => date("Y-m-d"),
            "deleted" => 0
        ];
        $this->cashFlow->persistData($cashFlowData);

        $this->expectException(Exception::class);
        $this->expectExceptionMessage("Instância inválida ao atualizar o dado");

        $this->cashFlow = new CashFlow();
        $cashFlowData['id_user'] = $this->cashFlow;
        $this->cashFlow->updateCashFlowByUuid($cashFlowData);
    }

    public function testDropCashFlowCascadeByUuid()
    {
        $this->user = new User();
        $response = $this->user->dropUserByUuid("1eed7357-6e74-6096-abf0-0242ac120003");
        $this->assertNull($response);
    }

    public function testUpdateCashFlowByUuidEmptyCashFlow()
    {
        $this->cashFlow = new CashFlow();
        $response = $this->cashFlow->updateCashFlowByUuid([]);
        $this->assertJsonStringEqualsJsonString(
            $response, json_encode(["data_is_empty" => "data não pode ser vazio"]));
    }

    public function testUpdateCashFlowByUuidEmptyData()
    {
        $this->cashFlow = new CashFlow();
        $cashFlowData = [
            "uuid" => Uuid::uuid6(),
            "id_user" => new User(),
            "entry" => "1.750,45",
            "history" => "venda realizada no dia " . date("d/m/Y"),
            "entry_type" => 0,
            "created_at" => date("Y-m-d"),
            "updated_at" => date("Y-m-d"),
            "deleted" => 0
        ];
        $response = $this->cashFlow->updateCashFlowByUuid($cashFlowData);
        $this->assertJsonStringEqualsJsonString($response,
            json_encode(["cash_flow_data_not_found" => "registro de fluxo de caixa não encontrado"]));
    }
}
