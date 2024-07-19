<?php

namespace Source\Controllers;

use DateTime;
use Exception;
use PhpOffice\PhpSpreadsheet\IOFactory;
use Ramsey\Uuid\Uuid;
use Source\Core\Connect;
use Source\Core\Controller;
use Source\Domain\Model\CashFlow as ModelCashFlow;
use Source\Domain\Model\CashFlowGroup;
use Source\Domain\Model\HistoryAudit;

/**
 * CashFlow C:\php-projects\sistema-contabil\source\Controllers
 * @link 
 * @author Roberto Dorado <robertodorado7@gmail.com>
 * @package Controllers
 */
class CashFlow extends Controller
{
    /**
     * CashFlow constructor
     */
    public function __construct()
    {
        parent::__construct();
    }

    public function cashFlowModifyData(array $data)
    {
        verifyRequestHttpOrigin($this->getServer()->getServerByKey("HTTP_ORIGIN"));
        if (empty($data["uuid"])) {
            throw new Exception("parametro uuid não pode estar vazio", 500);
        }

        $requestPost = $this->getRequests()->setRequiredFields(
            [
                "destroy",
                "restore",
                "launchValue",
                "history"
            ]
        )->getAllPostData();
        $requestPost["restore"] = filter_var($requestPost["restore"], FILTER_VALIDATE_BOOLEAN);
        $requestPost["destroy"] = filter_var($requestPost["destroy"], FILTER_VALIDATE_BOOLEAN);

        $responseUserAndCompany = initializeUserAndCompanyId();
        $cashFlow = new ModelCashFlow();
        $historyAudit = new HistoryAudit();
        $response = false;
        $requestPost["launchValue"] = convertCurrencyRealToFloat($requestPost["launchValue"]);

        if ($requestPost["restore"]) {
            $response = $cashFlow->updateCashFlowByUuid([
                "uuid" => $data["uuid"],
                "deleted" => 0
            ]);

            $responseHistoryAudit = $historyAudit->persistData([
                "uuid" => Uuid::uuid4(),
                "id_company" => $responseUserAndCompany["company_id"],
                "id_user" => $responseUserAndCompany["user"],
                "id_report" => 2,
                "history_transaction" => "Restauração da conta '{$requestPost["history"]}'",
                "transaction_value" => $requestPost["launchValue"],
                "created_at" => date("Y-m-d H:i:s"),
                "deleted" => 0,
            ]);
        }

        if ($requestPost["destroy"]) {
            $cashFlow->setUuid($data["uuid"]);
            $response = $cashFlow->dropCashFlowByUuid();

            $responseHistoryAudit = $historyAudit->persistData([
                "uuid" => Uuid::uuid4(),
                "id_company" => $responseUserAndCompany["company_id"],
                "id_user" => $responseUserAndCompany["user"],
                "id_report" => 2,
                "history_transaction" => "Exclusão permanente da conta '{$requestPost["history"]}'",
                "transaction_value" => $requestPost["launchValue"],
                "created_at" => date("Y-m-d H:i:s"),
                "deleted" => 0,
            ]);
        }

        if (empty($responseHistoryAudit)) {
            http_response_code(500);
            echo $historyAudit->message->json();
            die;
        }

        if (empty($response)) {
            http_response_code(500);
            echo $cashFlow->message->json();
            die;
        }

        echo json_encode(["success" => "registro modificado com sucesso"]);
    }

    public function cashFlowBackupReport()
    {
        $response = initializeUserAndCompanyId();
        $cashFlow = new ModelCashFlow();
        $cashFlowDataByUser = $cashFlow->findCashFlowDeletedTrue([], $response["user"], $response["company_id"]);

        if (!empty($cashFlowDataByUser)) {
            foreach ($cashFlowDataByUser as &$value) {
                $value->setEntry("R$ " . number_format($value->getEntry(), 2, ",", "."));
                $value->created_at = date("d/m/Y", strtotime($value->created_at));
                $value->entry_type_value = empty($value->entry_type) ? "Débito" : "Crédito";
            }
        }

        echo $this->view->render("admin/cash-flow-backup-report", [
            "userFullName" => showUserFullName(),
            "endpoints" => ["/admin/cash-flow/backup/report"],
            "cashFlowDataByUser" => $cashFlowDataByUser
        ]);
    }

