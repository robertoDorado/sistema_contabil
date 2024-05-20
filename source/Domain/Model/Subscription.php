<?php
namespace Source\Domain\Model;

use Exception;
use Source\Domain\Support\Tools;
use Source\Models\Subscription as ModelsSubscription;
use Source\Support\Message;

/**
 * Subscription Domain\Model
 * @link 
 * @author Roberto Dorado <robertodorado7@gmail.com>
 * @package Source\Domain\Model
 */
class Subscription
{
    /** @var ModelsSubscription Model da assinatura */
    private ModelsSubscription $subscription;

    /** @var int Id */
    private int $id;

    /** @var object */
    private object $data;

    /**
     * Subscription constructor
     */
    public function __construct()
    {
        $this->subscription = new ModelsSubscription();
        $this->data = new \stdClass();
    }

    public function __get($name)
    {
        return $this->data->$name ?? null;
    }

    public function __set($name, $value)
    {
        $this->data->$name = $value;
    }

    public function updateSubscriptionBySubscriptionId(array $data) : bool
    {
        $tools = new Tools($this->subscription, ModelsSubscription::class);
        $response = $tools->updateData("subscription_id=:subscription_id",
        ":subscription_id={$data['subscription_id']}", $data, "assinatura não localizada");
        $this->data->message = !empty($tools->message) ? $tools->message : "";
        return !empty($response) ? true : false;
    }

    public function findSubsCriptionBySubscriptionId(array $columns): ?ModelsSubscription
    {
        $columns = empty($columns) ? "*" : implode(", ", $columns);
        $subscriptionId = empty($this->data->subscription_id) ? 0 : $this->data->subscription_id;
        
        $subscriptionData = $this->subscription
        ->find("subscription_id=:subscription_id AND status=:status", ":subscription_id={$subscriptionId}&status=active", $columns)
        ->fetch();

        $message = new Message();
        if (empty($subscriptionData)) {
            $message->error("assinatura não encontrada");
            $this->data->message = $message;
            return null;
        }

        return $subscriptionData;
    }

    public function findSubsCriptionByCustomerId(array $columns): ?ModelsSubscription
    {
        $columns = empty($columns) ? "*" : implode(", ", $columns);
        $customerId = empty($this->data->customer_id) ? 0 : $this->data->customer_id;
        
        $subscriptionData = $this->subscription
        ->find("customer_id=:customer_id AND status=:status", ":customer_id={$customerId}&:status=active", $columns)
        ->fetch();

        $message = new Message();
        if (empty($subscriptionData)) {
            $message->error("assinatura não encontrada");
            $this->data->message = $message;
            return null;
        }

        return $subscriptionData;
    }

    public function getId(): int
    {
        if (empty($this->id)) {
            throw new Exception("id não atribuido");
        }
        return $this->id;
    }

    public function setId(int $id): void
    {
        $this->id = $id;
    }

    public function persistData(array $data): bool
    {
        $tools = new Tools($this->subscription, ModelsSubscription::class);
        $response = $tools->persistData($data);
        $this->data->message = !empty($tools->message) ? $tools->message : "";

        !empty($response) ? $this->setId($tools->lastId) : null;
        return !empty($response) ? true : false;
    }
}
