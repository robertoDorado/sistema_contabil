<?php

namespace Source\Controllers;

use Exception;
use Ramsey\Uuid\Nonstandard\Uuid;
use Source\Core\Controller;
use Source\Domain\Model\Company as ModelCompany;
use Source\Domain\Model\User;

/**
 * Company Controllers
 * @link 
 * @author Roberto Dorado <robertodorado7@gmail.com>
 * @package Source\Controllers
 */
class Company extends Controller
{
    /**
     * Company constructor
     */
    public function __construct()
    {
        parent::__construct();
    }

    public function companyFormUpdate(array $data)
    {
        if ($this->getServer()->getServerByKey("REQUEST_METHOD") == "POST") {
            $requestPost = $this->getRequests()->setRequiredFields(
                [
                    "uuid",
                    "csrfToken",
                    "companyName",
                    "companyDocument",
                    "openingDate",
                    "companyZipcode",
                    "companyAddress",
                    "companyAddressNumber",
                    "companyNeighborhood",
                    "companyCity",
                    "companyState",
                    "companyCellPhone"
                ]
            )->getAllPostData();

            if (empty($requestPost["uuid"])) {
                throw new Exception("uuid inválido");
            }
    
            if (!preg_match("/^\w{8}-\w{4}-\w{4}-\w{4}-\w{12}$/", $requestPost["uuid"])) {
                throw new Exception("uuid inválido");
            }

            $user = new User();
            $user->setEmail(session()->user->user_email);
            $userData = $user->findUserByEmail();

            if (empty($userData)) {
                http_response_code(500);
                echo $user->message->json();
                die;
            }

            $user->setId($userData->id);
            $company = new ModelCompany();
            $response = $company->updateCompanyByUuid([
                "uuid" => $requestPost["uuid"],
                "id_user" => $user,
                "company_name" => $requestPost["companyName"],
                "company_document" => $requestPost["companyDocument"],
                "state_registration" => $requestPost["stateRegistration"],
                "opening_date" => date("Y-m-d", strtotime(str_replace("/", "-", $requestPost["openingDate"]))),
                "web_site" => $requestPost["webSite"],
                "company_email" => $requestPost["companyEmail"],
                "company_zipcode" => $requestPost["companyZipcode"],
                "company_address" => $requestPost["companyAddress"],
                "company_address_number" => $requestPost["companyAddressNumber"],
                "company_neighborhood" => $requestPost["companyNeighborhood"],
                "company_city" => $requestPost["companyCity"],
                "company_state" => $requestPost["companyState"],
                "company_phone" => $requestPost["companyPhone"],
                "company_cell_phone" => $requestPost["companyCellPhone"],
                "created_at" => date("Y-m-d"),
                "updated_at" => date("Y-m-d"),
                "deleted" => 0
            ]);
            
            if (empty($response)) {
                http_response_code(500);
                echo $company->message->json();
                die;
            }

            echo json_encode(["success" => true]);
            die;
        }

        if (empty($data["uuid"])) {
            redirect("/admin/company/report");
        }

        if (!preg_match("/^\w{8}-\w{4}-\w{4}-\w{4}-\w{12}$/", $data["uuid"])) {
            redirect("/admin/company/report");
        }
        
        $company = new ModelCompany();
        $company->setUuid($data["uuid"]);
        $companyData = $company->findCompanyByUuid();

        if (empty($companyData)) {
            redirect("/admin/company/report");
        }

        echo $this->view->render("admin/company-form-update", [
            "userFullName" => showUserFullName(),
            "endpoints" => [],
            "companyData" => $companyData
        ]);
    }

    public function companyReport()
    {
        $user = new User();
        $user->setEmail(session()->user->user_email);
        $userData = $user->findUserByEmail(["id", "deleted"]);

        if (empty($userData)) {
            redirect("/admin/login");
        }

        $company = new ModelCompany();
        $company->id_user = $userData->id;
        $companyData = $company->findCompanyByUser();

        echo $this->view->render("admin/company-report", [
            "userFullName" => showUserFullName(),
            "endpoints" => ["/admin/company/report"],
            "companyData" => $companyData
        ]);
    }

    public function companySession()
    {
        $requestPost = $this->getRequests()->setRequiredFields(["companyId"])
            ->getAllPostData();

        if (!preg_match("/^\d+$/", $requestPost["companyId"])) {
            http_response_code(500);
            throw new Exception("id empresa enválido");
        }

        session()->user->company_id = $requestPost["companyId"];
        echo json_encode(["success" => true]);
    }

    public function companyRegister()
    {
        if ($this->getServer()->getServerByKey("REQUEST_METHOD") == "POST") {
            $requestPost = $this->getRequests()->setRequiredFields(
                [
                    "csrfToken",
                    "companyName",
                    "companyDocument",
                    "openingDate",
                    "companyZipcode",
                    "companyAddress",
                    "companyAddressNumber",
                    "companyNeighborhood",
                    "companyCity",
                    "companyState",
                    "companyCellPhone"
                ]
            )->getAllPostData();

            $user = new User();
            $user->setEmail(session()->user->user_email);
            $userData = $user->findUserByEmail();

            if (empty($userData)) {
                http_response_code(500);
                echo $user->message->json();
                die;
            }

            $user->setId($userData->id);
            $company = new ModelCompany();
            $response = $company->persistData([
                "uuid" => Uuid::uuid4(),
                "id_user" => $user,
                "company_name" => $requestPost["companyName"],
                "company_document" => $requestPost["companyDocument"],
                "state_registration" => $requestPost["stateRegistration"],
                "opening_date" => date("Y-m-d", strtotime(str_replace("/", "-", $requestPost["openingDate"]))),
                "web_site" => $requestPost["webSite"],
                "company_email" => $requestPost["companyEmail"],
                "company_zipcode" => $requestPost["companyZipcode"],
                "company_address" => $requestPost["companyAddress"],
                "company_address_number" => $requestPost["companyAddressNumber"],
                "company_neighborhood" => $requestPost["companyNeighborhood"],
                "company_city" => $requestPost["companyCity"],
                "company_state" => $requestPost["companyState"],
                "company_phone" => $requestPost["companyPhone"],
                "company_cell_phone" => $requestPost["companyCellPhone"],
                "created_at" => date("Y-m-d"),
                "updated_at" => date("Y-m-d"),
                "deleted" => 0
            ]);

            if (empty($response)) {
                http_response_code(500);
                echo $company->message->json();
                die;
            }

            echo json_encode(["success" => true]);
            die;
        }

        echo $this->view->render("admin/company-form", [
            "userFullName" => showUserFullName(),
            "endpoints" => ["/admin/company/register"]
        ]);
    }

    public function warningEmptyCompany()
    {
        echo $this->view->render("admin/warning-empty-company", [
            "userFullName" => showUserFullName(),
            "endpoints" => []
        ]);
    }
}