    public function importExcelFile()
    {
        verifyRequestHttpOrigin($this->getServer()->getServerByKey("HTTP_ORIGIN"));
        Connect::getInstance()->beginTransaction();
        $file = $this->getRequestFiles()->getFile("excelFile");
        $verifyExtensions = ["xls", "xlsx", "csv"];

        $fileExtension = explode(".", $file["name"]);
        $fileExtension = strtolower(array_pop($fileExtension));

        if (!in_array($fileExtension, $verifyExtensions)) {
            throw new \Exception("tipo de arquivo inválido", 500);
        }

        $spreadSheetFile = IOFactory::load($file["tmp_name"]);
        $data = $spreadSheetFile->getActiveSheet()->toArray();

        $requiredFieldsExcelFile = [
            "Data lançamento",
            "Grupo de contas",
            "Histórico",
            "Tipo de entrada",
            "Lançamento"
        ];

        $arrayHeader = array_shift($data);
        foreach ($requiredFieldsExcelFile as $field) {
            if (!in_array($field, $arrayHeader)) {
                http_response_code(500);
                echo json_encode(["error" => "cabeçalho do arquivo inválido"]);
                die;
            }
        }

        if (empty($data)) {
            http_response_code(500);
            echo json_encode(["error" => "os dados do arquivo não podem estar vazio"]);
            die;
        }

        $excelData = [];
        foreach ($data as $arrayData) {
            foreach ($arrayData as $key => $value) {
                $excelData[strtolower(substr($arrayHeader[$key], 0, 1))][] = $value;
            }
        }

        $excelData = array_map("array_filter", $excelData);
        $lengths = array_map('count', $excelData);

        if (count(array_unique($lengths)) != 1) {
            http_response_code(500);
            echo json_encode(["error" => "alguns dados possuem valores a mais no arquivo"]);
            die;
        }

        $formatDateByFileExtension = [
            "xls" => function ($item) {
                $item = preg_replace("/^(\d{1})\/(\d+)\/(\d+)$/", "0$1/$2/$3", $item);
                $item = preg_replace("/^(\d+)\/(\d{1})\/(\d+)$/", "$1/0$2/$3", $item);
                $item = preg_replace("/^(\d+)\/(\d+)\/(\d+)$/", "$3-$1-$2", $item);
                return $item;
            },

            "xlsx" => function ($item) {
                $item = preg_replace("/^(\d{1})\/(\d+)\/(\d+)$/", "0$1/$2/$3", $item);
                $item = preg_replace("/^(\d+)\/(\d{1})\/(\d+)$/", "$1/0$2/$3", $item);
                $item = preg_replace("/^(\d+)\/(\d+)\/(\d+)$/", "$3-$1-$2", $item);
                return $item;
            },

            "csv" => function ($item) {
                $item = preg_replace("/^(\d{1})\/(\d+)\/(\d+)$/", "0$1/$2/$3", $item);
                $item = preg_replace("/^(\d+)\/(\d{1})\/(\d+)$/", "$1/0$2/$3", $item);
                $item = preg_replace("/^(\d+)\/(\d+)\/(\d+)$/", "$3-$2-$1", $item);
                return $item;
            }
        ];

        $excelData["d"] = array_map(function ($item) use ($formatDateByFileExtension, $fileExtension) {
            if (!empty($formatDateByFileExtension[$fileExtension])) {
                return $formatDateByFileExtension[$fileExtension]($item);
            }
        }, $excelData["d"]);

        $verifyTotalDataFromExcelFile = array_map("count", $excelData);
        $verifyTotalDataFromExcelFile = array_unique($verifyTotalDataFromExcelFile);

        $limit = 1000;
        foreach ($verifyTotalDataFromExcelFile as $value) {
            if ($value > $limit) {
                http_response_code(500);
                echo json_encode(["error" => "o limite de importação é de {$limit} registros"]);
                die;
            }
        }

        $responseUserAndCompany = initializeUserAndCompanyId();
        $verifyEntryType = ["Crédito", "Débito"];
        $arrayUuid = [];
        $arrayEdit = [];
        $arrayDelete = [];
        $errorMessage = "";
        $invalidCashFlowGroup = [];
        $invalidEntryValue = [];
        $invalidDate = [];
        $invalidEntryType = [];

        foreach ($excelData['h'] as $key => $history) {
            if (!in_array($excelData["t"][$key], $verifyEntryType)) {
                $invalidEntryType[] = $excelData["t"][$key];
                continue;
            }

            if (!preg_match("/^[R\\$\s\d\,\.-]+$/", $excelData["l"][$key])) {
                $invalidEntryValue[] = $excelData["l"][$key];
                continue;
            }

            if (!preg_match("/^(\d{4}-\d{2}-\d{2}|\d{2}\/\d{2}\/\d{4})$/", $excelData["d"][$key])) {
                $invalidDate[] = $excelData["d"][$key];
                continue;
            }

            $verifyDateData = strtotime($excelData["d"][$key]);
            if (strtotime(date("Y-m-d")) < $verifyDateData) {
                $errorMessage = "a data de lançamento não pode ser uma data futura";
                continue;
            }

            $entryType = $excelData["t"][$key] == "Crédito" ? 1 : 0;
            $cashFlowGroup = new CashFlowGroup();
            $cashFlowGroupData = $cashFlowGroup->findCashFlowGroupByName($excelData["g"][$key], $responseUserAndCompany["user"]);

            if (empty($cashFlowGroupData)) {
                $invalidCashFlowGroup[] = $excelData["g"][$key];
                continue;
            }

            $cashFlowGroup->setId($cashFlowGroupData->id);
            $cashFlow = new ModelCashFlow();
            $uuid = Uuid::uuid4();

            array_push($arrayUuid, $uuid);
            array_push($arrayEdit, "<a class='icons' href=" . url("/admin/cash-flow/update/form/" . $uuid . "") . "><i class='fas fa-edit' aria-hidden='true'></i>");
            array_push($arrayDelete, "<a class='icons' href='#'><i style='color:#ff0000' class='fa fa-trash' aria-hidden='true'></i></a>");

            if (empty(session()->user->company_id)) {
                http_response_code(500);
                echo json_encode(["error" => "selecione uma empresa antes de criar uma conta"]);
                die;
            }

            $response = $cashFlow->persistData([
                "uuid" => $uuid,
                "id_company" => session()->user->company_id,
                "id_user" => $responseUserAndCompany["user"],
                "id_cash_flow_group" => $cashFlowGroup,
                "entry" => $excelData["l"][$key],
                "history" => $history,
                "entry_type" => $entryType,
                "created_at" => $excelData["d"][$key],
                "updated_at" => date("Y-m-d"),
                "deleted" => 0
            ]);

            if (!$response) {
                http_response_code(500);
                $errorMessage = "erro interno ao importar os dados";
                continue;
            }
        }

        if (!empty($invalidEntryType)) {
            $errorMessage = "tipo de entrada inválido";
            foreach ($invalidEntryType as $value) {
                $errorMessage .= ", {$value}";
            }

            http_response_code(500);
            echo json_encode(["error" => $errorMessage]);
            Connect::getInstance()->rollBack();
            die;
        }
        
        if (!empty($invalidDate)) {
            $errorMessage = "data inválida";
            foreach ($invalidDate as $value) {
                $errorMessage .= ", {$value}";
            }

            http_response_code(500);
            echo json_encode(["error" => $errorMessage]);
            Connect::getInstance()->rollBack();
            die;
        }

        if (!empty($invalidEntryValue)) {
            $errorMessage = "valor de lançamento inválido";
            foreach ($invalidEntryValue as $value) {
                $errorMessage .= ", {$value}";
            }

            http_response_code(500);
            echo json_encode(["error" => $errorMessage]);
            Connect::getInstance()->rollBack();
            die;
        }

        if (!empty($invalidCashFlowGroup)) {
            $errorMessage = "grupo de contas inexistente";
            foreach ($invalidCashFlowGroup as $value) {
                $errorMessage .= ", {$value}";
            }

            http_response_code(500);
            echo json_encode(["error" => $errorMessage]);
            Connect::getInstance()->rollBack();
            die;
        }

        $accountGroup = [];
        $launchDate = [];
        $history = [];
        $entryType = [];
        $launchValue = [];

        foreach ($arrayUuid as $uuid) {
            $cashFlow = new ModelCashFlow();
            $cashFlow->setUuid($uuid);
            $cashFlowData = $cashFlow->findCashFlowByUuid();

            if (empty($cashFlowData)) {
                echo $cashFlow->message->json();
                Connect::getInstance()->rollBack();
                die;
            }

            array_push($accountGroup, $cashFlowData->group_name);
            array_push($launchDate, date("d/m/Y", strtotime($cashFlowData->created_at)));

            array_push($history, $cashFlowData->getHistory());
            $entryTypeString = $cashFlowData->entry_type == 0 ? "Débito" : "Crédito";

            array_push($entryType, $entryTypeString);
            $entryValue = "R$ " . number_format($cashFlowData->getEntry(), 2, ",", ".");
            array_push($launchValue, $entryValue);

            $historyAudit = new HistoryAudit();
            $responseHistoryAudit = $historyAudit->persistData([
                "uuid" => Uuid::uuid4(),
                "id_company" => $responseUserAndCompany["company_id"],
                "id_user" => $responseUserAndCompany["user"],
                "id_report" => 2,
                "history_transaction" => "Importação de arquivo em excel '{$cashFlowData->getHistory()}'",
                "transaction_value" => $cashFlowData->getEntry(),
                "created_at" => date("Y-m-d H:i:s"),
                "deleted" => 0,
            ]);

            if (empty($responseHistoryAudit)) {
                http_response_code(500);
                echo $historyAudit->message->json();
                Connect::getInstance()->rollBack();
                die;
            }
        }

        $excelData = [];
        $excelData["Id"] = $arrayUuid;
        $excelData["Editar"] = $arrayEdit;
        $excelData["Excluir"] = $arrayDelete;
        $excelData["Tipo de entrada"] = $entryType;
        $excelData["Grupo de contas"] = $accountGroup;
        $excelData["Data lançamento"] = $launchDate;
        $excelData["Histórico"] = $history;
        $excelData["Tipo de entrada"] = $entryType;
        $excelData["Lançamento"] = $launchValue;

        $response = [
            "full_success" => "arquivo importado com sucesso",
            "excelData" => json_encode($excelData)
        ];

        if (!empty($errorMessage)) {
            http_response_code(500);
            unset($response["full_success"]);
            $response["error"] = $errorMessage;
            echo json_encode($response);
            Connect::getInstance()->rollBack();
            die;
        }

        Connect::getInstance()->commit();
        echo json_encode($response);
    }

