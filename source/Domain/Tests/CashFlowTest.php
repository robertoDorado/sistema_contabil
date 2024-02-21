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

    public function testCalculateBalancePositive()
    {
        $cashFlowIds = [];
        $userIds = [];
        
        for ($i = 0; $i < 3; $i++) {
            $this->cashFlow = new CashFlow();
            $this->user = new User();
            $varData = $i + 1;

            $userData = [
                "user_full_name" => "teste fulano de tal {$varData}",
                "user_nick_name" => "fulanoDeTal2",
                "user_email" => "testefulano{$varData}@gmail.com",
                "user_password" => password_hash("minhasenha1234", PASSWORD_DEFAULT),
                "created_at" => date("Y-m-d"),
                "updated_at" => date("Y-m-d")
            ];

            $this->user->persistData($userData);
            $this->user->setId($this->user->getId());
            $cashFlowData = [
                "id_user" => $this->user,
                "entry" => "1.750,45",
                "history" => "venda realizada no dia " . date("d/m/Y"),
                "entry_type" => 1,
                "created_at" => date("Y-m-d"),
                "updated_at" => date("Y-m-d")
            ];
            $this->cashFlow->persistData($cashFlowData);
            array_push($cashFlowIds, $this->cashFlow->getId());
            array_push($userIds, $this->user->getId());
        }

        $balance = $this->cashFlow->calculateBalance();
        $this->assertEquals(5251.35, $balance);

        if (!empty($cashFlowIds)) {
            for ($i = 0; $i < count($cashFlowIds); $i++) {
                $this->cashFlow = new CashFlow();
                $this->cashFlow->dropCashFlowById($cashFlowIds[$i]);
            }
        }

        if (!empty($userIds)) {
            for ($i = 0; $i < count($userIds); $i++) { 
                $this->user = new User();
                $this->user->dropUserById($userIds[$i]);
            }
        }
    }

    public function testCalculateBalanceNegative()
    {
        $cashFlowIds = [];
        $userIds = [];

        for ($i = 0; $i < 3; $i++) {
            $this->cashFlow = new CashFlow();
            $this->user = new User();
            $varData = $i + 1;

            $userData = [
                "user_full_name" => "teste fulano de tal {$varData}",
                "user_nick_name" => "fulanoDeTal2",
                "user_email" => "testefulano{$varData}@gmail.com",
                "user_password" => password_hash("minhasenha1234", PASSWORD_DEFAULT),
                "created_at" => date("Y-m-d"),
                "updated_at" => date("Y-m-d")
            ];

            $this->user->persistData($userData);
            $this->user->setId($this->user->getId());
            $cashFlowData = [
                "id_user" => $this->user,
                "entry" => "1.750,45",
                "history" => "venda realizada no dia " . date("d/m/Y"),
                "entry_type" => 0,
                "created_at" => date("Y-m-d"),
                "updated_at" => date("Y-m-d")
            ];
            $this->cashFlow->persistData($cashFlowData);
            array_push($cashFlowIds, $this->cashFlow->getId());
            array_push($userIds, $this->user->getId());
        }

        $balance = $this->cashFlow->calculateBalance();
        $this->assertEquals((5251.35 * -1), $balance);

        if (!empty($cashFlowIds)) {
            for ($i = 0; $i < count($cashFlowIds); $i++) {
                $this->cashFlow = new CashFlow();
                $this->cashFlow->dropCashFlowById($cashFlowIds[$i]);
            }
        }

        if (!empty($userIds)) {
            for ($i = 0; $i < count($userIds); $i++) { 
                $this->user = new User();
                $this->user->dropUserById($userIds[$i]);
            }
        }
    }

    public function testFindCashFlowByIdNotFound()
    {
        $this->cashFlow = new CashFlow();
        $this->cashFlow->setId(1);
        $response = $this->cashFlow->findCashFlowById();
        $this->assertJsonStringEqualsJsonString(
            json_encode([
                "cashflow_not_found" => "registro fluxo de caixa não encontrado"
            ]), 
        $response);
    }

    public function testFindCashFlowById()
    {
        $this->cashFlow = new CashFlow();
        $this->user = new User();
        $userData = [
            "user_full_name" => "teste fulano de tal 2",
            "user_nick_name" => "fulanoDeTal2",
            "user_email" => "testefulano2@gmail.com",
            "user_password" => password_hash("minhasenha1234", PASSWORD_DEFAULT)
        ];
        $this->user->persistData($userData);
        
        $cashFlowData = [
            "id_user" => $this->user,
            "entry" => "1.750,45",
            "history" => "venda realizada no dia " . date("d/m/Y"),
            "entry_type" => 1,
            "created_at" => date("2024-01-14"),
            "updated_at" => date("2024-01-14")
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

    public function testFindAllCashFlowIsEmpty()
    {
        $this->cashFlow = new CashFlow();
        $response = $this->cashFlow->findAllCashFlow();
        $this->assertJsonStringEqualsJsonString(json_encode(
            ["cash_flow_empty" => "nenhum registro foi encontrado"]),
        $response);
    }

    public function testFindAllCashFlow()
    {
        $cashFlowIds = [];
        $userIds = [];
        for ($i=0; $i < 3; $i++) {
            $this->user = new User();
            $userVal = 1 + $i;
            $userData = [
                "user_full_name" => "teste fulano de tal {$userVal}",
                "user_nick_name" => "fulanoDeTal{$userVal}",
                "user_email" => "testefulano{$userVal}@gmail.com",
                "user_password" => password_hash("minhasenha1234", PASSWORD_DEFAULT)
            ];
            
            $this->user->persistData($userData);
            $this->cashFlow = new CashFlow();
            $cashFlowData = [
                "id_user" => $this->user,
                "entry" => "1.750,45",
                "history" => "venda realizada no dia " . date("d/m/Y"),
                "entry_type" => 1,
                "created_at" => date("Y-m-d"),
                "updated_at" => date("Y-m-d")
            ];
            
            $this->cashFlow->persistData($cashFlowData);
            array_push($userIds, $this->user->getId());
            array_push($cashFlowIds, $this->cashFlow->getId());
        }

        $this->cashFlow = new CashFlow();
        $this->assertEquals(3, count($this->cashFlow->findAllCashFlow()));

        for ($i=0; $i < count($cashFlowIds); $i++) {
            $this->cashFlow = new CashFlow();
            $this->cashFlow->dropCashFlowById($cashFlowIds[$i]);
        }
        
        for ($i=0; $i < count($userIds); $i++) { 
            $this->user = new User();
            $this->user->dropUserById($userIds[$i]);
        }

    }
}
