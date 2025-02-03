<?php

namespace Source\Controllers;

use Exception;
use Ramsey\Uuid\Nonstandard\Uuid;
use Source\Core\Controller;
use Source\Domain\Model\CashFlowGroup as ModelCashFlowGroup;
use Source\Domain\Model\HistoryAudit;

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
        verifyRequestHttpOrigin($this->getServer()->getServerByKey("HTTP_ORIGIN"));
        if (empty($data["uuid"])) {
            throw new Exception("parametro uuid não pode estar vazio", 500);
        }

        $requestPost = $this->getRequests()
            ->setRequiredFields(["destroy", "restore", "accountName"])->getAllPostData();
        $requestPost["restore"] = filter_var($requestPost["restore"], FILTER_VALIDATE_BOOLEAN);
        $requestPost["destroy"] = filter_var($requestPost["destroy"], FILTER_VALIDATE_BOOLEAN);

        $cashFlowGroup = new ModelCashFlowGroup();
        $response = false;
        $historyAudit = new HistoryAudit();
        $responseUserAndCompany = initializeUserAndCompanyId();

        if ($requestPost["restore"]) {
            $response = $cashFlowGroup->updateCashFlowGroupByUuid([
                "uuid" => $data["uuid"],
                "deleted" => 0
            ]);

            $responseHistoryAudit = $historyAudit->persistData([
                "uuid" => Uuid::uuid4(),
                "id_company" => $responseUserAndCompany["company_id"],
                "id_user" => $responseUserAndCompany["user"],
                "id_report" => 2,
                "history_transaction" => "Restauração do grupo de contas '{$requestPost['accountName']}'",
                "transaction_value" => 0,
                "created_at" => date("Y-m-d H:i:s"),
                "deleted" => 0,
            ]);
        }

        if ($requestPost["destroy"]) {
            $cashFlowGroup->setUuid($data["uuid"]);
            $response = $cashFlowGroup->dropCashFlowGroupByUuid();

            $responseHistoryAudit = $historyAudit->persistData([
                "uuid" => Uuid::uuid4(),
                "id_company" => $responseUserAndCompany["company_id"],
                "id_user" => $responseUserAndCompany["user"],
                "id_report" => 2,
                "history_transaction" => "Exclusão do grupo de contas '{$requestPost['accountName']}'",
                "transaction_value" => 0,
                "created_at" => date("Y-m-d H:i:s"),
                "deleted" => 0,
            ]);
        }

        if (empty($responseHistoryAudit)) {
            http_response_code(400);
            echo $historyAudit->message->json();
            die;
        }

        if (empty($response)) {
            http_response_code(400);
            echo $cashFlowGroup->message->json();
            die;
        }

        echo json_encode(["success" => "registro modificado com sucesso"]);
    }

    public function cashFlowGroupBackupReport()
    {
        $response = initializeUserAndCompanyId();
        $cashFlowGroup = new ModelCashFlowGroup();
        $cashFlowGroupData = $cashFlowGroup->findCashFlowGroupDeletedTrue([], $response["user"], $response["company_id"]);

        echo $this->view->render("admin/cash-flow-group-backup-report", [
            "cashFlowGroupData" => $cashFlowGroupData,
            "userFullName" => showUserFullName(),
            "endpoints" => ["/admin/cash-flow-group/backup/report"],
        ]);
    }

    public function cashFlowGroupRemoveRegister(array $data)
    {
        verifyRequestHttpOrigin($this->getServer()->getServerByKey("HTTP_ORIGIN"));
        if (empty($data["uuid"])) {
            throw new \Exception("uuid inválido", 500);
        }

        $uuid = $data["uuid"];
        $cashFlowGroup = new ModelCashFlowGroup();
        $cashFlowGroup->setUuid($uuid);
        $cashFlowGroupData = $cashFlowGroup->findCashFlowGroupByUuid();

        if (empty($cashFlowGroupData)) {
            http_response_code(400);
            echo $cashFlowGroup->message->json();
            die;
        }

        $cashFlowGroup = new ModelCashFlowGroup();
        $response = $cashFlowGroup->updateCashFlowGroupByUuid([
            "uuid" => $cashFlowGroupData->getUuid(),
            "updated_at" => date("Y-m-d"),
            "deleted" => 1
        ]);

        $responseUserAndCompany = initializeUserAndCompanyId();
        $historyAudit = new HistoryAudit();
        $responseHistoryAudit = $historyAudit->persistData([
            "uuid" => Uuid::uuid4(),
            "id_company" => $responseUserAndCompany["company_id"],
            "id_user" => $responseUserAndCompany["user"],
            "id_report" => 2,
            "history_transaction" => "Exclusão do grupo de contas '{$cashFlowGroupData->group_name}'",
            "transaction_value" => 0,
            "created_at" => date("Y-m-d H:i:s"),
            "deleted" => 0,
        ]);

        if (empty($responseHistoryAudit)) {
            http_response_code(400);
            echo $historyAudit->message->json();
            die;
        }

        if (empty($response)) {
            http_response_code(400);
            echo $cashFlowGroup->message->json();
            die;
        }

        echo json_encode(["success" => "registro removido com sucesso"]);
    }

    public function cashFlowGroupFormUpdate(array $data)
    {
        if ($this->getServer()->getServerByKey("REQUEST_METHOD") == "POST") {
            verifyRequestHttpOrigin($this->getServer()->getServerByKey("HTTP_ORIGIN"));
            $requestPost = $this->getRequests()
                ->setRequiredFields(["csrfToken", "accountGroup"])->getAllPostData();

            $uriParameter = $this->getServer()->getServerByKey("REQUEST_URI");
            $uriParameter = explode("/", $uriParameter);
            $uriParameter = array_pop($uriParameter);

            if (empty($uriParameter)) {
                throw new \Exception("parametro vazio ou inválido para atualização do fluxo de caixa", 500);
            }

            $cashFlowGroup = new ModelCashFlowGroup();
            $cashFlowGroup->setUuid($uriParameter);
            $cashFlowGroupData = $cashFlowGroup->findCashFlowGroupByUuid();

            if (empty($cashFlowGroupData)) {
                http_response_code(400);
                echo $cashFlowGroup->message->json();
                die;
            }

            $cashFlowGroup = new ModelCashFlowGroup();
            $response = $cashFlowGroup->updateCashFlowGroupByUuid([
                "uuid" => $uriParameter,
                "updated_at" => date("Y-m-d"),
                "group_name" => $requestPost["accountGroup"]
            ]);

            $responseUserAndCompany = initializeUserAndCompanyId();
            $historyAudit = new HistoryAudit();
            $responseHistoryAudit = $historyAudit->persistData([
                "uuid" => Uuid::uuid4(),
                "id_company" => $responseUserAndCompany["company_id"],
                "id_user" => $responseUserAndCompany["user"],
                "id_report" => 2,
                "history_transaction" => "Alteração do grupo de contas '{$cashFlowGroupData->group_name}' para '{$requestPost["accountGroup"]}'",
                "transaction_value" => 0,
                "created_at" => date("Y-m-d H:i:s"),
                "deleted" => 0,
            ]);

            if (empty($responseHistoryAudit)) {
                http_response_code(400);
                echo $historyAudit->message->json();
                die;
            }

            if (!$response) {
                http_response_code(400);
                echo json_encode(["error" => "erro interno ao tentar alterar o dado"]);
                die;
            }

            echo json_encode(["success" => true, "url" => url("/admin/cash-flow-group/report")]);
            die;
        }

        if (empty($data["uuid"])) {
            redirect("/admin/cash-flow/report");
        }

        if (!preg_match("/^\w{8}-\w{4}-\w{4}-\w{4}-\w{12}$/", $data["uuid"])) {
            redirect("/admin/cash-flow-group/report");
        }

        $cashFlowGroup = new ModelCashFlowGroup();
        $cashFlowGroup->setUuid($data["uuid"]);
        $cashFlowGroupData = $cashFlowGroup->findCashFlowGroupByUuid();

        if (empty($cashFlowGroupData)) {
            redirect("/admin/cash-flow-group/report");
        }

        echo $this->view->render("admin/cash-flow-group-form-update", [
            "userFullName" => showUserFullName(),
            "endpoints" => [],
            "cashFlowGroupData" => $cashFlowGroupData
        ]);
    }

    public function cashFlowGroupReport()
    {
        $cashFlowGroup = new ModelCashFlowGroup();
        $response = initializeUserAndCompanyId();
        $cashFlowGroupData = $cashFlowGroup->findCashFlowGroupByUser([], $response["user"], $response["company_id"]);

        echo $this->view->render("admin/cash-flow-group-report", [
            "userFullName" => showUserFullName(),
            "endpoints" => ["/admin/cash-flow-group/report"],
            "cashFlowGroupData" => $cashFlowGroupData
        ]);
    }

    public function cashFlowGroupForm()
    {
        if ($this->getServer()->getServerByKey("REQUEST_METHOD") == "POST") {
            verifyRequestHttpOrigin($this->getServer()->getServerByKey("HTTP_ORIGIN"));
            $requestPost = $this->getRequests()
                ->setRequiredFields(["csrfToken", "accountGroup"])->getAllPostData();

            $responseUserAndCompany = initializeUserAndCompanyId();
            $cashFlowGroup = new ModelCashFlowGroup();

            if (empty($responseUserAndCompany["company_id"])) {
                http_response_code(400);
                echo json_encode(["error" => "selecione uma empresa antes de criar un grupo de contas"]);
                die;
            }

            $response = $cashFlowGroup->persistData([
                "uuid" => Uuid::uuid4(),
                "id_user" => $responseUserAndCompany["user"],
                "id_company" => $responseUserAndCompany["company_id"],
                "group_name" => $requestPost["accountGroup"],
                "created_at" => date("Y-m-d"),
                "updated_at" => date("Y-m-d"),
                "deleted" => 0
            ]);

            if (empty($response)) {
                http_response_code(400);
                echo $cashFlowGroup->message->json();
                die;
            }

            $historyAudit = new HistoryAudit();
            $responseHistoryAudit = $historyAudit->persistData([
                "uuid" => Uuid::uuid4(),
                "id_company" => $responseUserAndCompany["company_id"],
                "id_user" => $responseUserAndCompany["user"],
                "id_report" => 2,
                "history_transaction" => "Criação de grupo de contas '{$requestPost["accountGroup"]}'",
                "transaction_value" => 0,
                "created_at" => date("Y-m-d H:i:s"),
                "deleted" => 0,
            ]);

            if (empty($responseHistoryAudit)) {
                http_response_code(400);
                echo $historyAudit->message->json();
                die;
            }

            echo json_encode(["success" => "grupo criado com sucesso"]);
            die;
        }

        echo $this->view->render("admin/cash-flow-group-form", [
            "userFullName" => showUserFullName(),
            "endpoints" => ["/admin/cash-flow-group/form"]
        ]);
    }
}
