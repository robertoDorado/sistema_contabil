<?php
namespace Source\Models;

use Source\Core\Model;

/**
 * SupportTickets Models
 * @link 
 * @author Roberto Dorado <robertodorado7@gmail.com>
 * @package Source\Models
 */
class SupportTickets extends Model
{
    /** @var string Uuid */
    protected string $uuid = "uuid";

    /** @var string id_user */
    protected string $idUser = "id_user";

    /** @var string id_support */
    protected string $idSupport = "id_support";

    /** @var string Conteúdo do ticket */
    protected string $contentMessage = "content_message";

    /** @var string Anexo da mensagem */
    protected string $contentAttachment = "content_attachment";

    /** @var string Status do chamado */
    protected string $status = "status";

    /** @var string Soft delete */
    protected string $deleted = "deleted";

    /**
     * SupportTickets constructor
     */
    public function __construct()
    {
        parent::__construct(CONF_DB_NAME . ".support_tickets", ["id"], [
            $this->uuid,
            $this->idUser,
            $this->idSupport,
            $this->contentMessage,
            $this->contentAttachment,
            $this->status,
            $this->deleted
        ]);
    }

    public function setUuid(string $uuid)
    {
        $this->uuid = $uuid;
    }

    public function getUuid()
    {
        return $this->uuid;
    }

    public function setStatus(string $status)
    {
        $this->status = $status;
    }

    public function getStatus()
    {
        return $this->status;
    }

    public function setDeleted(string $deleted)
    {
        $this->deleted = $deleted;
    }

    public function getDeleted()
    {
        return $this->deleted;
    }
}
