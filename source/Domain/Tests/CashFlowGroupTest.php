<?php

namespace Source\Domain\Tests;

use Exception;
use PHPUnit\Framework\TestCase;
use Ramsey\Uuid\Nonstandard\Uuid;
use Source\Domain\Model\CashFlowGroup;
use Source\Domain\Model\User;

use function PHPUnit\Framework\assertJsonStringEqualsJsonString;

/**
 * CashFlowGroupTest Tests
 * @link 
 * @author Roberto Dorado <robertodorado7@gmail.com>
 * @package Domain\Tests
 */
class CashFlowGroupTest extends TestCase
{
    /** @var CashFlowGroup Modelo de domínio a ser testado */
    private CashFlowGroup $cashFlowGroup;

    /** @var User Modelo de domínio do usuário */
    private User $user;

    public function testPersistData()
    {
        $this->cashFlowGroup = new CashFlowGroup();
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
        $userId = $this->user->getId();

        $data = [
            "uuid" => "1eed7357-6e74-6096-abf0-0242ac120003",
            "id_user" => $this->user,
            "group_name" => "novo grupo",
            "created_at" => date("Y-m-d"),
            "updated_at" => date("Y-m-d"),
            "deleted" => 0
        ];
        $response = $this->cashFlowGroup->persistData($data);
        $this->assertTrue($response);
        $this->user = new User();
        $this->user->dropUserById($userId);
    }

    public function testInvalidDataOnPersistData()
    {
        $this->cashFlowGroup = new CashFlowGroup();
        $response = $this->cashFlowGroup->persistData([]);
        $this->assertJsonStringEqualsJsonString(
            json_encode(["error" => "dados inválidos"]),
            $response
        );
    }

    public function testGetEmptyId()
    {
        $this->cashFlowGroup = new CashFlowGroup();
        $this->expectException(Exception::class);
        $this->expectExceptionMessage("Id não atribuido");
        $this->cashFlowGroup->getId();
    }

    public function testUpdateCashFlowGroupByUuid()
    {
        $this->cashFlowGroup = new CashFlowGroup();
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
        $userId = $this->user->getId();
        $cashFlowGroupUuid = uuid::uuid6();

        $cashFlowGroupData = [
            "uuid" => $cashFlowGroupUuid,
            "id_user" => $this->user,
            "group_name" => "novo grupo",
            "created_at" => date("Y-m-d"),
            "updated_at" => date("Y-m-d"),
            "deleted" => 0
        ];

        $this->cashFlowGroup->persistData($cashFlowGroupData);
        $this->cashFlowGroup = new CashFlowGroup();

        $cashFlowGroupData["group_name"] = "novo grupo 2";
        $this->cashFlowGroup->updateCashFlowGroupByUuid($cashFlowGroupData);

        $this->cashFlowGroup = new CashFlowGroup();
        $cashFlowGroupData = $this->cashFlowGroup->findCashFlowGroupByUuid($cashFlowGroupUuid);

        $this->assertEquals("novo grupo 2", $cashFlowGroupData->group_name);
        $this->user = new User();
        $this->user->dropUserById($userId);
    }

    public function testUpdateCashFlowGroupIsEmpty()
    {
        $this->cashFlowGroup = new CashFlowGroup();
        $response = $this->cashFlowGroup->updateCashFlowGroupByUuid([]);
        $this->assertJsonStringEqualsJsonString(
            json_encode(["error" => "data não pode ser vazio"]),
            $response
        );
    }

    public function testUpdateCashFlowGroupByUuidNotFound()
    {
        $this->cashFlowGroup = new CashFlowGroup();
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
        $userId = $this->user->getId();
        $cashFlowGroupUuid = uuid::uuid6();

        $cashFlowGroupData = [
            "uuid" => $cashFlowGroupUuid,
            "id_user" => $this->user,
            "group_name" => "novo grupo",
            "created_at" => date("Y-m-d"),
            "updated_at" => date("Y-m-d"),
            "deleted" => 0
        ];

        $this->cashFlowGroup->persistData($cashFlowGroupData);
        $cashFlowGroupData["uuid"] = $cashFlowGroupUuid . "3";
        $response = $this->cashFlowGroup->updateCashFlowGroupByUuid($cashFlowGroupData);

        $this->assertJsonStringEqualsJsonString(
            json_encode(["error" => "grupo fluxo de caixa não encontrado"]),
            $response
        );

        $this->user = new User();
        $this->user->dropUserById($userId);
    }

