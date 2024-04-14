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
        $this->stripeClient = new StripeClient(STRIPE_TEST_SECRET_KEY);
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

        }catch (CardException $e) {
            $message->error("Erro no cartão de crédito: " . $e->getError()->message);
            $this->data->message = $message;
            return null;
        } catch (InvalidRequestException $e) {
            throw new Exception("Requisição inválida: " . $e->getError()->message);
        } catch (AuthenticationException $e) {
            throw new Exception("Erro na autenticação: " . $e->getError()->message);
        } catch (ApiConnectionException $e) {
           throw new Exception("Erro na conexão com a api: " . $e->getError()->message);
        } catch (ApiErrorException $e) {
            $message->error("Erro no cartão de crédito: " . $e->getError()->message);
            $this->data->message = $message;
            return null;
            // throw new Exception("Erro na api: " . $e->getError()->message);
        } catch (Exception $e) {
            throw new Exception($e->getMessage());
        }
    }

    public function createPrice(array $requestData): void
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

    public function createProduct(array $requestData): void
    {
        validateRequestData(["name", "description"], $requestData);
        $this->product = $this->stripeClient->products->create($requestData);
    }

    public function createCustomer(array $requestData): void
    {
        validateRequestData(["id", "name", "email", "source"], $requestData);
        $this->customer = $this->stripeClient->customers->create($requestData);
    }
}
