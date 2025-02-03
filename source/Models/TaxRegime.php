<?php
namespace Source\Models;

use Source\Core\Model;

/**
 * TaxRegime Models
 * @link 
 * @author Roberto Dorado <robertodorado7@gmail.com>
 * @package Source\Models
 */
class TaxRegime extends Model
{
    /** @var string uuid */
    protected string $uuid = "uuid";

    /** @var string id do usuário */
    protected string $idUser = "id_user";

    /** @var string id da empresa */
    protected string $idCompany = "id_company";

    /** @var string regisme tributário id */
    protected string $taxRegimeId = "tax_regime_id";

    /** @var string Data criada */
    protected string $createdAt = "created_at";

    /** @var string Data de atualização */
    protected string $updatedAt = "updated_at";

    /** @var string Soft delete */
    protected string $deleted = "deleted";

    /**
     * TaxRegime constructor
     */
    public function __construct()
    {
        parent::__construct(CONF_DB_NAME . ".tax_regime", ["id"], [
            $this->uuid,
            $this->idUser,
            $this->idCompany,
            $this->taxRegimeId,
            $this->createdAt,
            $this->updatedAt,
            $this->deleted
        ]);
    }

    public function getDeleted()
    {
        return $this->deleted;
    }

    public function setDeleted(int $delete) {
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
