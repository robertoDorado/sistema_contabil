<?php
namespace Source\Controllers;

use Exception;
use Source\Core\Controller;
use Source\Domain\Model\Customer as ModelCustomer;
use Source\Domain\Model\User;

/**
 * Customer Controllers
 * @link 
 * @author Roberto Dorado <robertodorado7@gmail.com>
 * @package Source\Controllers
 */
class Customer extends Controller
{
    /**
     * Customer constructor
     */
    public function __construct()
    {
        parent::__construct();
    }

    public function updateDataCustomerForm()
    {
        if (empty(session()->user)) {
            redirect("/admin/login");
        }
        
        if ($this->getServer()->getServerByKey("REQUEST_METHOD") == "POST") {
            $requestPost = $this->getRequests()->setRequiredFields([
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
                "csrfToken"
            ])->getAllPostData();

            if (!preg_match("/^[A-Z]{2}$/", $requestPost["state"])) {
                throw new Exception("estado inválido");
            }
    
            $verifyZipcode = preg_replace("/[^\d]+/", "", $requestPost["zipcode"]);
            if (strlen($verifyZipcode) > 8) {
                throw new Exception("cep inválido");
            }
    
            $verifyDocument = preg_replace("/[^\d]+/", "", $requestPost["document"]);
            if (strlen($verifyDocument) > 14) {
                throw new Exception("documento inválido");
            }
            
            if ($requestPost["password"] != $requestPost["confirmPassword"]) {
                throw new Exception("as senhas não conferem");
            }

            $customer = new ModelCustomer();
            $customer->setId(session()->user->id_customer);
            $response = $customer->updateCustomerById([
                "id" => session()->user->id_customer,
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
                "customer_phone" => $requestPost["phone"],
                "cell_phone" => $requestPost["cellPhone"],
                "updated_at" => date("Y-m-d")
            ]);

            if (empty($response)) {
                echo $customer->message->json();
                die;
            }

            $user = new User();
            $response = $user->updateUserByCustomerId([
                "id_customer" => $customer,
                "user_full_name" => $requestPost["fullName"],
                "user_nick_name" => $requestPost["userName"],
                "user_email" => $requestPost["email"],
                "user_password" => password_hash($requestPost["confirmPassword"], PASSWORD_DEFAULT)
            ]);

            if (empty($response)) {
                echo $user->message->json();
                die;
            }

            session()->user->user_full_name = $requestPost["fullName"];
            session()->user->user_nick_name = $requestPost["userName"];
            session()->user->user_email = $requestPost["email"];
            echo json_encode(["success" => "cliente atualizado com sucesso"]);
            die;
        }

        $customer = new ModelCustomer();
        $customer->email = session()->user->user_email;
        $customerData = $customer->findCustomerByEmail();
        
        echo $this->view->render("admin/customer-update-data-form", [
            "userFullName" => showUserFullName(),
            "endpoints" => ["/admin/customer/update-data/form"],
            "customerData" => $customerData
        ]);
    }

    public function thanksPurchase()
    {
        $message = "Agradecemos pela sua compra, 
        você pode fazer login usando o seu nome de usuário ou e-mail.";
        echo $this->view->render("site/thanks-purchase", [
            "message" => $message
        ]);
    }

    public function customerSubscribeForm()
    {
        $csrfToken = session()->csrf_token;
        echo $this->view->render("site/subscribe-form", [
            "csrfToken" => $csrfToken
        ]);
    }
}
