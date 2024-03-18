<?php
namespace Source\Controllers;

use DateTime;
use Exception;
use PhpOffice\PhpSpreadsheet\IOFactory;
use Ramsey\Uuid\Uuid;
use Source\Core\Controller;
use Source\Domain\Model\CashFlow as ModelCashFlow;
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

    public function importExcelFile()
    {
        if (empty(session()->user)) {
            throw new \Exception("usuário inválido");
        }

        $file = $this->getRequestFiles()->getFile("excelFile");
        $verifyExtensions = ["xls", "xlsx"];
        
        $fileExtension = explode(".", $file["name"]);
        $fileExtension = strtolower(array_pop($fileExtension));
        
        if (!in_array($fileExtension, $verifyExtensions)) {
            throw new \Exception("tipo de arquivo inválido");
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
            echo json_encode(["error" => "cabeçalho do arquivo inválido"]);
            die;
        }

        if (empty($data)) {
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
            echo json_encode(["error" => "alguns dados possuem valores a mais no arquivo"]);
            die;
        }

        foreach ($excelData["d"] as $date) {
            $dateObj = DateTime::createFromFormat("Y-m-d", $date);
            if (!$dateObj) {
                echo json_encode(["error" => "campo data no arquivo está mal formatado"]);
                die;
            }
        }

        $verifyTotalDataFromExcelFile = array_map("count", $excelData);
        $verifyTotalDataFromExcelFile = array_unique($verifyTotalDataFromExcelFile);

        if ($verifyTotalDataFromExcelFile["h"] > 100) {
            echo json_encode(["error" => "o limite de importação é de 100 registros"]);
            die;
        }
        
        $user = new User();
        $userData = $user->findUserByEmail(session()->user->user_email);
        $user->setId($userData->id);
        
        $verifyEntryType = ["Crédito", "Débito"];
        $arrayUuid = [];
        $arrayEdit = [];
        $arrayDelete = [];
        
        foreach ($excelData['h'] as $key => $history) {
            if (!in_array($excelData["t"][$key], $verifyEntryType)) {
                echo json_encode(["error" => "somente Débito ou Crédito são aceitos como tipo de entrada"]);
                die;
            }

            $excelData["t"][$key] = $excelData["t"][$key] == "Crédito" ? 1 : 0;
            $excelData["l"][$key] = trim(str_replace(["R$", ","], "", $excelData["l"][$key]));
            $excelData["l"][$key] = number_format($excelData["l"][$key], 2, ",", ".");

            $cashFlow = new ModelCashFlow();
            $uuid = Uuid::uuid6();
            
            array_push($arrayUuid, $uuid);
            array_push($arrayEdit, "<a class='icons' href=" . url("/admin/cash-flow/update/form/" . $uuid . "") . "><i class='fas fa-edit' aria-hidden='true'></i>");
            array_push($arrayDelete, "<a class='icons' href='#'><i style='color:#ff0000' class='fa fa-trash' aria-hidden='true'></i></a>");
            
            $response = $cashFlow->persistData([
                "uuid" => $uuid,
                "id_user" => $user,
                "entry" => $excelData["l"][$key],
                "history" => $history,
                "entry_type" => $excelData["t"][$key],
                "created_at" => $excelData["d"][$key],
                "updated_at" => date("Y-m-d"),
                "deleted" => 0
            ]);

            if (!$response) {
                echo json_encode(["error" => "erro genérico ao importar os dados"]);
                die;
            }

        }

        $excelData = [];
        foreach ($data as $arrayData) {
            foreach ($arrayData as $key => $value) {
                $excelData[$arrayHeader[$key]][] = $value;
            }
        }

        $excelData["Id"] = $arrayUuid;
        $excelData["Editar"] = $arrayEdit;
        $excelData["Excluir"] = $arrayDelete;

        foreach ($excelData["Data lançamento"] as $key => &$date) {
            $date = DateTime::createFromFormat("Y-m-d", $date)->format("d/m/Y");
            $excelData["Lançamento"][$key] = $excelData["Tipo de entrada"][$key] == "Crédito" ? 
                floatval(trim(str_replace([",", "R$"], "", $excelData["Lançamento"][$key]))) :
                floatval(trim(str_replace([",", "R$"], "", $excelData["Lançamento"][$key]))) * -1;
            
            $excelData["Lançamento"][$key] = "R$ " . number_format($excelData["Lançamento"][$key], 2, ",", ".");
        }
        
        echo json_encode(["success" => "arquivo importado com sucesso", "excelData" => json_encode($excelData)]);
    }

    public function cashFlowRemoveRegister(array $data)
    {
        if (empty(session()->user)) {
            throw new \Exception("usuário inválido");
        }

        if (empty($data["uuid"])) {
            throw new \Exception("uuid inválido");
        }    
        
        $uuid = $data["uuid"];
        $cashFlow = new ModelCashFlow();
        $cashFlowData = $cashFlow->findCashFlowByUuid($uuid);
        
        if (is_string($cashFlowData) && json_decode($cashFlowData) != null) {
            throw new \Exception("registro inválido");
        }

        $cashFlow = new ModelCashFlow();        
        $response = $cashFlow->updateCashFlowByUuid([
            "uuid" => $cashFlowData->getUuid(),
            "updated_at" => date("Y-m-d"),
            "deleted" => 1
        ]);

        if (is_string($response) && json_decode($response) != null) {
            throw new \Exception($response);
        }

        $user = new User();
        $userData = $user->findUserByEmail(session()->user->user_email);

        if (is_string($userData) && json_decode($userData) != null) {
            throw new \Exception($userData);
        }
        
        $user->setId($userData->id);
        $cashFlow = new ModelCashFlow();
        
        $balance = $cashFlow->calculateBalance($user);
        $color = ($balance < 0 ? "#ff0000" : ($balance > 0 ? "#008000" : ""));
        $balance = ($balance < 0 ? $balance * -1 : $balance);

        echo json_encode(
            [
                "success" => true, 
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
                    "createdAt"
                ])->getAllPostData();

            $uriParameter = $this->getServer()->getServerByKey("REQUEST_URI");
            $uriParameter = explode("/", $uriParameter);
            $uriParameter = array_pop($uriParameter);

            if (empty($uriParameter)) {
                throw new \Exception("parametro vazio ou inválido para atualização do fluxo de caixa");
            }
            
            $user = new User();
            $userData = $user->findUserByEmail(session()->user->user_email);

            if (is_string($userData) && json_decode($userData) != null) {
                echo $userData;
                die;
            }

            $user->setId($userData->id);
            $cashFlow = new ModelCashFlow();
            $cashFlowData = $cashFlow->findCashFlowByUuid($uriParameter);
            
            if (is_string($cashFlowData) && json_decode($cashFlowData) != null) {
                echo $cashFlowData;
                die;
            }

            if (strtotime($requestPost["createdAt"]) > strtotime(date("Y-m-d"))) {
                echo json_encode(["invalid_date" => "Data de lançamento não pode ser futura"]);
                die;
            }
            
            $cashFlow = new ModelCashFlow();
            $response = $cashFlow->updateCashFlowByUuid([
                "uuid" => $uriParameter,
                "id_user" => $user,
                "entry" => $requestPost["launchValue"],
                "history" => $requestPost["releaseHistory"],
                "entry_type" => $requestPost["entryType"],
                "created_at" => $requestPost["createdAt"],
                "updated_at" => date("Y-m-d"),
                "deleted" => 0
            ]);

            if (is_string($response) && json_decode($response) != null) {
                echo $response;
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
        
        $cashFlowData = $cashFlow->findCashFlowByUuid($uuid);
        
        if (is_string($cashFlowData) && json_decode($cashFlowData) != null) {
            redirect("/admin/cash-flow/report");
        }
        
        if ($cashFlowData->getEntry() < 0) {
            $cashFlowData->setEntry($cashFlowData->getEntry() * -1);
        }

        echo $this->view->render("admin/cash-flow-form-update", [
            "userFullName" => showUserFullName(),
            "endpoints" => ['/admin/cash-flow/form', "/admin/cash-flow/report"],
            "cashFlowData" => $cashFlowData
        ]);
    }

    public function cashFlowReport()
    {
        if (empty(session()->user)) {
            redirect("/admin/login");
        }

        $user = new User();
        $userData = $user->findUserByEmail(session()->user->user_email);

        if (is_string($userData) && json_decode($userData) != null) {
            echo $userData;
            die;
        }

        $user->setId($userData->id);
        
        $cashFlow = new ModelCashFlow();
        $cashFlowDataByUser = $cashFlow->findCashFlowByUser([], $user);

        if (!empty($_GET["daterange"])) {
            $cashFlow = new ModelCashFlow();
            $cashFlowDataByUser = $cashFlow->findCashFlowDataByDate($_GET["daterange"], $user);
        }
        
        $cashFlowEmptyMessage = "";
        if (is_string($cashFlowDataByUser) && json_decode($cashFlowDataByUser) != null) {
            $cashFlowEmptyMessage = $cashFlowDataByUser;
        }
        
        if (is_array($cashFlowDataByUser) && !empty($cashFlowDataByUser)) {
            foreach($cashFlowDataByUser as &$data) {
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
            "endpoints" => ['/admin/cash-flow/form', "/admin/cash-flow/report"],
            "cashFlowDataByUser" => $cashFlowDataByUser,
            "cashFlowEmptyMessage" => $cashFlowEmptyMessage,
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
                ->setRequiredFields(["launchValue", "releaseHistory", "entryType", "csrfToken"])
                ->getAllPostData();
            
            $entryTypeFields = [
                0 => 'success',
                1 => 'success',
            ];
            if (empty($entryTypeFields[$requestPost["entryType"]])) {
                echo json_encode(["invalid_entry_type" => "erro na verificação do tipo de entrada"]);
                die;
            }
            
            $user = new User();
            $userData = $user->findUserByEmail(session()->user->user_email);

            if (is_string($userData) && json_decode($userData) != null) {
                echo $userData;
                die;
            }

            $user->setId($userData->id);
            $cashFlow = new ModelCashFlow();

            $response = $cashFlow->persistData([
                "uuid" => Uuid::uuid6(),
                "id_user" => $user,
                "entry" => $requestPost["launchValue"],
                "history" => $requestPost["releaseHistory"],
                "entry_type" => $requestPost["entryType"],
                "created_at" => date("Y-m-d"),
                "updated_at" => date("Y-m-d"),
                "deleted" => 0
            ]);

            if (is_string($response) && json_decode($response) != null) {
                echo $response;
                die;
            }
            
            echo json_encode(['success' => 'lançamento realizado com sucesso']);
            die;
        }
        
        echo $this->view->render("admin/cash-flow-form", [
            "userFullName" => showUserFullName(),
            "endpoints" => ['/admin/cash-flow/form', "/admin/cash-flow/report"]
        ]);
    }
}
