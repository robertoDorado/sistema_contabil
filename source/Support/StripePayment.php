<?php

namespace Source\Support;

use Exception;
use Stripe\Customer;
use Stripe\Exception\ApiConnectionException;
use Stripe\Exception\ApiErrorException;
use Stripe\Exception\AuthenticationException;
use Stripe\Exception\CardException;
use Stripe\Exception\OAuth\InvalidRequestException;
use Stripe\Price;
use Stripe\Product;
use Stripe\StripeClient;
use Stripe\Subscription;

/**
 * StripePayment Support
 * @link 
 * @author Roberto Dorado <robertodorado7@gmail.com>
 * @package Source\Support
 */
class StripePayment
{
    /** @var StripeClient StripeClient */
    private StripeClient $stripeClient;

    /** @var Product Produto */
    public Product $product;

    /** @var Price Preço */
    public Price $price;

    /** @var Customer Cliente */
    public Customer $customer;

    /** @var object $data */
    private object $data;

    /**
     * StripePayment constructor
     */
    public function __construct()
    {
        $this->stripeClient = new StripeClient(STRIPE_SECRET_KEY);
        $this->data = new \stdClass();
    }

    public function __set($name, $value)
    {
        $this->data->$name = $value;
    }

    public function __get($name)
    {
        return $this->data->$name ?? null;
    }

    public function cancelSubscription(string $subscriptionId): ?Subscription
    {
        $message = new Message();
        try {
            if (empty($subscriptionId)) {
                throw new Exception("id da assinatura não pode estar vazio");
            }

            if (!preg_match("/^sub_/", $subscriptionId)) {
                throw new Exception("Id da assinatura inválido");
            }

            $response = $this->stripeClient->subscriptions->update($subscriptionId, [
                'cancel_at_period_end' => true
            ]);

            return $response;
        } catch (ApiErrorException $e) {
            $message->error("Erro interno no servidor: " . $e->getError()->message);
            $this->data->message = $message;
            return null;
        }
    }

    public function createSubscription(array $requestData): ?Subscription
    {
        $message = new Message();
        try {
            if (empty($this->customer)) {
                throw new Exception("objeto cliente não pode estar vazio");
            }

            if (empty($this->price)) {
                throw new Exception("objeto preço não pode estar vazio");
            }

            if (empty($requestData["customer"])) {
                $requestData["customer"] = $this->customer->id;
            }

            if (empty($requestData["items"])) {
                $requestData["items"] = [["price" => $this->price->id]];
            }

            validateRequestData(["customer", "items", "expand"], $requestData);
            $subscription = $this->stripeClient->subscriptions->create($requestData);
            return $subscription;
        } catch (ApiErrorException $e) {
            $message->error("Erro interno no servidor: " . $e->getError()->message);
            $this->data->message = $message;
            return null;
        }
    }

    public function createPrice(array $requestData): bool
    {
        $message = new Message();
        try {
            if (empty($this->product)) {
                throw new Exception("objeto produto não pode estar vazio");
            }

            if (empty($requestData["product"])) {
                $requestData["product"] = $this->product->id;
            }

            validateRequestData(["currency", "unit_amount", "recurring", "product"], $requestData);
            $this->price = $this->stripeClient->prices->create($requestData);
            return true;
        } catch (ApiErrorException $e) {
            $message->error("Erro interno no servidor: " . $e->getError()->message);
            $this->data->message = $message;
            return false;
        }
    }

    public function createProduct(array $requestData): bool
    {
        $message = new Message();
        try {
            validateRequestData(["name", "description"], $requestData);
            $this->product = $this->stripeClient->products->create($requestData);
            return true;
        } catch (ApiErrorException $e) {
            $message->error("Erro interno no servidor: " . $e->getError()->message);
            $this->data->message = $message;
            return false;
        }
    }

    public function createCustomer(array $requestData): bool
    {
        $message = new Message();
        try {
            validateRequestData(["name", "email", "source"], $requestData);
            $this->customer = $this->stripeClient->customers->create($requestData);
            return true;
        } catch (CardException $e) {
            $message->error("Erro ao processar o cartão de crédito: " . $e->getError()->message);
            $this->data->message = $message;
            return false;
        } catch (ApiErrorException $e) {
            $message->error("Erro interno no servidor: " . $e->getError()->message);
            $this->data->message = $message;
            return false;
        }
    }
}
