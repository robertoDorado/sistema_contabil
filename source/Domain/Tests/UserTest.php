<?php
namespace Source\Domain\Tests;

use Exception;
use PHPUnit\Framework\TestCase;
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
            "user_full_name" => "teste fulano de tal",
            "user_nick_name" => "fulanoDeTal",
            "user_email" => "testefulano@gmail.com",
            "user_password" => password_hash("minhasenha123", PASSWORD_DEFAULT)
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
            "user_full_name" => "teste fulano de tal",
            "user_nick_name" => "fulanoDeTal",
            "user_emaill" => "testefulano@gmail.com",
            "user_password" => password_hash("minhasenha123", PASSWORD_DEFAULT)
        ];
        $this->expectException(Exception::class);
        $this->expectExceptionMessage("esta propriedade user_email foi passado de maneira incorreta");
        $this->user->persistData($data);
    }

    public function testErrorJsonEqualsUserAlreadyRegister()
    {
        $this->user = new User();
        $data = [
            "user_full_name" => "teste fulano de tal",
            "user_nick_name" => "fulanoDeTal",
            "user_email" => "testefulano@gmail.com",
            "user_password" => password_hash("minhasenha123", PASSWORD_DEFAULT)
        ];

        $this->user->persistData($data);
        $response = $this->user->persistData($data);
        $this->assertJsonStringEqualsJsonString(json_encode([
            "error_user_exists" => "este usuário já foi cadastrado"
        ]),
        $response);
        $this->user->dropUserByEmail($data["user_email"]);
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
            "user_full_name" => "teste fulano de tal",
            "user_nick_name" => "fulanoDeTal",
            "user_email" => "testefulano@gmail.com",
            "user_password" => password_hash("minhasenha123", PASSWORD_DEFAULT)
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
            "user_full_name" => "teste fulano de tal",
            "user_nick_name" => "fulanoDeTal",
            "user_email" => "testefulano@gmail.com",
            "user_password" => password_hash("minhasenha123", PASSWORD_DEFAULT)
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
            "user_full_name" => "teste fulano de tal",
            "user_nick_name" => "fulanoDeTal",
            "user_email" => "testefulano@gmail.com",
            "user_password" => password_hash("minhasenha123", PASSWORD_DEFAULT)
        ];
        
        $this->user->persistData($data);
        $response = $this->user->dropUserByEmail($data["user_email"]);
        $this->assertTrue($response, 'erro ao deletar usuário');
    }
}
