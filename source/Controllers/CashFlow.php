<?php
namespace Source\Controllers;

use DateTime;
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
            ->setRequiredFields(["launchValue", "releaseHistory", "entryType", "csrfToken"])
            ->getAllPostData();

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
            
            $cashFlow = new ModelCashFlow();
            $response = $cashFlow->updateCashFlowByUuid([
                "uuid" => $uriParameter,
                "id_user" => $user,
                "entry" => $requestPost["launchValue"],
                "history" => $requestPost["releaseHistory"],
                "entry_type" => $requestPost["entryType"],
                "created_at" => $cashFlowData->created_at,
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
        
        $cashFlowEmptyMessage = "";
        if (is_string($cashFlowDataByUser) && json_decode($cashFlowDataByUser) != null) {
            $cashFlowEmptyMessage = $cashFlowDataByUser;
        }
        
        if (is_array($cashFlowDataByUser) && !empty($cashFlowDataByUser)) {
            foreach($cashFlowDataByUser as &$data) {
                if (!empty($data->entry_type)) {
                    $data->setEntry('R$ ' . number_format($data->getEntry(), 2, ',', '.'));
                }else {
                    $data->setEntry('R$ ' . number_format($data->getEntry() * -1, 2, ',', '.'));
                }
                
                $data->created_at = date('d/m/Y', strtotime($data->created_at));
                $data->entry_type_value = $data->entry_type == 1 ? "Crédito" : "Débito";
                
                $data->uuid_array = explode("-", $data->getUuid());
                $data->uuid_value = $data->uuid_array[0];
            }
        }
        
        $cashFlow = new ModelCashFlow();
        $user = new User();
        
        $user->setId($userData->id);
        $balanceValue = $cashFlow->calculateBalance($user);

        if ($balanceValue < 0) {
            $balance = !empty($balanceValue) ? 'R$ ' . number_format($balanceValue * -1, 2, ',', '.') : 0;
        }else {
            $balance = !empty($balanceValue) ? 'R$ ' . number_format($balanceValue, 2, ',', '.') : 0;
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
