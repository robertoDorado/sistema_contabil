<?php
namespace Source\Domain\Tests;

use Exception;
use PHPUnit\Framework\TestCase;
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
            "id_user" => $this->user,
            "entry" => 100.55,
            "history" => "venda realizada no dia" . date("d/m/Y"),
            "entry_typee" => 1,
            "created_at" => date("Y-m-d"),
            "updated_at" => date("Y-m-d")
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
            "id_user" => $this->user,
            "entry" => "---",
            "history" => "venda realizada no dia" . date("d/m/Y"),
            "entry_type" => 1,
            "created_at" => date("Y-m-d"),
            "updated_at" => date("Y-m-d")
        ];

        $this->expectException(Exception::class);
        $this->expectExceptionMessage("valor de entrada inválido");
        $this->cashFlow->persistData($cashFlowData);
    }

    public function testInvalidInstance()
    {
        $this->cashFlow = new CashFlow();
        $cashFlowData = [
            "id_user" => $this->cashFlow,
            "entry" => "445.77",
            "history" => "venda realizada no dia" . date("d/m/Y"),
            "entry_type" => 1,
            "created_at" => date("Y-m-d"),
            "updated_at" => date("Y-m-d")
        ];

        $this->expectException(Exception::class);
        $this->expectExceptionMessage("Instancia inválida");
        $this->cashFlow->persistData($cashFlowData);
    }

    public function testPersistDataIsTrue()
    {
        $this->cashFlow = new CashFlow();
        $this->user = new User();
        $userData = [
            "user_full_name" => "teste fulano de tal 2",
            "user_nick_name" => "fulanoDeTal2",
            "user_email" => "testefulano2@gmail.com",
            "user_password" => password_hash("minhasenha1234", PASSWORD_DEFAULT),
            "created_at" => date("Y-m-d"),
            "updated_at" => date("Y-m-d")
        ];
        $this->user->persistData($userData);
        $this->user = new User();
        
        $userData = $this->user->findUserByEmail($userData["user_email"]);
        $this->user->setId($userData->id);

        $cashFlowData = [
            "id_user" => $this->user,
            "entry" => "1.750,45",
            "history" => "venda realizada no dia " . date("d/m/Y"),
            "entry_type" => 1,
            "created_at" => date("Y-m-d"),
            "updated_at" => date("Y-m-d")
        ];

        $response = $this->cashFlow->persistData($cashFlowData);
        $this->assertTrue($response, "erro ao persistir o registro do fluxo de caixa");
        $this->user = new User();
        $this->cashFlow->dropCashFlowById($this->cashFlow->getId());
        $this->user->dropUserById($userData->id);
    }
}
