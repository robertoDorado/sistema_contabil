<?php
namespace Source\Domain\Tests;

use Exception;
use PDOException;
use PHPUnit\Framework\TestCase;
use Ramsey\Uuid\Uuid;
use Source\Domain\Model\User;

/**
 * User Domain\Tests
 * @link 
 * @author Roberto Dorado <robertodorado7@gmail.com>
 * @package Source\Domain\Tests
 */
class UserTest extends TestCase
{
    /** @var User Modelo Usuário a ser testado */
    private User $user;

    public function testPersistDataIsTrue()
    {
        $this->user = new User();
        $data = [
            "uuid" => Uuid::uuid6(),
            "user_full_name" => "teste fulano de tal",
            "user_nick_name" => "fulanoDeTal",
            "user_email" => "testefulano@gmail.com",
            "user_password" => password_hash("minhasenha123", PASSWORD_DEFAULT),
            "deleted" => 0
        ];

        $response = $this->user->persistData($data);
        $this->assertTrue($response);
        $this->user->dropUserByEmail($data["user_email"]);
    }

    public function testPersistDataErrorJsonEmptyData()
    {
        $this->user = new User();
        $this->assertJsonStringEqualsJsonString(json_encode([
            "invalid_persist_data" => "dados inválidos"
        ]),
        $this->user->persistData([]));
    }

    public function testException()
    {
        $this->user = new User();
        $data = [
            "uuid" => Uuid::uuid6(),
            "user_full_name" => "teste fulano de tal",
            "user_nick_name" => "fulanoDeTal",
            "user_emaill" => "testefulano@gmail.com",
            "user_password" => password_hash("minhasenha123", PASSWORD_DEFAULT),
            "deleted" => 0
        ];
        $this->expectException(Exception::class);
        $this->expectExceptionMessage("esta propriedade user_email foi passado de maneira incorreta");
        $this->user->persistData($data);
    }

    public function testErrorJsonEqualsUserAlreadyRegister()
    {
        $this->user = new User();
        $data = [
            "uuid" => Uuid::uuid6(),
            "user_full_name" => "teste fulano de tal",
            "user_nick_name" => "fulanoDeTal",
            "user_email" => "testefulano@gmail.com",
            "user_password" => password_hash("minhasenha123", PASSWORD_DEFAULT),
            "deleted" => 0
        ];

        $this->user->persistData($data);
        $response = $this->user->persistData($data);
        $this->assertJsonStringEqualsJsonString(json_encode([
            "error_user_exists" => "este usuário já foi cadastrado"
        ]),
        $response);
        $this->user->dropUserByEmail($data["user_email"]);
    }

    public function testLoginAccessDenied()
    {
        $this->user = new User();
        $data = [
            "uuid" => Uuid::uuid6(),
            "user_full_name" => "teste fulano de tal",
            "user_nick_name" => "fulanoDeTal",
            "user_email" => "testefulano@gmail.com",
            "user_password" => password_hash("minhasenha123", PASSWORD_DEFAULT),
            "deleted" => 1
        ];
        $this->user->persistData($data);
        $userId = $this->user->getId();
        
        $response = $this->user->login("testefulano@gmail.com", "minhasenha123");
        $this->assertJsonStringEqualsJsonString(
            $response, json_encode(["access_denied" => "acesso negado"]));
        
        $this->user = new User();
        $this->user->dropUserById($userId);
    }

    public function testInvalidLoginParameter()
    {
        $this->user = new User();
        $response = $this->user->login("", "");
        $this->assertJsonStringEqualsJsonString(json_encode([
            "invalid_login_data" => "dados iválidos"
        ]),
        $response);
    }

    public function testUserIsNotRegister()
    {
        $this->user = new User();
        $response = $this->user->login("testefulano@gmail.com", "minhasenha123");
        $this->assertJsonStringEqualsJsonString(json_encode([
            "user_not_register" => "usuário não registrado"
        ]),
        $response);
    }

    public function testUserNotAuth()
    {
        $this->user = new User();
        $data = [
            "uuid" => Uuid::uuid6(),
            "user_full_name" => "teste fulano de tal",
            "user_nick_name" => "fulanoDeTal",
            "user_email" => "testefulano@gmail.com",
            "user_password" => password_hash("minhasenha123", PASSWORD_DEFAULT),
            "deleted" => 0
        ];

        $this->user->persistData($data);
        $response = $this->user->login($data["user_email"], "minhasenha12345");
        $this->assertJsonStringEqualsJsonString(json_encode([
            "user_not_auth" => "usuário não autenticado"
        ]),
        $response);
        $this->user->dropUserByEmail($data["user_email"]);
    }

    public function testLoginAuth()
    {
        $this->user = new User();
        $data = [
            "uuid" => Uuid::uuid6(),
            "user_full_name" => "teste fulano de tal",
            "user_nick_name" => "fulanoDeTal",
            "user_email" => "testefulano@gmail.com",
            "user_password" => password_hash("minhasenha123", PASSWORD_DEFAULT),
            "deleted" => 0
        ];
        
        $this->user->persistData($data);
        $response = $this->user->login($data["user_email"], "minhasenha123");
        $this->assertIsObject($response);
        $this->user->dropUserByEmail($data["user_email"]);
    }

