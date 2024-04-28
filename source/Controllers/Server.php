<?php
namespace Source\Controllers;

use Exception;
use Source\Core\Controller;
use Source\Domain\Model\Subscription;
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
            $subscription = new Subscription();
                    
            $subscription->subscription_id = $event->data->object->id;
            $subscriptionData = $subscription->findSubsCriptionBySubscriptionId([]);

            if (!empty($subscriptionData)) {
                $subscription->updateSubscriptionBySubscriptionId([
                    "subscription_id" => $event->data->object->id,
                    "status" => "canceled",
                    "updated_at" => date("Y-m-d", strtotime($event->data->object->canceled_at))
                ]);
            }
        }
    }
}