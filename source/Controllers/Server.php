<?php

namespace Source\Controllers;

use DateTime;
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

        $validateStripeEvent = [
            "invoice.finalized" => function (Event $event) {
                $id = $event->object->lines->data[0]->parent->subscription_item_details->subscription;
                $subscription = new Subscription();

                $subscription->subscription_id = $id;
                $subscriptionData = $subscription->findSubsCriptionBySubscriptionId([]);

                if (empty($subscriptionData)) {
                    throw new Exception($subscription->message->json() . json_encode(["subscription_id" => $id]));
                }

                $dateTimePeriodStart = new DateTime();
                $dateTimePeriodStart->setTimestamp($event->data->object->lines->data[0]->period->start);

                $dateTimePeriodEnd = new DateTime();
                $dateTimePeriodEnd->setTimestamp($event->data->object->lines->data[0]->period->end);

                $subscription = new Subscription();
                $response = $subscription->updateSubscriptionBySubscriptionId([
                    "subscription_id" => $id,
                    "price_value" => formatStripePriceInFloatValue(true, $event->data->object->lines->data[0]->amount),
                    "charge_id" => $event->data->object->charge,
                    "product_description" => $event->data->object->lines->data[0]->description,
                    "updated_at" => $dateTimePeriodStart->format("Y-m-d"),
                    "period_start" => $dateTimePeriodStart->format("Y-m-d"),
                    "period_end" => $dateTimePeriodEnd->format("Y-m-d"),
                    "status" => "active"
                ]);

                if (empty($response)) {
                    throw new Exception($subscription->message->json() . json_encode(["subscription_id" => $id]));
                }
            },
            "customer.subscription.deleted" => function (Event $event) {
                $id = $event->data->object->id;
                $subscription = new Subscription();

                $subscription->subscription_id = $id;
                $subscriptionData = $subscription->findSubsCriptionBySubscriptionId([]);

                if (empty($subscriptionData)) {
                    throw new Exception($subscription->message->json() . json_encode(["subscription_id" => $id]));
                }

                $dateTime = new DateTime();
                $dateTime->setTimestamp($event->data->object->canceled_at);

                $subscription = new Subscription();
                $response = $subscription->updateSubscriptionBySubscriptionId([
                    "subscription_id" => $id,
                    "status" => "canceled",
                    "updated_at" => $dateTime->format("Y-m-d")
                ]);

                if (empty($response)) {
                    throw new Exception($subscription->message->json() . json_encode(["subscription_id" => $id]));
                }

                $response = file_put_contents(CONF_SUBSCRIPTION_CANCELED_PATH, json_encode($subscriptionData->data())  . PHP_EOL, FILE_APPEND);
                if (!$response) {
                    throw new Exception("Não foi possível criar o arquivo " . CONF_SUBSCRIPTION_CANCELED_PATH . "");
                }
            },

            "invoice.payment_succeeded" => function(Event $event) {
                $id = $event->data->object->charge;
                $subscription = new Subscription();
                
                $subscription->charge_id = $id;
                $subscriptionData = $subscription->findSubscriptionByChargeId(["customer_id"]);

                if (empty($subscriptionData)) {
                    throw new Exception("assinatura não encontrada");
                }

                $customer = new Customer();
                $customer->setId($subscriptionData->customer_id);
                $response = $customer->updateCustomerById([
                    "id" => $subscriptionData->customer_id,
                    "deleted" => 0
                ]);

                if (empty($response)) {
                    throw new Exception($customer->message->json() . json_encode(["subscription_id" => $id]));
                }

                $user = new User();
                $response = $user->updateUserByCustomerId([
                    "id_customer" => $customer,
                    "deleted" => 0
                ]);

                if (empty($response)) {
                    throw new Exception($user->message->json() . json_encode(["subscription_id" => $id]));
                }

                $dateTimePeriodStart = new DateTime();
                $dateTimePeriodStart->setTimestamp($event->data->object->lines->data[0]->period->start);

                $dateTimePeriodEnd = new DateTime();
                $dateTimePeriodEnd->setTimestamp($event->data->object->lines->data[0]->period->end);

                $subscription = new Subscription();
                $response = $subscription->updateSubscriptionByChargeId([
                    "charge_id" => $id,
                    "updated_at" => $dateTimePeriodStart->format("Y-m-d"),
                    "period_start" => $dateTimePeriodStart->format("Y-m-d"),
                    "period_end" => $dateTimePeriodEnd->format("Y-m-d"),
                    "status" => "active"
                ]);

                if (empty($response)) {
                    throw new Exception("não foi possível atualizar a data de renovação da fatura");
                }
            }
        ];

        if (!empty($validateStripeEvent[$event->type])) {
            $validateStripeEvent[$event->type]($event);
        }
    }
}
