<?php
namespace Source\Domain\Tests;

use PHPUnit\Framework\TestCase;
use Source\Domain\Model\CashFlowGroup;

/**
 * CashFlowGroupTest Domain\Tests
 * @link 
 * @author Roberto Dorado <robertodorado7@gmail.com>
 * @package Source\Domain\Tests
 */
class CashFlowGroupTest extends TestCase
{
    /** @var CashFlowGroup CashFlowGroup */
    private CashFlowGroup $cashFlowGroup;

    public function testInvalidPersistData()
    {
        $this->cashFlowGroup = new CashFlowGroup();
        $response = $this->cashFlowGroup->persistData([]);
        $this->assertJsonStringEqualsJsonString(
            json_encode(["error" => "dados inválidos"]),
            $response
        );
    }
}
