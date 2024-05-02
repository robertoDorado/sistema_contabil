<?php
namespace Source\Controllers;

use Exception;
use Ramsey\Uuid\Nonstandard\Uuid;
use Source\Core\Controller;
use Source\Domain\Model\CashFlowGroup as ModelCashFlowGroup;
use Source\Domain\Model\Customer;
use Source\Domain\Model\User;

/**
 * CashFlowGroup C:\php-projects\sistema-contabil\source\Controllers
 * @link 
 * @author Roberto Dorado <robertodorado7@gmail.com>
 * @package Controllers
 */
class CashFlowGroup extends Controller
{
    /**
     * CashFlowGroup constructor
     */
    public function __construct()
    {
        parent::__construct();
    }

    public function cashFlowGroupModiFyData(array $data)
    {
        if (empty(session()->user)) {
            redirect("/admin/login");
        }

        if (empty($data["uuid"])) {
            throw new Exception("parametro uuid não pode estar vazio", 500);
        }

        $customer = new Customer();
        $customer->email = session()->user->user_email;
        $customerData = $customer->findCustomerByEmail();

        if (empty($customerData)) {
            redirect("/admin/login");
        }
        
        if (!empty($customerData->getDeleted())) {
            redirect("/admin/login");
        }

        $requestPost = $this->getRequests()
        ->setRequiredFields(["destroy", "restore"])->getAllPostData();
        $requestPost["restore"] = filter_var($requestPost["restore"], FILTER_VALIDATE_BOOLEAN);
        $requestPost["destroy"] = filter_var($requestPost["destroy"], FILTER_VALIDATE_BOOLEAN);

        $cashFlowGroup = new ModelCashFlowGroup();
        $response = false;
        if ($requestPost["restore"]) {
            $response = $cashFlowGroup->updateCashFlowGroupByUuid([
                "uuid"=> $data["uuid"],
                "deleted" => 0
            ]);
        }

        if ($requestPost["destroy"]) {
            $cashFlowGroup->setUuid($data["uuid"]);
            $response = $cashFlowGroup->dropCashFlowGroupByUuid();
        }

        if (empty($response)) {
            http_response_code(500);
            echo $cashFlowGroup->message->json();
            die;
        }

        echo json_encode(["success" => "registro modificado com sucesso"]);
    }

    public function cashFlowGroupBackupReport()
    {
        if (empty(session()->user)) {
            redirect("/admin/login");
        }

        $customer = new Customer();
        $customer->email = session()->user->user_email;
        $customerData = $customer->findCustomerByEmail();

        if (empty($customerData)) {
            redirect("/admin/login");
        }
        
        if (!empty($customerData->getDeleted())) {
            redirect("/admin/login");
        }

        $user = new User();
        $user->setEmail(session()->user->user_email);
        $userData = $user->findUserByEmail();

        if (empty($userData)) {
            throw new Exception($user->message->json(), 500);
        }

        $user->setId($userData->id);
        $cashFlowGroup = new ModelCashFlowGroup();
        $cashFlowGroupData = $cashFlowGroup->findCashFlowGroupDeletedTrue([], $user);

        echo $this->view->render("admin/cash-flow-group-backup-report", [
            "cashFlowGroupData" => $cashFlowGroupData,
            "userFullName" => showUserFullName(),
            "endpoints" => ["/admin/cash-flow-group/backup/report"],
        ]);
    }

    public function cashFlowGroupRemoveRegister(array $data)
    {
        if (empty(session()->user)) {
            redirect("/admin/login");
        }

        if (empty($data["uuid"])) {
            throw new \Exception("uuid inválido", 500);
        }

        $customer = new Customer();
        $customer->email = session()->user->user_email;
        $customerData = $customer->findCustomerByEmail();

        if (empty($customerData)) {
            redirect("/admin/login");
        }
        
        if (!empty($customerData->getDeleted())) {
            redirect("/admin/login");
        }

        $uuid = $data["uuid"];
        $cashFlowGroup = new ModelCashFlowGroup();
        $cashFlowGroup->setUuid($uuid);
        $cashFlowGroupData = $cashFlowGroup->findCashFlowGroupByUuid();

        if (empty($cashFlowGroupData)) {
            http_response_code(500);
            echo $cashFlowGroup->message->json();
            die;
        }

        $cashFlowGroup = new ModelCashFlowGroup();
        $response = $cashFlowGroup->updateCashFlowGroupByUuid([
            "uuid" => $cashFlowGroupData->getUuid(),
            "updated_at" => date("Y-m-d"),
            "deleted" => 1
        ]);

        if (empty($response)) {
            http_response_code(500);
            echo $cashFlowGroup->message->json();
            die;
        }

        echo json_encode(["success" => "registro removido com sucesso"]);
    }

