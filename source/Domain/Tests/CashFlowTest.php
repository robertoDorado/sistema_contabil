<?php
namespace Source\Domain\Tests;

use PHPUnit\Framework\TestCase;
use Source\Domain\Model\CashFlow;

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

    public function testInvalidPersistCashFlow()
    {
        $this->cashFlow = new CashFlow();
        $response = $this->cashFlow->persistData([]);
        $this->assertJsonStringEqualsJsonString(
            json_encode(["invalid_persist_data" => "dados inválidos"]),
            $response
        );
    }
}
