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
            $event = Event::constructFrom(json_decode($payload));
        } catch (UnexpectedValueException $e) {
            throw new Exception($e->getMessage());
        }

        if ($event->type == "invoice.updated") {
            if (!empty($event->data->object->lines->data)) {
                
                foreach ($event->data->object->lines->data as $value) {
                    $subscription = new Subscription();
                    
                    $subscription->subscription_id = $value->id;
                    $subscriptionData = $subscription->findSubsCriptionBySubscriptionId([]);
                    if (!empty($subscriptionData)) {
                        $response = $subscription->updateSubscriptionBySubscriptionId([
                            "subscription_id" => $value->id,
                            "period_start" => date("Y-m-d", $value->period->start),
                            "period_end" => date("Y-m-d", $value->period->end),
                        ]);

                        if (empty($response)) {
                            throw new Exception($subscription->message->json());
                        }
                    }
                }
            }
        }
    }
}