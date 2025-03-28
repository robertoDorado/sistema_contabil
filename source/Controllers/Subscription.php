<?php

namespace Source\Controllers;

use Exception;
use Ramsey\Uuid\Nonstandard\Uuid;
use Source\Core\Controller;
use Source\Domain\Model\Customer;
use Source\Domain\Model\Subscription as ModelSubscription;
use Source\Domain\Model\SubscriptionCancellation;
use Source\Domain\Model\User;
use Source\Support\StripePayment;

/**
 * Subscription Controllers
 * @link 
 * @author Roberto Dorado <robertodorado7@gmail.com>
 * @package Source\Controllers
 */
class Subscription extends Controller
{
    /**
     * Subscription constructor
     */
    public function __construct()
    {
        parent::__construct();
    }

    public function cancelSubscription()
    {
        verifyRequestHttpOrigin($this->getServer()->getServerByKey("HTTP_ORIGIN"));
        if (empty(session()->user)) {
            throw new Exception("usuário não está logado");
        }

        $postData = $this->getRequests()->setRequiredFields(
            [
                "csrfToken",
                "cancelSubscriptionValue",
                "cancelData"
            ]
        )->getAllPostData();
        $verifyPostData = array_filter($postData, function ($item) {
            return empty($item);
        });

        if (!empty($verifyPostData)) {
            http_response_code(400);
            echo json_encode(["error" => "dados do formulário inválido"]);
            die;
        }

        if (empty($postData["cancelData"])) {
            http_response_code(400);
            echo json_encode(["error" => "não foi possível cancelar a assinatura"]);
            die;
        }

        if (strlen($postData["cancelSubscriptionValue"]) > 1000) {
            http_response_code(400);
            echo json_encode(["error" => "limite de caracteres inválido"]);
            die;
        }

        $customer = new Customer();
        $customer->email = session()->user->user_email;
        $customerData = $customer->findCustomerByEmail();

        if (empty($customerData)) {
            echo $customer->message->json();
            die;
        }

        $subscription = new ModelSubscription();
        $subscription->customer_id = session()->user->id_customer;
        $subscriptionData = $subscription->findSubsCriptionByCustomerId(["subscription_id", "status"]);

        if (empty($subscriptionData)) {
            echo $subscription->message->json();
            die;
        }

        $stripePayment = new StripePayment();
        $response = $stripePayment->cancelSubscription($subscriptionData->subscription_id);

        if (!empty($response)) {
            $subscription = new ModelSubscription();
            $subscription->subscription_id = $response->id;
            $subscriptionData = $subscription->findSubsCriptionBySubscriptionId(["id"]);

            if (empty($subscriptionData)) {
                http_response_code(400);
                echo $subscription->message->json();
                die;
            }

            $customer = new Customer();
            $customer->setId(session()->user->id_customer);

            $subscriptionCancellation = new SubscriptionCancellation();
            $responsePersistData = $subscriptionCancellation->persistData([
                "uuid" => Uuid::uuid4(),
                "id_customer" => $customer,
                "cancellation_reason" => $postData["cancelSubscriptionValue"],
                "created_at" => date("Y-m-d"),
                "updated_at" => date("Y-m-d"),
                "deleted" => 0
            ]);

            if (empty($responsePersistData)) {
                http_response_code(400);
                echo $subscriptionCancellation->message->json();
                die;
            }

            echo json_encode(["success" => "assinatura cancelada com sucesso"]);
        } else {
            echo $stripePayment->message->json();
        }
    }

