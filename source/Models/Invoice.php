<?php
namespace Source\Models;

use Source\Core\Model;

/**
 * Invoice Models
 * @link 
 * @author Roberto Dorado <robertodorado7@gmail.com>
 * @package Source\Models
 */
class Invoice extends Model
{
    /** @var string uuid */
    protected string $uuid = "uuid";

    /** @var string idUser */
    protected string $idUser = "id_user";

    /** @var string idCompany */
    protected string $idCompany = "id_company";

    /** @var string Nota Fiscal XML */
    protected string $xml = "xml";

    /** @var string Número de protocolo */
    protected string $protocolNumber = "protocol_number";

    /** @var string Chave de acesso da nota */
    protected string $accessKey = "access_key";

    /** @var string Data de criação */
    protected string $createdAt = "created_at";

    /** @var string Data de atualização */
    protected string $updatedAt = "updated_at";

    /** @var string soft delete */
    protected string $deleted = "deleted";

    /**
     * Invoice constructor
     */
    public function __construct()
    {
        parent::__construct(CONF_DB_NAME . ".invoice", ["id"], [
            $this->uuid,
            $this->idUser,
            $this->idCompany,
            $this->xml,
            $this->protocolNumber,
            $this->accessKey,
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

    public function getXml()
    {
        return $this->xml;
    }

    public function setXml(string $xml)
    {
        $this->xml = $xml;
    }
}
