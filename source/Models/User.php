<?php
namespace Source\Models;

use Source\Core\Model;

/**
 * User Models
 * @link 
 * @author Roberto Dorado <robertodorado7@gmail.com>
 * @package Source\Models
 */
class User extends Model
{
    /** @var string Nome completo do usuário */
    private string $userFullName = "user_full_name";

    /** @var string Nickname do usuário */
    private string $userNickName = "user_nick_name";

    /** @var string E-mail do usuário */
    private string $userEmail = "user_email";

    /** @var string Senha do usuário */
    private string $userPassword = "user_password";

    /**
     * User constructor
     */
    public function __construct()
    {
        parent::__construct(CONF_DB_NAME . ".user", ["id"], [$this->userFullName, $this->userNickName, $this->userEmail, $this->userPassword]);
    }
}