    public function processSubscription()
    {
        try {
            verifyRequestHttpOrigin($this->getServer()->getServerByKey("HTTP_ORIGIN"));
            $requestPost = $this->getRequests()
                ->setRequiredFields([
                    "fullName",
                    "document",
                    "birthDate",
                    "gender",
                    "email",
                    "zipcode",
                    "address",
                    "number",
                    "neighborhood",
                    "city",
                    "state",
                    "userName",
                    "password",
                    "confirmPassword",
                    "csrfToken",
                    "cardToken"
                ])->getAllPostData();

            $errorMessage = fn(string $message) => json_encode(['error' => $message]);
            if (!preg_match("/^[A-Z]{2}$/", $requestPost["state"])) {
                echo $errorMessage("estado inválido");
                die;
            }

            $verifyZipcode = preg_replace("/[^\d]+/", "", $requestPost["zipcode"]);
            if (strlen($verifyZipcode) > 8) {
                echo $errorMessage("cep inválido");
                die;
            }

            $verifyDocument = preg_replace("/[^\d]+/", "", $requestPost["document"]);
            if (strlen($verifyDocument) > 14) {
                echo $errorMessage("documento inválido");
                die;
            }

            if ($requestPost["password"] != $requestPost["confirmPassword"]) {
                echo $errorMessage("as senhas não conferem");
                die;
            }

            if (!empty($requestPost['free_days'])) {
                if ($requestPost['free_days'] > 7) {
                    echo $errorMessage("período gratuito não autorizado");
                    die;
                }
            }

            if (!empty($requestPost['period'])) {
                $checkIntervalPeriod = ["month", "year", "week", "day"];
                if (!in_array($requestPost['period'], $checkIntervalPeriod)) {
                    echo $errorMessage("período inválido");
                    die;
                }
            }

            if (!empty($requestPost['period']) && !empty($requestPost['value'])) {
                $value = formatStripePriceInFloatValue(true, $requestPost['value']);
                if ($requestPost['period'] === 'year' && $value < 1200) {
                    echo $errorMessage("valor anual não autorizado");
                    die;
                }
            }
            
            if (!empty($requestPost['value'])) {
                $value = formatStripePriceInFloatValue(true, $requestPost['value']);
                if ($value < DEFAULT_PRICE_VALUE) {
                    echo $errorMessage("valor da mensalidade não autorizado.");
                    die;
                }
            }

            $customer = new Customer();
            $customer->email = $requestPost["email"];
            $customerData = $customer->findCustomerByEmail();

            $user = new User();
            $user->setEmail($requestPost["email"]);
            $userData = $user->findUserByEmail();

            $requestUserData = [
                "uuid" => Uuid::uuid4(),
                "user_full_name" => $requestPost["fullName"],
                "user_nick_name" => $requestPost["userName"],
                "user_email" => $requestPost["email"],
                "user_password" => password_hash($requestPost["confirmPassword"], PASSWORD_DEFAULT),
                "deleted" => 0
            ];

            $customerUuid = Uuid::uuid4();
            $requestCustomerData = [
                "uuid" => $customerUuid,
                "customer_name" => $requestPost["fullName"],
                "customer_document" => $requestPost["document"],
                "birth_date" => date("Y-m-d", strtotime(str_replace("/", "-", $requestPost["birthDate"]))),
                "customer_gender" => $requestPost["gender"],
                "customer_email" => $requestPost["email"],
                "customer_zipcode" => $requestPost["zipcode"],
                "customer_address" => $requestPost["address"],
                "customer_number" => $requestPost["number"],
                "customer_neighborhood" => $requestPost["neighborhood"],
                "customer_city" => $requestPost["city"],
                "customer_state" => $requestPost["state"],
                "customer_phone" => empty($requestPost["phone"]) ? null : $requestPost["phone"],
                "cell_phone" => empty($requestPost["cellPhone"]) ? null : $requestPost["cellPhone"],
                "created_at" => date("Y-m-d"),
                "updated_at" => date("Y-m-d"),
                "deleted" => 0,
            ];

            $customerId = 0;
            if (empty($customerData)) {
                $customer = new Customer();
                $response = $customer->persistData($requestCustomerData);
                $customerId = $customer->getId();

                if (empty($response)) {
                    echo $customer->message->json();
                    die;
                }

                $user = new User();
                $requestUserData["id_customer"] = $customer;
                $response = $user->persistData($requestUserData);

                if (empty($response)) {
                    echo $user->message->json();
                    die;
                }
            } else {
                $unsetFields = ["uuid", "created_at", "deleted"];
                foreach ($unsetFields as $key) {
                    unset($requestCustomerData[$key]);
                }

                $requestCustomerData["customer_email"] = $customerData->customer_email;
                $customer = new Customer();
                $customerId = $customerData->id;

                $customer->setId($customerData->id);
                $response = $customer->updateCustomerByEmail($requestCustomerData);

                if (empty($response)) {
                    echo $customer->message->json();
                    die;
                }

                $customerUuid = $customerData->getUuid();
                $requestUserData["id_customer"] = $customer;

                $user->setEmail($userData->user_email);
                $response = $user->updateUserByEmail($requestUserData);

                if (empty($response)) {
                    echo $user->message->json();
                    die;
                }
            }

            $stripePayment = new StripePayment();
            $response = $stripePayment->createCustomer([
                "name" => $requestPost["fullName"],
                "email" => $requestPost["email"],
                "source" => $requestPost["cardToken"]
            ]);

            if (empty($response)) {
                echo $stripePayment->message->json();
                die;
            }

            $response = $stripePayment->createProduct([
                "name" => "sistema_contabil premium",
                "description" => "Assinatura premium do Sistema Contábil. 
                Projetada para atender às demandas mais exigentes de empresas 
                e profissionais contábeis, esta assinatura representa o ápice da inovação, 
                confiabilidade e eficiência no mundo da contabilidade."
            ]);

            if (empty($response)) {
                echo $stripePayment->message->json();
                die;
            }

            $requestPost['value'] = formatStripePriceInFloatValue(false, $requestPost['value'] ?? "");
            if (empty($requestPost['value'])) {
                $requestPost['value'] = DEFAULT_PRICE_VALUE;
            }

            $requestPost['period'] = formatStripeIntervalPeriod(false, $requestPost['period'] ?? "");
            if (empty($requestPost['period'])) {
                $requestPost['period'] = ["interval" => DEFAULT_PERIOD];
            }

            $response = $stripePayment->createPrice([
                "currency" => "BRL",
                "unit_amount" => $requestPost['value'],
                "recurring" => $requestPost['period'],
                "product" => $stripePayment->product->id
            ]);

            if (empty($response)) {
                echo $stripePayment->message->json();
                die;
            }

            $subscriptionParams = [
                "customer" => $stripePayment->customer->id,
                "items" => [["price" => $stripePayment->price->id]],
                "expand" => ["latest_invoice.payment_intent"]
            ];

            if (!empty($requestPost['free_days'])) {
                if (!preg_match("/^\d+$/", $requestPost['free_days'])) {
                    $requestPost['free_days'] = 7;
                }
                $subscriptionParams['trial_period_days'] = $requestPost['free_days'];
            }

            $response = $stripePayment->createSubscription($subscriptionParams);
            if (empty($response)) {
                echo $stripePayment->message->json();
                die;
            }

            $checkStatus = ["active", "trialing"];
            if (in_array($response->status, $checkStatus)) {
                $subscription = new ModelSubscription();

                if (!empty($response->latest_invoice->lines->data)) {
                    $customer = new Customer();
                    $customer->setId($customerId);

                    foreach ($response->latest_invoice->lines->data as $value) {
                        $response = $subscription->persistData([
                            "uuid" => Uuid::uuid4(),
                            "subscription_id" => $response->id,
                            "customer_id" => $customer,
                            "charge_id" => $response->latest_invoice->charge ?? "ch_" . uniqid(),
                            "product_description" => $value->description,
                            "period_end" => date("Y-m-d", $value->period->end),
                            "period_start" => date("Y-m-d", $value->period->start),
                            "created_at" => date("Y-m-d"),
                            "updated_at" => date("Y-m-d"),
                            "status" => $response->status
                        ]);

                        if (empty($response)) {
                            echo $subscription->message->json();
                            die;
                        }
                    }
                }
                echo json_encode([
                    "success" => true,
                    "url" => url("/customer/subscription/thanks-purchase")
                ]);
            }
        } catch (Exception $e) {
            echo json_encode(['error' => 'erro ao processar o pagamento: ' . $e->getMessage()]);
        }
    }
}
