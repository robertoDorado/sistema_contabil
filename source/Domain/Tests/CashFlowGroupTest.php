<?php
namespace Source\Domain\Tests;

use Exception;
use PHPUnit\Framework\TestCase;
use Ramsey\Uuid\Nonstandard\Uuid;
use Source\Domain\Model\CashFlowGroup;

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

    public function testPersistData()
    {
        $this->cashFlowGroup = new CashFlowGroup();
        $uuid = Uuid::uuid6();
        $data = [
            "uuid" => $uuid,
            "group_name" => "novo grupo",
            "created_at" => date("Y-m-d"),
            "updated_at" => date("Y-m-d"),
            "deleted" => 0
        ];
        $response = $this->cashFlowGroup->persistData($data);
        $this->assertTrue($response);
        $this->cashFlowGroup->dropCashFlowGroupByUuid($uuid);
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
        $cashFlowGroupUuid = uuid::uuid6();

        $cashFlowGroupData = [
            "uuid" => $cashFlowGroupUuid,
            "group_name" => "novo grupo",
            "created_at" => date("Y-m-d"),
            "updated_at" => date("Y-m-d"),
            "deleted" => 0
        ];

        $this->cashFlowGroup->persistData($cashFlowGroupData);
        $cashFlowGroupData["group_name"] = "novo grupo 2";
        $this->cashFlowGroup->updateCashFlowGroupByUuid($cashFlowGroupData);
        $cashFlowGroupData = $this->cashFlowGroup->findCashFlowGroupByUuid($cashFlowGroupUuid);
        
        $this->assertEquals("novo grupo 2", $cashFlowGroupData->group_name);
        $this->cashFlowGroup->dropCashFlowGroupByUuid($cashFlowGroupUuid);
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
        $cashFlowGroupUuid = uuid::uuid6();

        $cashFlowGroupData = [
            "uuid" => $cashFlowGroupUuid,
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
        $this->cashFlowGroup->dropCashFlowGroupByUuid($cashFlowGroupUuid);
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
}
