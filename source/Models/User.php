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
    /** @var string Uuid do usuário */
    protected string $uuid = "uuid";

    /** @var string Nome completo do usuário */
    protected string $userFullName = "user_full_name";

    /** @var string Nickname do usuário */
    protected string $userNickName = "user_nick_name";

    /** @var string E-mail do usuário */
    protected string $userEmail = "user_email";

    /** @var string Senha do usuário */
    protected string $userPassword = "user_password";

    /** @var string Coluna para soft delete do registro */
    protected string $deleted = "deleted";

    /**
     * User constructor
     */
    public function __construct()
    {
        parent::__construct(CONF_DB_NAME . ".user", ["id"], [
            $this->uuid,
            $this->userFullName,
            $this->userNickName,
            $this->userEmail,
            $this->userPassword,
            $this->deleted
        ]);
    }

    public function getDeleted()
    {
        return $this->deleted;
    }

    public function setDeleted(int $delete)
    {
        $this->deleted = $delete;
    }

    public function getUuid()
    {
        return $this->uuid;
    }

    public function setUuid(string $uuid)
    {
        $this->uuid = $uuid;
    }
}