    public function testDropUserEmpty()
    {
        $this->user = new User();
        $this->expectException(Exception::class);
        $this->expectExceptionMessage("usuario nao encontrado");
        $this->user->dropUserByEmail("emailinexistente@gmail.com");
    }

    public function testDropUserByEmail()
    {
        $this->user = new User();
        $data = [
            "uuid" => Uuid::uuid6(),
            "user_full_name" => "teste fulano de tal",
            "user_nick_name" => "fulanoDeTal",
            "user_email" => "testefulano@gmail.com",
            "user_password" => password_hash("minhasenha123", PASSWORD_DEFAULT),
            "deleted" => 0,
        ];
        
        $this->user->persistData($data);
        $response = $this->user->dropUserByEmail($data["user_email"]);
        $this->assertTrue($response, 'erro ao deletar usuário');
    }

    public function testFindUserByIdAccessDenied()
    {
        $this->user = new User();
        $data = [
            "uuid" => Uuid::uuid6(),
            "user_full_name" => "teste fulano de tal",
            "user_nick_name" => "fulanoDeTal",
            "user_email" => "testefulano@gmail.com",
            "user_password" => password_hash("minhasenha123", PASSWORD_DEFAULT),
            "deleted" => 1
        ];
        
        $this->user->persistData($data);
        $userId = $this->user->getId();
        
        $this->user = new User();
        $this->user->setId($userId);
        $response = $this->user->findUserById();
        
        $this->assertJsonStringEqualsJsonString($response, 
            json_encode(["access_denied" => "acesso negado"]));
        
        $this->user = new User();
        $this->user->dropUserById($userId);
    }

    public function testFindUserById()
    {
        $this->user = new User();
        $data = [
            "uuid" => Uuid::uuid6(),
            "user_full_name" => "teste fulano de tal",
            "user_nick_name" => "fulanoDeTal",
            "user_email" => "testefulano@gmail.com",
            "user_password" => password_hash("minhasenha123", PASSWORD_DEFAULT),
            "deleted" => 0
        ];
        
        $this->user->persistData($data);
        $userId = $this->user->getId();

        $this->user = new User();
        $this->user->setId($userId);
        
        $userData = $this->user->findUserById();
        $this->assertIsObject($userData);
        
        $this->user = new User();
        $this->user->dropUserById($userData->id);
    }

    public function testFindUserByIdNotFound()
    {
        $this->user = new User();
        $this->user->setId(1000000000000000000);
        $userData = $this->user->findUserById();
        $this->assertJsonStringEqualsJsonString(json_encode([
            "user_not_found" => "usuário não encontrado"
        ]), $userData);
    }

    public function invalidSetUserId()
    {
        $this->user = new User();
        $this->expectException(Exception::class);
        $this->expectExceptionMessage("parametro inválido na classe " . User::class . "");
        $this->user->setId("--");
    }

    public function testGetId()
    {
        $this->user = new User();
        $this->expectException(Exception::class);
        $this->expectExceptionMessage("id não atribuido");
        $this->user->setId(0);
        $this->user->getId();
    }

    public function testDropUserById()
    {
        $this->user = new User();
        $data = [
            "uuid" => Uuid::uuid6(),
            "user_full_name" => "teste fulano de tal",
            "user_nick_name" => "fulanoDeTal",
            "user_email" => "testefulano@gmail.com",
            "user_password" => password_hash("minhasenha123", PASSWORD_DEFAULT),
            "deleted" => 0
        ];
        $this->user->persistData($data);
        $userData = $this->user->findUserByEmail($data["user_email"]);
        $this->user = new User();
        $this->assertTrue($this->user->dropUserById($userData->id), 'erro ao dropar usuário pelo id');
    }

    public function testTryDropUserNotFoud()
    {
        $this->user = new User();
        $this->expectException(Exception::class);
        $this->expectExceptionMessage("usuario nao encontrado");
        $this->user->dropUserById(0);
    }

    public function testFindUserByEmailAccessDenied()
    {
        $this->user = new User();
        $data = [
            "uuid" => Uuid::uuid6(),
            "user_full_name" => "teste fulano de tal",
            "user_nick_name" => "fulanoDeTal",
            "user_email" => "testefulano@gmail.com",
            "user_password" => password_hash("minhasenha123", PASSWORD_DEFAULT),
            "deleted" => 1
        ];
        $this->user->persistData($data);
        $userId = $this->user->getId();

        $this->user = new User();
        $response = $this->user->findUserByEmail($data["user_email"]);
       
        $this->assertJsonStringEqualsJsonString($response, 
            json_encode(["access_denied" => "acesso negado"]));
        
        $this->user = new User();
        $this->user->dropUserById($userId);
    }

