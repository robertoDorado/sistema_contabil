<?php
namespace Source\Controllers;

use Exception;
use Source\Core\Controller;
use Source\Domain\Model\Customer;
use Source\Domain\Model\Subscription;
use Source\Domain\Model\User;
use Stripe\Event;
use UnexpectedValueException;

/**
 * Server Controllers
 * @link 
 * @author Roberto Dorado <robertodorado7@gmail.com>
 * @package Source\Controllers
 */
class Server extends Controller
{
    /**
     * Server constructor
     */
    public function __construct()
    {
        parent::__construct();
    }

    public function webhookUpdateSubscription()
    {
        $payload = @file_get_contents("php://input");
        $event = null;
        
        try {
            $event = Event::constructFrom(json_decode($payload, true));
        } catch (UnexpectedValueException $e) {
            throw new Exception($e->getMessage());
        }

        if ($event->type == "customer.subscription.deleted") {
            $id = $event->data->object->id;
            $subscription = new Subscription();
            
            $subscription->subscription_id = $id;
            $subscriptionData = $subscription->findSubsCriptionBySubscriptionId([]);
            
            if (empty($subscriptionData)) {
                throw new Exception($subscription->message->json() . json_encode(["subscription_id" => $id]));
            }
            
            $subscription = new Subscription();
            $response = $subscription->updateSubscriptionBySubscriptionId([
                "subscription_id" => $id,
                "status" => "canceled",
                "updated_at" => date("Y-m-d", strtotime($event->data->object->canceled_at))
            ]);

            if (empty($response)) {
                throw new Exception($subscription->message->json() . json_encode(["subscription_id" => $id]));
            }
            
            $customer = new Customer();
            $customer->setId($subscriptionData->customer_id);
            $response = $customer->updateCustomerById([
                "id" => $subscriptionData->customer_id,
                "deleted" => 1
            ]);

            if (empty($response)) {
                throw new Exception($customer->message->json() . json_encode(["subscription_id" => $id]));
            }

            $user = new User();
            $response = $user->updateUserByCustomerId([
                "id_customer" => $customer,
                "deleted" => 1
            ]);

            if (empty($response)) {
                throw new Exception($user->message->json() . json_encode(["subscription_id" => $id]));
            }

            $response = file_put_contents(CONF_SUBSCRIPTION_CANCELED_PATH, json_encode($subscriptionData->data())  . PHP_EOL, FILE_APPEND);
            if (!$response) {
                throw new Exception("Não foi possível criar o arquivo " . CONF_SUBSCRIPTION_CANCELED_PATH . "");
            }
        }
    }
}