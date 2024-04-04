<?php
namespace Source\Support;

use Exception;
use Stripe\Customer;
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
    private Product $product;

    /** @var Price Preço */
    private Price $price;

    /** @var Customer Cliente */
    private Customer $customer;

    /**
     * StripePayment constructor
     */
    public function __construct()
    {
        $this->stripeClient = new StripeClient(STRIPE_TEST_SECRET_KEY);
    }

    public function cancelSubscription(string $subscriptionId): Subscription
    {
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
    }

    public function createSubscription(array $requestData): Subscription
    {
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
    }

    public function createPrice(array $requestData)
    {
        if (empty($this->product)) {
            throw new Exception("objeto produto não pode estar vazio");
        }

        if (empty($requestData["product"])) {
            $requestData["product"] = $this->product->id;
        }

        validateRequestData(["currency", "unit_amount", "recurring", "product"], $requestData);
        $this->price = $this->stripeClient->prices->create($requestData);
    }

    public function createProduct(array $requestData)
    {
        validateRequestData(["name", "description"], $requestData);
        $this->product = $this->stripeClient->products->create($requestData);
    }

    public function createCustomer(array $requestData)
    {
        validateRequestData(["id", "name", "email", "source"], $requestData);
        $this->customer = $this->stripeClient->customers->create($requestData);
    }
}
