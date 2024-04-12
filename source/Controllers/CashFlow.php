<?php

namespace Source\Controllers;

use DateTime;
use Exception;
use PhpOffice\PhpSpreadsheet\IOFactory;
use Ramsey\Uuid\Uuid;
use Source\Core\Controller;
use Source\Domain\Model\CashFlow as ModelCashFlow;
use Source\Domain\Model\CashFlowGroup;
use Source\Domain\Model\User;

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
        if (empty(session()->user)) {
            redirect("/admin/login");
        }

        if (empty($data["uuid"])) {
            throw new Exception("parametro uuid não pode estar vazio", 500);
        }

        $requestPost = $this->getRequests()
        ->setRequiredFields(["destroy", "restore"])->getAllPostData();
        $requestPost["restore"] = filter_var($requestPost["restore"], FILTER_VALIDATE_BOOLEAN);
        $requestPost["destroy"] = filter_var($requestPost["destroy"], FILTER_VALIDATE_BOOLEAN);

        $cashFlow = new ModelCashFlow();
        $response = false;
        if ($requestPost["restore"]) {
            $response = $cashFlow->updateCashFlowByUuid([
                "uuid"=> $data["uuid"],
                "deleted" => 0
            ]);
        }

        if ($requestPost["destroy"]) {
            $cashFlow->setUuid($data["uuid"]);
            $response = $cashFlow->dropCashFlowByUuid();
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
        if (empty(session()->user)) {
            redirect("/admin/login");
        }

        $user = new User();
        $user->setEmail(session()->user->user_email);
        $userData = $user->findUserByEmail();

        if (empty($userData)) {
            throw new Exception($user->message->json(), 500);
        }

        $user->setId($userData->id);
        $cashFlow = new ModelCashFlow();
        $cashFlowDataByUser = $cashFlow->findCashFlowDeletedTrue([], $user);

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

    public function findCashFlowDataForChartPie()
    {
        $user = basicsValidatesForChartsRender();
        $cashFlow = new ModelCashFlow();
        $cashFlowData = $cashFlow->findGroupAccountsAgrupped($user);
        
        if (empty($cashFlowData)) {
            echo json_encode([]);
            die;
        }

        $accountsData = [];
        $totalAccounts = [];

        foreach ($cashFlowData as $arrayData) {
            array_push($accountsData, $arrayData->group_name);
            array_push($totalAccounts, $arrayData->total_accounts);
        }

        echo json_encode(
            [
                "total_accounts" => $totalAccounts, 
                "accounts_data" => $accountsData
            ]);
    }

    public function findCashFlowDataForChartLine()
    {
        $user = basicsValidatesForChartsRender();
        $cashFlow = new ModelCashFlow();
        $cashFlowData = $cashFlow->findCashFlowByUser(["entry", "created_at"], $user);

        $dateRange = $this->getRequests()->get("daterange");
        if (!empty($dateRange)) {
            $cashFlow = new ModelCashFlow();
            $cashFlowData = $cashFlow->findCashFlowDataByDate($dateRange, $user, ["entry", "created_at"]);
        }

        if (empty($cashFlowData)) {
            echo json_encode([]);
            die;
        }

        $orderByDate = function($a, $b) {
            $monthA = date("n", strtotime($a));
            $monthB = date("n", strtotime($b));
            
            $dayA = date("j", strtotime($a));
            $dayB = date("j", strtotime($b));

            if ($monthA == $monthB) {
                if ($dayA == $dayB) {
                    return 0;
                }

                return $dayA < $dayB ? -1 : 1;
            }
            return $monthA < $monthB ? -1 : 1;
        };

        $groupByDate = [];
        foreach ($cashFlowData as $value) {
            $date = $value->created_at;
            $entryValue = $value->getEntry();

            if (array_key_exists($date, $groupByDate)) {
                $groupByDate[$date] += $entryValue;
            }else {
                $groupByDate[$date] = $entryValue;
            }
        }

        uksort($groupByDate, $orderByDate);
        $formatDate = function ($date) {
            return date("d/m", strtotime($date));
        };

        $response = [];
        $response["created_at"] = array_keys($groupByDate);
        $response["created_at"] = array_map($formatDate, $response["created_at"]);

        $response["entry"] = array_values($groupByDate);
        $response["created_at"] = array_slice($response["created_at"], 0, 31);
        
        $response["entry"] = array_slice($response["entry"], 0, 31);
        echo json_encode($response);
    }

    public function importExcelFile()
    {
        if (empty(session()->user)) {
            redirect("/admin/login");
        }

        $file = $this->getRequestFiles()->getFile("excelFile");
        $verifyExtensions = ["xls", "xlsx"];

        $fileExtension = explode(".", $file["name"]);
        $fileExtension = strtolower(array_pop($fileExtension));

        if (!in_array($fileExtension, $verifyExtensions)) {
            throw new \Exception("tipo de arquivo inválido", 500);
        }

        $spreadSheetFile = IOFactory::load($file["tmp_name"]);
        $data = $spreadSheetFile->getActiveSheet()->toArray();

        $requiredFieldsExcelFile = [
            "Data lançamento",
            "Histórico",
            "Tipo de entrada",
            "Lançamento"
        ];

        $arrayHeader = array_shift($data);
        if (!empty(array_diff($arrayHeader, $requiredFieldsExcelFile)) && !empty(array_diff($requiredFieldsExcelFile, $arrayHeader))) {
            http_response_code(500);
            echo json_encode(["error" => "cabeçalho do arquivo inválido"]);
            die;
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

        foreach ($excelData["d"] as $date) {
            $dateObj = DateTime::createFromFormat("Y-m-d", $date);
            if (!$dateObj) {
                http_response_code(500);
                echo json_encode(["error" => "campo data no arquivo está mal formatado"]);
                die;
            }
        }

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

        $user = new User();
        $user->setEmail(session()->user->user_email);
        $userData = $user->findUserByEmail();
        $user->setId($userData->id);

        $verifyEntryType = ["Crédito", "Débito"];
        $arrayUuid = [];
        $arrayEdit = [];
        $arrayDelete = [];
        $errorMessage = "";

        foreach ($excelData['h'] as $key => $history) {
            if (!in_array($excelData["t"][$key], $verifyEntryType)) {
                $errorMessage = "tipo de entrada inválida";
                continue;
            }

            $verifyDateData = strtotime($excelData["d"][$key]);
            if (strtotime(date("Y-m-d")) < $verifyDateData) {
                $errorMessage = "a data de lançamento não pode ser uma data futura";
                continue;
            }

            $excelData["t"][$key] = $excelData["t"][$key] == "Crédito" ? 1 : 0;
            $excelData["l"][$key] = trim(str_replace(["R$", ",", "-"], "", $excelData["l"][$key]));
            $excelData["l"][$key] = number_format($excelData["l"][$key], 2, ",", ".");

            $cashFlowGroup = new CashFlowGroup();
            $cashFlowGroupData = $cashFlowGroup->findCashFlowGroupByName($excelData["g"][$key], $user);

            if (empty($cashFlowGroupData)) {
                $errorMessage = "grupo de contas inexistente";
                continue;
            }

            $cashFlowGroup->setId($cashFlowGroupData->id);
            $cashFlow = new ModelCashFlow();
            $uuid = Uuid::uuid6();

            array_push($arrayUuid, $uuid);
            array_push($arrayEdit, "<a class='icons' href=" . url("/admin/cash-flow/update/form/" . $uuid . "") . "><i class='fas fa-edit' aria-hidden='true'></i>");
            array_push($arrayDelete, "<a class='icons' href='#'><i style='color:#ff0000' class='fa fa-trash' aria-hidden='true'></i></a>");

            $response = $cashFlow->persistData([
                "uuid" => $uuid,
                "id_user" => $user,
                "id_cash_flow_group" => $cashFlowGroup,
                "entry" => $excelData["l"][$key],
                "history" => $history,
                "entry_type" => $excelData["t"][$key],
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

        $accountGroup = [];
        $launchDate = [];
        $history = [];
        $entryType = [];
        $launchValue = [];

        foreach ($arrayUuid as $uuid) {
            $cashFlow = new ModelCashFlow();
            $cashFlow->setUuid($uuid);
            $cashFlowData = $cashFlow->findCashFlowByUuid();
            
            array_push($accountGroup, $cashFlowData->group_name);
            array_push($launchDate, date("d/m/Y", strtotime($cashFlowData->created_at)));
            
            array_push($history, $cashFlowData->getHistory());
            $entryTypeString = $cashFlowData->entry_type == 0 ? "Débito" : "Crédito";
            
            array_push($entryType, $entryTypeString);
            $entryValue = "R$ " . number_format($cashFlowData->getEntry(), 2, ",", ".");
            array_push($launchValue, $entryValue);
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
            $response["success"] = true;
            $response["error"] = $errorMessage;
            echo json_encode($response);
            die;
        }

        echo json_encode($response);
    }

    public function cashFlowRemoveRegister(array $data)
    {
        if (empty(session()->user)) {
            redirect("/admin/login");
        }

        if (empty($data["uuid"])) {
            throw new \Exception("uuid inválido", 500);
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

        $user = new User();
        $user->setEmail(session()->user->user_email);
        $userData = $user->findUserByEmail();

        if (empty($userData)) {
            throw new \Exception($user->message->json(), 500);
        }

        $user->setId($userData->id);
        $cashFlow = new ModelCashFlow();

        $balance = $cashFlow->calculateBalance($user);
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
        if (empty(session()->user)) {
            redirect("/admin/login");
        }

        if ($this->getServer()->getServerByKey("REQUEST_METHOD") == "POST") {
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

            $user = new User();
            $user->setEmail(session()->user->user_email);
            $userData = $user->findUserByEmail();

            if (empty($userData)) {
                http_response_code(500);
                echo $user->message->json();
                die;
            }

            $user->setId($userData->id);
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
                "id_user" => $user,
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

            echo json_encode(["success" => true, "url" => url("/admin/cash-flow/report")]);
            die;
        }

        if (empty($data["uuid"])) {
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

        $user = new User();
        $user->setEmail(session()->user->user_email);
        $userData = $user->findUserByEmail();

        if (empty($userData)) {
            throw new Exception($user->message->json(), 500);
        }

        $user->setId($userData->id);
        $cashFlowGroup = new CashFlowGroup();
        $cashFlowGroupData = $cashFlowGroup->findCashFlowGroupByUser([], $user);

        echo $this->view->render("admin/cash-flow-form-update", [
            "userFullName" => showUserFullName(),
            "endpoints" => ["/admin/cash-flow/form", "/admin/cash-flow/report"],
            "cashFlowData" => $cashFlowData,
            "cashFlowGroupData" => $cashFlowGroupData
        ]);
    }

    public function cashFlowReport()
    {
        if (empty(session()->user)) {
            redirect("/admin/login");
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
        $cashFlow = new ModelCashFlow();
        $cashFlowDataByUser = $cashFlow->findCashFlowByUser([], $user);

        $dateRange = $this->getRequests()->get("daterange");
        if (!empty($dateRange)) {
            $cashFlow = new ModelCashFlow();
            $cashFlowDataByUser = $cashFlow->findCashFlowDataByDate($dateRange, $user);
        }

        if (!empty($cashFlowDataByUser)) {
            foreach ($cashFlowDataByUser as &$data) {
                $data->setEntry('R$ ' . number_format($data->getEntry(), 2, ',', '.'));

                $data->created_at = date('d/m/Y', strtotime($data->created_at));
                $data->entry_type_value = $data->entry_type == 1 ? "Crédito" : "Débito";
            }
        }

        $cashFlow = new ModelCashFlow();
        $user = new User();

        $user->setId($userData->id);
        $balanceValue = $cashFlow->calculateBalance($user);

        $balance = !empty($balanceValue) ? 'R$ ' . number_format($balanceValue, 2, ',', '.') : 0;

        if (!empty($cashFlowDataByUser) && is_array($cashFlowDataByUser)) {
            $cashFlowDataByUser = array_reverse($cashFlowDataByUser);
        }

        echo $this->view->render("admin/cash-flow-report", [
            "userFullName" => showUserFullName(),
            "endpoints" => ["/admin/cash-flow/form", "/admin/cash-flow/report"],
            "cashFlowDataByUser" => $cashFlowDataByUser,
            "balance" => $balance,
            "balanceValue" => $balanceValue
        ]);
    }

    public function cashFlowForm()
    {
        if (empty(session()->user)) {
            redirect("/admin/login");
        }

        if ($this->getServer()->getServerByKey('REQUEST_METHOD') == 'POST') {
            $requestPost = $this->getRequests()
                ->setRequiredFields(
                    [
                        "launchValue",
                        "releaseHistory",
                        "entryType",
                        "csrfToken",
                        "accountGroup"
                    ]
                )->getAllPostData();

            $entryTypeFields = [
                0 => 'success',
                1 => 'success',
            ];

            if (empty($entryTypeFields[$requestPost["entryType"]])) {
                http_response_code(500);
                echo json_encode(["invalid_entry_type" => "erro na verificação do tipo de entrada"]);
                die;
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
            $cashFlowGroup = new CashFlowGroup();
            $cashFlowGroup->setUuid($requestPost["accountGroup"]);
            $cashFlowGroupData = $cashFlowGroup->findCashFlowGroupByUuid();

            if (empty($cashFlowGroup)) {
                http_response_code(500);
                echo $cashFlowGroup->message->json();
                die;
            }
            
            $cashFlowGroup->setId($cashFlowGroupData->id);
            $user->setId($userData->id);
            
            $cashFlow = new ModelCashFlow();
            $response = $cashFlow->persistData([
                "uuid" => Uuid::uuid6(),
                "id_user" => $user,
                "id_cash_flow_group" => $cashFlowGroup,
                "entry" => $requestPost["launchValue"],
                "history" => $requestPost["releaseHistory"],
                "entry_type" => $requestPost["entryType"],
                "created_at" => date("Y-m-d"),
                "updated_at" => date("Y-m-d"),
                "deleted" => 0
            ]);

            if (empty($response)) {
                http_response_code(500);
                echo $cashFlow->message->json();
                die;
            }

            echo json_encode(['success' => 'lançamento realizado com sucesso']);
            die;
        }

        $user = new User();
        $user->setEmail(session()->user->user_email);
        $userData = $user->findUserByEmail();
        $user->setId($userData->id);

        $cashFlowGroup = new CashFlowGroup();
        $cashFlowGroupData = $cashFlowGroup->findCashFlowGroupByUser([], $user);

        echo $this->view->render("admin/cash-flow-form", [
            "userFullName" => showUserFullName(),
            "endpoints" => ["/admin/cash-flow/form", "/admin/cash-flow/report"],
            "cashFlowGroupData" => $cashFlowGroupData
        ]);
    }
}