    public function testDropCashFlowGroupByUuidEmptyParameter()
    {
        $this->cashFlowGroup = new CashFlowGroup();
        $response = $this->cashFlowGroup->dropCashFlowGroupByUuid("");
        $this->assertJsonStringEqualsJsonString(
            json_encode(["error" => "uuid não pode estar vazio"]),
            $response
        );
    }

    public function testDropCashFlowGroupByUuidIsEmpty()
    {
        $this->cashFlowGroup = new CashFlowGroup();
        $response = $this->cashFlowGroup->dropCashFlowGroupByUuid(Uuid::uuid6());
        $this->assertJsonStringEqualsJsonString(
            json_encode(["error" => "o registro não existe"]),
            $response
        );
    }

    public function testDropCashFlowGroupByIdEmptyParameter()
    {
        $this->cashFlowGroup = new CashFlowGroup();
        $response = $this->cashFlowGroup->dropCashFlowGroupById(0);
        $this->assertJsonStringEqualsJsonString(
            json_encode(["error" => "id não pode estar vazio"]),
            $response
        );
    }

    public function testFindCashFlowGroupByUuidInvalidParameter()
    {
        $this->cashFlowGroup = new CashFlowGroup();
        $response = $this->cashFlowGroup->findCashFlowGroupByUuid("");
        $this->assertJsonStringEqualsJsonString(
            json_encode(["error" => "uuid não pode estar vazio"]),
            $response
        );
    }

    public function testFindCashFlowGroupByUuidIsEmpty()
    {
        $this->cashFlowGroup = new CashFlowGroup();
        $response = $this->cashFlowGroup->findCashFlowGroupByUuid(Uuid::uuid6());
        $this->assertJsonStringEqualsJsonString(
            json_encode(["error" => "registro não encontrado"]),
            $response
        );
    }

    public function testPersistDataInstanceUserError()
    {
        $this->cashFlowGroup = new CashFlowGroup();
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
        $cashFlowGroupData = [
            "uuid" => Uuid::uuid6(),
            "id_user" => $this->cashFlowGroup,
            "group_name" => "novo grupo",
            "created_at" => date("Y-m-d"),
            "updated_at" => date("Y-m-d"),
            "deleted" => 0
        ];

        $this->expectException(Exception::class);
        $this->expectExceptionMessage("Instância inválida ao persistir o dado");
        $this->cashFlowGroup->persistData($cashFlowGroupData);
    }

    public function testClearDataPersistCashFlowGroup()
    {
        $this->user = new User();
        $response = $this->user->dropUserByUuid("1eed7357-6e74-6096-abf0-0242ac120003");
        $this->assertNull($response);
    }

    public function testUpdateByUuidInstanceUserError()
    {
        $this->cashFlowGroup = new CashFlowGroup();
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
        $cashFlowGroupData = [
            "uuid" => Uuid::uuid6(),
            "id_user" => $this->user,
            "group_name" => "novo grupo",
            "created_at" => date("Y-m-d"),
            "updated_at" => date("Y-m-d"),
            "deleted" => 0
        ];

        $this->cashFlowGroup->persistData($cashFlowGroupData);
        $this->expectException(Exception::class);
        $this->expectExceptionMessage("Instância inválida ao atualizar o dado");

        $this->cashFlowGroup = new CashFlowGroup();
        $cashFlowGroupData["id_user"] = $this->cashFlowGroup;
        $this->cashFlowGroup->updateCashFlowGroupByUuid($cashFlowGroupData);
    }

    public function testClearDataUpdateByUuid()
    {
        $this->user = new User();
        $response = $this->user->dropUserByUuid("1eed7357-6e74-6096-abf0-0242ac120003");
        $this->assertNull($response);
    }

    public function testDropCashFlowDataById()
    {
        $this->cashFlowGroup = new CashFlowGroup();
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
        $userId = $this->user->getId();

        $cashFlowGroupData = [
            "uuid" => Uuid::uuid6(),
            "id_user" => $this->user,
            "group_name" => "novo grupo",
            "created_at" => date("Y-m-d"),
            "updated_at" => date("Y-m-d"),
            "deleted" => 0
        ];

        $this->cashFlowGroup->persistData($cashFlowGroupData);
        $cashFlowGroupId = $this->cashFlowGroup->getId();

        $response = $this->cashFlowGroup->dropCashFlowGroupById($cashFlowGroupId);
        $this->assertNull($response);

        $this->user = new User();
        $this->user->dropUserById($userId);
    }

