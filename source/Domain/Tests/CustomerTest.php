<?php
namespace Source\Domain\Tests;

use PHPUnit\Framework\TestCase;
use Source\Domain\Model\Customer;

/**
 * CustomerTest Domain\Tests
 * @link 
 * @author Roberto Dorado <robertodorado7@gmail.com>
 * @package Source\Domain\Tests
 */
class CustomerTest extends TestCase
{
    /** @var Customer Customer */
    private Customer $customer;

    public function testPersistInvalidData()
    {
       $this->customer = new Customer();
       $response = $this->customer->persistData([]);
       $this->assertJsonStringEqualsJsonString(
            json_encode(["error" => "dados inválidos"]),
            $response
       );
    }
}
