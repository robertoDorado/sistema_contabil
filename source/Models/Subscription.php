<?php
namespace Source\Models;

use Source\Core\Model;

/**
 * Subscription Models
 * @link 
 * @author Roberto Dorado <robertodorado7@gmail.com>
 * @package Source\Models
 */
class Subscription extends Model
{
    /** @var string Uuid de identificação */
    protected string $uuid = "uuid";

    /** @var  string Id da assinatura */
    protected string $subscriptionId = "subscription_id";

    /** @var string Id do cliente */
    protected string $customerId = "customer_id";

    /** @var string Id da transação */
    protected string $chargeId = "charge_id";

    /** @var string Descrição do produto de aquisição */
    protected string $productDescription = "product_description";

    /** @var string Data de encerramento do ciclo da assinatura */
    protected string $periodEnd = "period_end";

    /** @var string Data de inicio do ciclo da assinatura */
    protected string $periodStart = "period_start";

    /** @var string Criado em */
    protected string $createdAt = "created_at";

    /** @var string Atualizado em */
    protected string $updatedAt = "updated_at";

    /** @var string status da assinatura */
    protected string $status = "status";

    /**
     * Subscription constructor
     */
    public function __construct()
    {
        parent::__construct(CONF_DB_NAME . ".subscription", ["id"], [
            $this->uuid,
            $this->subscriptionId,
            $this->customerId,
            $this->chargeId,
            $this->productDescription,
            $this->periodEnd,
            $this->periodStart,
            $this->createdAt,
            $this->updatedAt,
            $this->status
        ]);
    }

    public function getStatus()
    {
        return $this->status;
    }

    public function setStatus(string $status)
    {
        $this->status = $status;
    }

    public function setUuid(string $uuid)
    {
        $this->uuid = $uuid;
    }

    public function getUuid()
    {
        return $this->uuid;
    }
}