    public function cashFlowRemoveRegister(array $data)
    {
        verifyRequestHttpOrigin($this->getServer()->getServerByKey("HTTP_ORIGIN"));
        if (empty($data["uuid"])) {
            redirect("/admin/login");
        }

        $uuid = $data["uuid"];
        $cashFlow = new ModelCashFlow();
        $cashFlow->setUuid($uuid);
        $cashFlowData = $cashFlow->findCashFlowByUuid();

        if (empty($cashFlowData)) {
            throw new \Exception("registro inválido", 500);
        }

        $cashFlow = new ModelCashFlow();
        $response = $cashFlow->updateCashFlowByUuid([
            "uuid" => $cashFlowData->getUuid(),
            "updated_at" => date("Y-m-d"),
            "deleted" => 1
        ]);

        if (empty($response)) {
            throw new \Exception($cashFlow->message->json(), 500);
        }

        $responseUserAndCompany = initializeUserAndCompanyId();
        $historyAudit = new HistoryAudit();
        $responseHistoryAudit = $historyAudit->persistData([
            "uuid" => Uuid::uuid4(),
            "id_company" => $responseUserAndCompany["company_id"],
            "id_user" => $responseUserAndCompany["user"],
            "id_report" => 2,
            "history_transaction" => "Exclusão do registro '{$cashFlowData->getHistory()}'",
            "transaction_value" => $cashFlowData->getEntry(),
            "created_at" => date("Y-m-d H:i:s"),
            "deleted" => 0,
        ]);

        if (empty($responseHistoryAudit)) {
            http_response_code(500);
            echo $historyAudit->message->json();
            die;
        }

        $cashFlow = new ModelCashFlow();
        $balance = $cashFlow->calculateBalance($responseUserAndCompany["user"], $responseUserAndCompany["company_id"]);
        $color = ($balance < 0 ? "#ff0000" : ($balance > 0 ? "#008000" : ""));
        $balance = ($balance < 0 ? $balance * -1 : $balance);

        echo json_encode(
            [
                "success" => true,
                "message" => "registro removido com sucesso",
                "balance" => "R$ " . number_format($balance, 2, ",", "."),
                "color" => $color
            ]
        );
    }

