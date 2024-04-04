<?php
namespace Source\Domain\Model;

use Exception;
use Ramsey\Uuid\Nonstandard\Uuid;
use Source\Core\Connect;
use Source\Models\Subscription as ModelsSubscription;

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

    /**
     * Subscription constructor
     */
    public function __construct()
    {
        $this->subscription = new ModelsSubscription();    
    }

    public function getId()
    {
        if (empty($this->id)) {
            throw new Exception("id não atribuido");;
        }
        return $this->id;
    }

    public function setId(int $id)
    {
        $this->id = $id;
    }

    public function persistData(array $data)
    {
        if (empty($data)) {
            return json_encode(["invalid_persist_data" => "dados inválidos"]);
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
            }else {
                $value = $value;
            }
            $this->subscription->$key = $value;
        }

        $this->subscription->save();
        $this->setId(Connect::getInstance()->lastInsertId());
        return true;
    }
}
