<?php
namespace Source\Domain\Model;

use Exception;
use Ramsey\Uuid\Nonstandard\Uuid;
use Source\Core\Connect;
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
        $message = new Message();
        if (empty($data)) {
            $message->error("parametro data não pode ser vazio");
            $this->data->message = $message;
            return false;
        }

        $subscriptionData = $this->subscription
        ->find("subscription_id=:subscription_id", ":subscription_id={$data['subscription_id']}")
        ->fetch();

        if (empty($subscriptionData)) {
            $message->error("assinatura não localizada");
            $this->data->message = $message;
            return false;
        }

        $verifyKeys = [
            "subscription_id" => function($value) {
                if (!preg_match("/^sub_/", $value)) {
                    throw new Exception("id da assinatura inválida");
                }
                return $value;
            }
        ];

        foreach ($data as $key => &$value) {
            if (!empty($verifyKeys[$key])) {
                $value = $verifyKeys[$key]($value);
            }
            $subscriptionData->$key = $value;
        }

        $subscriptionData->setRequiredFields(array_keys($data));
        return $subscriptionData->save();
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
        $message = new Message();
        if (empty($data)) {
            $message->error("dados inválidos");
            $this->data->message = $message;
            return false;
        }

        validateModelProperties(ModelsSubscription::class, $data);
        $verifyKeys = [
            "customer_id" => function($value) {
                if (!$value instanceof Customer) {
                    throw new Exception("instância do cliente está incorreta");
                }
                return $value->getId();
            },
            "uuid" => function($value) {
                if (!Uuid::isValid($value)) {
                    throw new Exception("uuid inválido");
                }
                return $value;
            },
            "subscription_id" => function($value) {
                if (!preg_match("/^sub_/", $value)) {
                    throw new Exception("subscription_id inválido");
                }
                return $value;
            },
            "charge_id" => function($value) {
                if (!preg_match("/^ch_/", $value)) {
                    throw new Exception("charge_id inválido");
                }
                return $value;
            }
        ];

        foreach ($data as $key => &$value) {
            if (!empty($verifyKeys[$key])) {
                $value = $verifyKeys[$key]($value);
            }
            $this->subscription->$key = $value;
        }

        $this->subscription->save();
        $this->setId(Connect::getInstance()->lastInsertId());
        return true;
    }
}