    public function cashFlowUpdateForm(array $data)
    {
        if ($this->getServer()->getServerByKey("REQUEST_METHOD") == "POST") {
            verifyRequestHttpOrigin($this->getServer()->getServerByKey("HTTP_ORIGIN"));
            $requestPost = $this->getRequests()
                ->setRequiredFields(
                    [
                        "launchValue",
                        "releaseHistory",
                        "entryType",
                        "csrfToken",
                        "createdAt",
                        "accountGroup"
                    ]
                )->getAllPostData();

            $uriParameter = $this->getServer()->getServerByKey("REQUEST_URI");
            $uriParameter = explode("/", $uriParameter);
            $uriParameter = array_pop($uriParameter);

            if (empty($uriParameter)) {
                throw new \Exception("parametro vazio ou inválido para atualização do fluxo de caixa");
            }

            $responseUserAndCompany = initializeUserAndCompanyId();
            $cashFlow = new ModelCashFlow();
            $cashFlow->setUuid($uriParameter);
            $cashFlowData = $cashFlow->findCashFlowByUuid();

            if (empty($cashFlowData)) {
                http_response_code(500);
                echo $cashFlow->message->json();
                die;
            }

            if (strtotime($requestPost["createdAt"]) > strtotime(date("Y-m-d"))) {
                http_response_code(500);
                echo json_encode(["invalid_date" => "Data de lançamento não pode ser futura"]);
                die;
            }

            $cashFlowGroup = new CashFlowGroup();
            $cashFlowGroup->setUuid($requestPost["accountGroup"]);
            $cashFlowGroupData = $cashFlowGroup->findCashFlowGroupByUuid();

            if (empty($cashFlowGroupData)) {
                http_response_code(500);
                echo $cashFlowGroup->message->json();
                die;
            }

            $cashFlowGroup->setId($cashFlowGroupData->id);
            $cashFlow = new ModelCashFlow();

            $response = $cashFlow->updateCashFlowByUuid([
                "uuid" => $uriParameter,
                "id_user" => $responseUserAndCompany["user"],
                "id_cash_flow_group" => $cashFlowGroup,
                "entry" => $requestPost["launchValue"],
                "history" => $requestPost["releaseHistory"],
                "entry_type" => $requestPost["entryType"],
                "created_at" => $requestPost["createdAt"],
                "updated_at" => date("Y-m-d"),
                "deleted" => 0
            ]);

            if (empty($response)) {
                http_response_code(500);
                echo $cashFlow->message->json();
                die;
            }

            $dateTime = new DateTime($cashFlowData->created_at);
            $cashFlowHistoryData = new \stdClass();
            $cashFlowHistoryData->valor_lacamento = "R$ " . number_format($cashFlowData->getEntry(), 2, ",", ".");
            $cashFlowHistoryData->data_lancamento = $dateTime->format("d/m/Y");
            $cashFlowHistoryData->grupo_contas_nome = $cashFlowData->group_name;
            $cashFlowHistoryData->historico = $cashFlowData->getHistory();
            $cashFlowHistoryData->tipo_entrada = !empty($cashFlowData->entry_type) ? "Crédito" : "Débito";

            $dateTime = new DateTime($requestPost["createdAt"]);
            $newCashFlowData = new \stdClass();
            $newCashFlowData->valor_lacamento = $requestPost["launchValue"];
            $newCashFlowData->data_lancamento = $dateTime->format("d/m/Y");
            $newCashFlowData->grupo_contas_nome = $cashFlowGroupData->group_name;
            $newCashFlowData->historico = $requestPost["releaseHistory"];
            $newCashFlowData->tipo_entrada = !empty($requestPost["entryType"]) ? "Crédito" : "Débito";

            $requestPost["launchValue"] = convertCurrencyRealToFloat($requestPost["launchValue"]);
            $historyAudit = new HistoryAudit();
            $responseHistoryAudit = $historyAudit->persistData([
                "uuid" => Uuid::uuid4(),
                "id_company" => $responseUserAndCompany["company_id"],
                "id_user" => $responseUserAndCompany["user"],
                "id_report" => 2,
                "history_transaction" => "Alteração na conta de "
                    . json_encode($cashFlowHistoryData) . " para " . json_encode($newCashFlowData) . "",
                "transaction_value" => $requestPost["launchValue"],
                "created_at" => date("Y-m-d H:i:s"),
                "deleted" => 0,
            ]);

            if (empty($responseHistoryAudit)) {
                http_response_code(500);
                echo $historyAudit->message->json();
                die;
            }

            echo json_encode(["success" => true, "url" => url("/admin/cash-flow/report")]);
            die;
        }

        if (empty($data["uuid"])) {
            redirect("/admin/cash-flow/report");
        }

        if (!preg_match("/^\w{8}-\w{4}-\w{4}-\w{4}-\w{12}$/", $data["uuid"])) {
            redirect("/admin/cash-flow/report");
        }

        $uuid = $data["uuid"];
        $cashFlow = new ModelCashFlow();
        $cashFlow->setUuid($uuid);
        $cashFlowData = $cashFlow->findCashFlowByUuid();

        if (empty($cashFlowData)) {
            redirect("/admin/cash-flow/report");
        }

        if ($cashFlowData->getEntry() < 0) {
            $cashFlowData->setEntry($cashFlowData->getEntry() * -1);
        }

        $response = initializeUserAndCompanyId();
        $cashFlowGroup = new CashFlowGroup();
        $cashFlowGroupData = $cashFlowGroup->findCashFlowGroupByUser([], $response["user"], $response["company_id"]);

        echo $this->view->render("admin/cash-flow-form-update", [
            "userFullName" => showUserFullName(),
            "endpoints" => [],
            "cashFlowData" => $cashFlowData,
            "cashFlowGroupData" => $cashFlowGroupData
        ]);
    }

