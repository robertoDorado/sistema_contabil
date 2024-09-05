<?php
namespace Source\Models;

use Source\Core\Model;

/**
 * SupportResponse Models
 * @link 
 * @author Roberto Dorado <robertodorado7@gmail.com>
 * @package Source\Models
 */
class SupportResponse extends Model
{
    /** @var string Uuid */
    protected string $uuid = "uuid";

    /** @var string id dos tickets chave estrangeira */
    protected string $idSupportTickets = "id_support_tickets";

    /** @var string id suporte */
    protected string $idSupport = "id_support";

    /** @var string Conteúdo da mensagem */
    protected string $contentMessage = "content_message";

    /** @var string anexo */
    protected string $contentAttachment = "content_attachment";

    /** @var string deleted */
    protected string $deleted = "deleted";

    /** @var string datetime */
    protected string $createdAt = "created_at";

    /** @var string updatedAt datetime */
    protected string $updatedAt = "updated_at";

    /**
     * SupportResponse constructor
     */
    public function __construct()
    {
        parent::__construct(CONF_DB_NAME . ".support_response", ["id"], [
            $this->uuid,
            $this->idSupportTickets,
            $this->contentMessage,
            $this->contentAttachMent,
            $this->deleted,
            $this->createdAt,
            $this->updatedAt
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

    public function setDeleted(string $deleted)
    {
        $this->deleted = $deleted;
    }

    public function getDeleted()
    {
        return $this->deleted;
    }
}