    public function testFindCashFlowGroupByUser()
    {
        $this->cashFlowGroup = new CashFlowGroup();
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
        $userId = $this->user->getId();

        $cashFlowGroupData = [
            "uuid" => Uuid::uuid6(),
            "id_user" => $this->user,
            "group_name" => "novo grupo",
            "created_at" => date("Y-m-d"),
            "updated_at" => date("Y-m-d"),
            "deleted" => 0
        ];

        $this->cashFlowGroup->persistData($cashFlowGroupData);
        $cashFlowGroupData = $this->cashFlowGroup->findCashFlowGroupByUser([], $this->user);
        $this->assertIsArray($cashFlowGroupData);
        
        $this->user = new User();
        $this->user->dropUserById($userId);
    }

    public function testFindCashFlowGroupByUserIsNotEmpty()
    {
        $this->cashFlowGroup = new CashFlowGroup();
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
        $userId = $this->user->getId();

        $cashFlowGroupData = [
            "uuid" => Uuid::uuid6(),
            "id_user" => $this->user,
            "group_name" => "novo grupo",
            "created_at" => date("Y-m-d"),
            "updated_at" => date("Y-m-d"),
            "deleted" => 0
        ];

        $this->cashFlowGroup->persistData($cashFlowGroupData);
        $cashFlowGroupData = $this->cashFlowGroup->findCashFlowGroupByUser([], $this->user);
        $this->assertNotEmpty($cashFlowGroupData);
        
        $this->user = new User();
        $this->user->dropUserById($userId);
    }

    public function testFindCashFlowGroupByUserIsEmpty()
    {
        $this->user = new User();
        $this->cashFlowGroup = new CashFlowGroup();
        
        $this->user->setId(10000);
        $response = $this->cashFlowGroup->findCashFlowGroupByUser([], $this->user);
        $this->assertJsonStringEqualsJsonString(
            json_encode(["error" => "nenhum registro foi encontrado"]),
            $response
        );
    }

    public function testDropCashFlowGroupByUuid()
    {
        $this->cashFlowGroup = new CashFlowGroup();
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
        $userId = $this->user->getId();
        $cashFlowGroupUuid = Uuid::uuid6();
        
        $cashFlowGroupData = [
            "uuid" => $cashFlowGroupUuid,
            "id_user" => $this->user,
            "group_name" => "novo grupo",
            "created_at" => date("Y-m-d"),
            "updated_at" => date("Y-m-d"),
            "deleted" => 0
        ];

        $this->cashFlowGroup->persistData($cashFlowGroupData);
        $response = $this->cashFlowGroup->dropCashFlowGroupByUuid($cashFlowGroupUuid);
        $this->assertNull($response);

        $this->user = new User();
        $this->user->dropUserById($userId);
    }

    public function testFindCashFlowGroupByName()
    {
        $this->cashFlowGroup = new CashFlowGroup();
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
        $userId = $this->user->getId();
        $cashFlowGroupUuid = Uuid::uuid6();
        
        $cashFlowGroupData = [
            "uuid" => $cashFlowGroupUuid,
            "id_user" => $this->user,
            "group_name" => "novo grupo",
            "created_at" => date("Y-m-d"),
            "updated_at" => date("Y-m-d"),
            "deleted" => 0
        ];

        $this->cashFlowGroup->persistData($cashFlowGroupData);
        $response = $this->cashFlowGroup
        ->findCashFlowGroupByName($cashFlowGroupData["group_name"], $this->user);

        $this->assertIsObject($response);
        $this->user->dropUserById($userId);
    }

    public function testFindCashFlowGroupByNameIsEmpty()
    {
        $this->user = new User();
        $this->cashFlowGroup = new CashFlowGroup();

        $userData = [
            "uuid" => "1eed7357-6e74-6096-abf0-0242ac120003",
            "user_full_name" => "teste fulano de tal 2",
            "user_nick_name" => "fulanoDeTal2",
            "user_email" => "testefulano2@gmail.com",
            "user_password" => password_hash("minhasenha1234", PASSWORD_DEFAULT),
            "deleted" => 0
        ];

        $this->user->persistData($userData);
        $userId = $this->user->getId();
        $response = $this->cashFlowGroup->findCashFlowGroupByName("undefined_name", $this->user);
        $this->assertJsonStringEqualsJsonString(
            json_encode(["error" => "nenhum registro foi encontrado"]),
            $response
        );
        $this->user->dropUserById($userId);
    }
}