    public function testFindUserByEmailNotFound()
    {
        $this->user = new User();
        $userData = $this->user->findUserByEmail("emailqualquer@gmail.com");
        $this->assertJsonStringEqualsJsonString(json_encode(["user_not_exists" => "usuário não existe"]),
        $userData);
    }

    public function testfindUserByUuid()
    {
        $this->user = new User();
        $data = [
            "uuid" => "1eed719c-6cdc-6b78-bbef-0242ac120003",
            "user_full_name" => "teste fulano de tal",
            "user_nick_name" => "fulanoDeTal",
            "user_email" => "testefulano@gmail.com",
            "user_password" => password_hash("minhasenha123", PASSWORD_DEFAULT),
            "deleted" => 0
        ];
        $this->user->persistData($data);
        
        $this->user = new User();
        $userData = $this->user->findUserByUuid($data["uuid"]);
        $this->assertIsObject($userData);

        $this->user = new User();
        $this->user->dropUserByUuid($data["uuid"]);
    }

    public function testFindUserByUuidNotFoud()
    {
        $this->user = new User();
        $userData = $this->user->findUserByUuid("1eed719c-5555-6b78-bbef-0242ac120003");

        $this->assertJsonStringEqualsJsonString($userData,
            json_encode(["user_not_found" => "usuário não encontrado"]));
    }

    public function testfindUserByUuidAccessDenied()
    {
        $this->user = new User();
        $data = [
            "uuid" => "1eed719c-6cdc-6b78-bbef-0242ac120003",
            "user_full_name" => "teste fulano de tal",
            "user_nick_name" => "fulanoDeTal",
            "user_email" => "testefulano@gmail.com",
            "user_password" => password_hash("minhasenha123", PASSWORD_DEFAULT),
            "deleted" => 1
        ];

        $this->user->persistData($data);
        $this->user = new User();
        $response = $this->user->findUserByUuid($data["uuid"]);

        $this->assertJsonStringEqualsJsonString($response, 
            json_encode(["access_denied" => "acesso negado"]));
        
        $this->user = new User();
        $this->user->dropUserByUuid($data["uuid"]);
    }

    public function testDropUserByUuidNotFound()
    {
        $this->user = new User();
        $data = [
            "uuid" => "1eed719c-6cdc-6b78-bbef-0242ac120003",
            "user_full_name" => "teste fulano de tal",
            "user_nick_name" => "fulanoDeTal",
            "user_email" => "testefulano@gmail.com",
            "user_password" => password_hash("minhasenha123", PASSWORD_DEFAULT),
            "deleted" => 0
        ];

        $this->user->persistData($data);
        $this->expectException(Exception::class);
        $this->expectExceptionMessage("usuário não encontrado");
        $this->user = new User();
        $this->user->dropUserByUuid("1eed719c-6cdc-5252-bbef-0242ac120003");
    }

    public function testDropUserByUuid()
    {
        $this->user = new User();
        $data = [
            "uuid" => "1eed719c-6cdc-6b78-bbef-0242ac120003",
            "user_full_name" => "teste fulano de tal",
            "user_nick_name" => "fulanoDeTal",
            "user_email" => "testefulano@gmail.com",
            "user_password" => password_hash("minhasenha123", PASSWORD_DEFAULT),
            "deleted" => 0
        ];

        $this->user->persistData($data);
        
        $this->user = new User();
        $this->assertNull($this->user->dropUserByUuid($data["uuid"]));
    }

    public function testUpdateUserByUuid()
    {
        $this->user = new User();
        $data = [
            "uuid" => "1eed719c-6cdc-6b78-bbef-0242ac120003",
            "user_full_name" => "teste fulano de tal",
            "user_nick_name" => "fulanoDeTal",
            "user_email" => "testefulano@gmail.com",
            "user_password" => password_hash("minhasenha123", PASSWORD_DEFAULT),
            "deleted" => 0
        ];

        $this->user->persistData($data);
        $this->user = new User();
        
        $data["deleted"] = 1;
        $response = $this->user->updateUserByUuid($data);
        $this->assertTrue($response);
        
        $this->user = new User();
        $this->user->dropUserByUuid($data["uuid"]);
    }

    public function testUpdateUserByUuidOnNull()
    {
        $this->user = new User();
        $data = [
            "uuid" => "1eed719c-6cdc-6b78-bbef-0242ac120003",
            "user_full_name" => "teste fulano de tal",
            "user_nick_name" => "fulanoDeTal",
            "user_email" => "testefulano@gmail.com",
            "user_password" => password_hash("minhasenha123", PASSWORD_DEFAULT),
            "deleted" => 0
        ];
        $response = $this->user->updateUserByUuid($data);
        $this->assertJsonStringEqualsJsonString($response,
            json_encode(["user_not_found" => "usuário não encontrado"]));
    }

    public function testUpdateUserByUuidAccessDenied()
    {
        $this->user = new User();
        $response = $this->user->updateUserByUuid([]);
        $this->assertJsonStringEqualsJsonString($response, 
        json_encode(["data_is_empty" => "data não pode ser vazio"]));
    }
}
