<?php
namespace Source\Domain\Tests;

use Exception;
use PHPUnit\Framework\TestCase;
use Source\Support\StripePayment;

/**
 * StripePaymentTest Domain\Tests
 * @link 
 * @author Roberto Dorado <robertodorado7@gmail.com>
 * @package Source\Domain\Tests
 */
class StripePaymentTest extends TestCase
{
    private StripePayment $stripePayment;

    public function testInvalidCreateCustomer()
    {
        $this->stripePayment = new StripePayment();
        $this->expectException(Exception::class);
        $this->expectExceptionMessage("dados do cliente não pode estar vazio");
        $this->stripePayment->createCustomer([]);
    }
}
