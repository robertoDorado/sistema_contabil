<?php
namespace Source\Models;

use Source\Core\Model;

/**
 * SubscriptionCancellation Models
 * @link 
 * @author Roberto Dorado <robertodorado7@gmail.com>
 * @package Source\Models
 */
class SubscriptionCancellation extends Model
{
    /** @var string uuid */
    protected string $uuid = "uuid";
    
    /** @var string id do cliente */
    protected string $idCustomer = "id_customer";

    /** @var string motivo do cancelamento */
    protected string $cancellationReason = "cancellation_reason";

    /** @var string Data criada */
    protected string $createdAt = "created_at";

    /** @var string Data de atualização */
    protected string $updatedAt = "updated_at";

    /** @var string Soft delete */
    protected string $deleted = "deleted";

    /**
     * SubscriptionCancellation constructor
     */
    public function __construct()
    {
        parent::__construct(CONF_DB_NAME . ".subscription_cancellation", ["id"], [
            $this->uuid,
            $this->idCustomer,
            $this->cancellationReason,
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