    public function cashFlowReport()
    {
        $response = initializeUserAndCompanyId();
        $cashFlow = new ModelCashFlow();
        $cashFlowDataByUser = $cashFlow->findCashFlowByUser([], $response["user"], $response["company_id"]);

        $dateRange = $this->getRequests()->get("daterange");
        if (!empty($dateRange)) {
            $cashFlow = new ModelCashFlow();
            $cashFlowDataByUser = $cashFlow->findCashFlowDataByDate($dateRange, $response["user"], [], $response["company_id"]);
        }

        if (!empty($cashFlowDataByUser)) {
            foreach ($cashFlowDataByUser as &$data) {
                $data->setEntry('R$ ' . number_format($data->getEntry(), 2, ',', '.'));

                $data->created_at = date('d/m/Y', strtotime($data->created_at));
                $data->entry_type_value = $data->entry_type == 1 ? "Crédito" : "Débito";
            }
        }

        $cashFlow = new ModelCashFlow();
        $balanceValue = $cashFlow->calculateBalance($response["user"], $response["company_id"]);

        $balance = !empty($balanceValue) ? 'R$ ' . number_format($balanceValue, 2, ',', '.') : 0;
        if (!empty($cashFlowDataByUser) && is_array($cashFlowDataByUser)) {
            $cashFlowDataByUser = array_reverse($cashFlowDataByUser);
        }

        echo $this->view->render("admin/cash-flow-report", [
            "userFullName" => showUserFullName(),
            "endpoints" => ["/admin/cash-flow/form", "/admin/cash-flow/report"],
            "cashFlowDataByUser" => $cashFlowDataByUser,
            "balance" => $balance,
            "balanceValue" => $balanceValue,
            "hasBalance" => true,
            "hasControls" => true,
            "urlDateRangeInput" => url("/admin/cash-flow/report"),
        ]);
    }

