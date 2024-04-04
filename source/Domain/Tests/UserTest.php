<?php
namespace Source\Domain\Tests;

use PHPUnit\Framework\TestCase;
use Source\Domain\Model\User;

/**
 * UserTest Domain\Tests
 * @link 
 * @author Roberto Dorado <robertodorado7@gmail.com>
 * @package Source\Domain\Tests
 */
class UserTest extends TestCase
{
    /** @var User Usuário */
    private User $user;

    public function testInvalidPersistData()
    {
        $this->user = new User();
        $response = $this->user->persistData([]);
        $this->assertJsonStringEqualsJsonString(
            json_encode(["invalid_persist_data" => "dados inválidos"]), 
            $response
        );
    }
}
