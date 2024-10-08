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

    public function companyModifyData()
    {
        verifyRequestHttpOrigin($this->getServer()->getServerByKey("HTTP_ORIGIN"));
        $requestPost = $this->getRequests()
        ->setRequiredFields(["csrfToken", "uuid", "destroy", "restore"])->getAllPostData();

        if (!preg_match("/^\w{8}-\w{4}-\w{4}-\w{4}-\w{12}$/", $requestPost["uuid"])) {
            throw new Exception("uuid inválido");
        }

        $requestPost["restore"] = filter_var($requestPost["restore"], FILTER_VALIDATE_BOOLEAN);
        $requestPost["destroy"] = filter_var($requestPost["destroy"], FILTER_VALIDATE_BOOLEAN);
        $company = new ModelCompany();
        $response = false;

        if ($requestPost["restore"]) {
            $response = $company->updateCompanyByUuid([
                "uuid" => $requestPost["uuid"],
                "deleted" => 0
            ]);
        }

        if ($requestPost["destroy"]) {
            $company->setUuid($requestPost["uuid"]);
            $response = $company->dropCompanyByUuid();
        }

        if (empty($response)) {
            http_response_code(400);
            echo $company->message->json();
            die;
        }

        echo json_encode(["success" => "registro modificado com sucesso"]);
    }

    public function companyBackupReport()
    {
        $response = initializeUserAndCompanyId();
        $company = new ModelCompany();
        $company->id_user = $response["user_data"]->id;
        $companyData = $company->findAllCompanyByUserDeleted(["uuid", "company_name", "deleted"]);

        echo $this->view->render("admin/company-backup-report", [
            "endpoints" => ["/admin/company/backup/report"],
            "userFullName" => showUserFullName(),
            "companyData" => $companyData
        ]);
    }

    public function companyDeleteRegister()
    {
        verifyRequestHttpOrigin($this->getServer()->getServerByKey("HTTP_ORIGIN"));
        $requestPost = $this->getRequests()->setRequiredFields(["uuid", "csrfToken"])->getAllPostData();
        if (empty($requestPost["uuid"])) {
            throw new Exception("uuid inválido");
        }

        if (!preg_match("/^\w{8}-\w{4}-\w{4}-\w{4}-\w{12}$/", $requestPost["uuid"])) {
            throw new Exception("uuid inválido");
        }

        $company = new ModelCompany();
        $company->setUuid($requestPost["uuid"]);
        $companyData = $company->findCompanyByUuid(["id", "deleted"]);
        
        if (!empty($companyData->getDeleted())) {
            throw new Exception("este registro já foi deletado");
        }

        if (!empty(session()->user->company_id)) {
            if ($companyData->id == session()->user->company_id) {
                http_response_code(400);
                echo json_encode(["error" => "não é possível deletar o id da empresa selecionado"]);
                die;
            }
        }

        $company = new ModelCompany();
        $response = $company->updateCompanyByUuid([
            "uuid" => $requestPost["uuid"],
            "deleted" => 1
        ]);

        if (empty($response)) {
            http_response_code(400);
            echo $company->message->json();
            die;
        }

        echo json_encode(["success" => true]);
    }

    public function companyFormUpdate(array $data)
    {
        if ($this->getServer()->getServerByKey("REQUEST_METHOD") == "POST") {
            verifyRequestHttpOrigin($this->getServer()->getServerByKey("HTTP_ORIGIN"));
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

            $response = initializeUserAndCompanyId();
            $company = new ModelCompany();
            $response = $company->updateCompanyByUuid([
                "uuid" => $requestPost["uuid"],
                "id_user" => $response["user"],
                "company_name" => $requestPost["companyName"],
                "company_document" => $requestPost["companyDocument"],
                "state_registration" => empty($requestPost["stateRegistration"]) ? null : $requestPost["stateRegistration"],
                "opening_date" => date("Y-m-d", strtotime(str_replace("/", "-", $requestPost["openingDate"]))),
                "web_site" => empty($requestPost["webSite"]) ? null : $requestPost["webSite"],
                "company_email" => empty($requestPost["companyEmail"]) ? null : $requestPost["companyEmail"],
                "company_zipcode" => $requestPost["companyZipcode"],
                "company_address" => $requestPost["companyAddress"],
                "company_address_number" => $requestPost["companyAddressNumber"],
                "company_neighborhood" => $requestPost["companyNeighborhood"],
                "company_city" => $requestPost["companyCity"],
                "company_state" => $requestPost["companyState"],
                "company_phone" => empty($requestPost["companyPhone"]) ? null : $requestPost["companyPhone"],
                "company_cell_phone" => $requestPost["companyCellPhone"],
                "updated_at" => date("Y-m-d"),
                "deleted" => 0
            ]);
            
            if (empty($response)) {
                http_response_code(400);
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
        $response = initializeUserAndCompanyId();
        $company = new ModelCompany();
        $company->id_user = $response["user_data"]->id;
        $companyData = $company->findCompanyByUser();

        echo $this->view->render("admin/company-report", [
            "userFullName" => showUserFullName(),
            "endpoints" => ["/admin/company/report"],
            "companyData" => $companyData
        ]);
    }

    public function companySession()
    {
        verifyRequestHttpOrigin($this->getServer()->getServerByKey("HTTP_ORIGIN"));
        $requestPost = $this->getRequests()->setRequiredFields(["companyId"])->getAllPostData();
        
        $company = new ModelCompany();
        $company->setId($requestPost["companyId"]);
        $companyData = $company->findCompanyById(["id", "deleted"]);

        if (!empty($companyData->getDeleted())) {
            http_response_code(400);
            echo json_encode(["error" => "esta empresa já foi deletada"]);
            die;
        }

        if (!preg_match("/^\d+$/", $requestPost["companyId"])) {
            http_response_code(400);
            throw new Exception("id empresa inválido");
        }

        session()->user->company_id = $requestPost["companyId"];
        echo json_encode(["success" => true]);
    }

    public function companyRegister()
    {
        if ($this->getServer()->getServerByKey("REQUEST_METHOD") == "POST") {
            verifyRequestHttpOrigin($this->getServer()->getServerByKey("HTTP_ORIGIN"));
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

            $response = initializeUserAndCompanyId();
            $company = new ModelCompany();
            $response = $company->persistData([
                "uuid" => Uuid::uuid4(),
                "id_user" => $response["user"],
                "company_name" => $requestPost["companyName"],
                "company_document" => $requestPost["companyDocument"],
                "state_registration" => empty($requestPost["stateRegistration"]) ? null : $requestPost["stateRegistration"],
                "opening_date" => date("Y-m-d", strtotime(str_replace("/", "-", $requestPost["openingDate"]))),
                "web_site" => empty($requestPost["webSite"]) ? null : $requestPost["webSite"],
                "company_email" => empty($requestPost["companyEmail"]) ? null : $requestPost["companyEmail"],
                "company_zipcode" => $requestPost["companyZipcode"],
                "company_address" => $requestPost["companyAddress"],
                "company_address_number" => $requestPost["companyAddressNumber"],
                "company_neighborhood" => $requestPost["companyNeighborhood"],
                "company_city" => $requestPost["companyCity"],
                "company_state" => $requestPost["companyState"],
                "company_phone" => empty($requestPost["companyPhone"]) ? null : $requestPost["companyPhone"],
                "company_cell_phone" => $requestPost["companyCellPhone"],
                "created_at" => date("Y-m-d"),
                "updated_at" => date("Y-m-d"),
                "deleted" => 0
            ]);

            if (empty($response)) {
                http_response_code(400);
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