    public function cashFlowGroupFormUpdate(array $data)
    {
        if (empty(session()->user)) {
            redirect("/admin/login");
        }

        $customer = new Customer();
        $customer->email = session()->user->user_email;
        $customerData = $customer->findCustomerByEmail();

        if (empty($customerData)) {
            redirect("/admin/login");
        }
        
        if (!empty($customerData->getDeleted())) {
            redirect("/admin/login");
        }

        if ($this->getServer()->getServerByKey("REQUEST_METHOD") == "POST") {
            $requestPost = $this->getRequests()
            ->setRequiredFields(["csrfToken", "accountGroup"])->getAllPostData();

            $uriParameter = $this->getServer()->getServerByKey("REQUEST_URI");
            $uriParameter = explode("/", $uriParameter);
            $uriParameter = array_pop($uriParameter);

            if (empty($uriParameter)) {
                throw new \Exception("parametro vazio ou inválido para atualização do fluxo de caixa", 500);
            }

            $user = new User();
            $user->setEmail(session()->user->user_email);
            $userData = $user->findUserByEmail();

            if (empty($userData)) {
                throw new Exception($user->message->json(), 500);
            }

            $user->setId($userData->id);
            $cashFlowGroup = new ModelCashFlowGroup();
            $cashFlowGroup->setUuid($uriParameter);
            $cashFlowGroupData = $cashFlowGroup->findCashFlowGroupByUuid();

            if (empty($cashFlowGroupData)) {
                http_response_code(500);
                echo $cashFlowGroup->message->json();
                die;
            }

            $cashFlowGroup = new ModelCashFlowGroup();
            $response = $cashFlowGroup->updateCashFlowGroupByUuid([
                "uuid" => $uriParameter,
                "updated_at" => date("Y-m-d"),
                "group_name" => $requestPost["accountGroup"]
            ]);

            if (!$response) {
                http_response_code(500);
                echo json_encode(["error" => "erro interno ao tentar alterar o dado"]);
                die;
            }

            echo json_encode(["success" => true, "url" => url("/admin/cash-flow-group/report")]);
            die;
        }

        $cashFlowGroup = new ModelCashFlowGroup();
        $cashFlowGroup->setUuid($data["uuid"]);
        $cashFlowGroupData = $cashFlowGroup->findCashFlowGroupByUuid();

        if (empty($cashFlowGroupData)) {
            redirect("/admin/cash-flow-group/report");
        }

        echo $this->view->render("admin/cash-flow-group-form-update", [
            "userFullName" => showUserFullName(),
            "endpoints" => ["/admin/cash-flow-group/form", "/admin/cash-flow-group/report"],
            "cashFlowGroupData" => $cashFlowGroupData
        ]);
    }

    public function cashFlowGroupReport()
    {
        if (empty(session()->user)) {
            redirect("/admin/login");
        }

        $customer = new Customer();
        $customer->email = session()->user->user_email;
        $customerData = $customer->findCustomerByEmail();

        if (empty($customerData)) {
            redirect("/admin/login");
        }
        
        if (!empty($customerData->getDeleted())) {
            redirect("/admin/login");
        }

        $cashFlowGroup = new ModelCashFlowGroup();
        $user = new User();
        $user->setEmail(session()->user->user_email);
        $userData = $user->findUserByEmail();

        if (empty($userData)) {
            throw new Exception($user->message->json(), 500);
        }

        $user->setId($userData->id);
        $cashFlowGroupData = $cashFlowGroup->findCashFlowGroupByUser([], $user);

        echo $this->view->render("admin/cash-flow-group-report", [
            "userFullName" => showUserFullName(),
            "endpoints" => ["/admin/cash-flow-group/form", "/admin/cash-flow-group/report"],
            "cashFlowGroupData" => $cashFlowGroupData
        ]);
    }

    public function cashFlowGroupForm()
    {
        if (empty(session()->user)) {
            redirect("/admin/login");
        }

        $customer = new Customer();
        $customer->email = session()->user->user_email;
        $customerData = $customer->findCustomerByEmail();

        if (empty($customerData)) {
            redirect("/admin/login");
        }
        
        if (!empty($customerData->getDeleted())) {
            redirect("/admin/login");
        }

        if ($this->getServer()->getServerByKey("REQUEST_METHOD") == "POST") {
            $requestPost = $this->getRequests()
            ->setRequiredFields(["csrfToken", "accountGroup"])->getAllPostData();

            $user = new User();
            $user->setEmail(session()->user->user_email);
            $userData = $user->findUserByEmail();

            if (empty($userData)) {
                throw new Exception($user->message->json(), 500);
            }

            $user->setId($userData->id);
            $cashFlowGroup = new ModelCashFlowGroup();
            
            $response = $cashFlowGroup->persistData([
                "uuid" => Uuid::uuid4(),
                "id_user" => $user,
                "group_name" => $requestPost["accountGroup"],
                "created_at" => date("Y-m-d"),
                "updated_at" => date("Y-m-d"),
                "deleted" => 0
            ]);

            if (empty($response)) {
                http_response_code(500);
                echo $cashFlowGroup->message->json();
                die;
            }

            echo json_encode(["success" => "grupo criado com sucesso"]);
            die;
        }

        echo $this->view->render("admin/cash-flow-group-form", [
            "userFullName" => showUserFullName(),
            "endpoints" => ["/admin/cash-flow-group/form", "/admin/cash-flow-group/report"]
        ]);
    }
}