    public function cashFlowForm()
    {
        if ($this->getServer()->getServerByKey('REQUEST_METHOD') == 'POST') {
            $requestPost = $this->getRequests()
                ->setRequiredFields(
                    [
                        "launchValue",
                        "releaseHistory",
                        "entryType",
                        "launchDate",
                        "csrfToken",
                        "accountGroup"
                    ]
                )->getAllPostData();

            $entryTypeFields = [
                0 => 'success',
                1 => 'success',
            ];

            $launchDate = date("Y-m-d", strtotime(str_replace("/", "-", $requestPost["launchDate"])));
            if (strtotime(date("Y-m-d")) < strtotime($launchDate)) {
                http_response_code(500);
                echo json_encode(["error" => "a data de lançamento não pode ser uma data futura"]);
                die;
            }

            if (empty($entryTypeFields[$requestPost["entryType"]])) {
                http_response_code(500);
                echo json_encode(["invalid_entry_type" => "erro na verificação do tipo de entrada"]);
                die;
            }

            $responseUserAndCompany = initializeUserAndCompanyId();
            $cashFlowGroup = new CashFlowGroup();
            $cashFlowGroup->setUuid($requestPost["accountGroup"]);
            $cashFlowGroupData = $cashFlowGroup->findCashFlowGroupByUuid();

            if (empty($cashFlowGroup)) {
                http_response_code(500);
                echo $cashFlowGroup->message->json();
                die;
            }

            $cashFlowGroup->setId($cashFlowGroupData->id);
            $cashFlow = new ModelCashFlow();
            $response = $cashFlow->persistData([
                "uuid" => Uuid::uuid4(),
                "id_user" => $responseUserAndCompany["user"],
                "id_company" => $responseUserAndCompany["company_id"],
                "id_cash_flow_group" => $cashFlowGroup,
                "entry" => $requestPost["launchValue"],
                "history" => $requestPost["releaseHistory"],
                "entry_type" => $requestPost["entryType"],
                "created_at" => $launchDate,
                "updated_at" => date("Y-m-d"),
                "deleted" => 0
            ]);

            $historyAudit = new HistoryAudit();
            $requestPost["launchValue"] = convertCurrencyRealToFloat($requestPost["launchValue"]);
            $responseHistoryAudit = $historyAudit->persistData([
                "uuid" => Uuid::uuid4(),
                "id_company" => $responseUserAndCompany["company_id"],
                "id_user" => $responseUserAndCompany["user"],
                "id_report" => 2,
                "history_transaction" => "Criação de nova conta '{$requestPost["releaseHistory"]}'",
                "transaction_value" => $requestPost["launchValue"],
                "created_at" => date("Y-m-d H:i:s"),
                "deleted" => 0,
            ]);

            if (empty($responseHistoryAudit)) {
                http_response_code(500);
                echo $historyAudit->message->json();
                die;
            }

            if (empty($response)) {
                http_response_code(500);
                echo $cashFlow->message->json();
                die;
            }

            echo json_encode(['success' => 'lançamento realizado com sucesso']);
            die;
        }

        $response = initializeUserAndCompanyId();
        $cashFlowGroup = new CashFlowGroup();
        $cashFlowGroupData = $cashFlowGroup->findCashFlowGroupByUser([], $response["user"], $response["company_id"]);

        echo $this->view->render("admin/cash-flow-form", [
            "userFullName" => showUserFullName(),
            "endpoints" => ["/admin/cash-flow/form", "/admin/cash-flow/report"],
            "cashFlowGroupData" => $cashFlowGroupData
        ]);
    }
}
