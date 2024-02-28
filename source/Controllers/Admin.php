<?php
namespace Source\Controllers;

use Source\Core\Controller;
use Source\Domain\Model\CashFlow;
use Source\Domain\Model\User;
use stdClass;

/**
 * Admin Controllers
 * @link 
 * @author Roberto Dorado <robertodorado7@gmail.com>
 * @package Source\Controllers
 */
class Admin extends Controller
{
    /**
     * Admin constructor
     */
    public function __construct()
    {
        parent::__construct();
    }

    public function cashFlowReport()
    {
        $user = new User();
        $userData = $user->findUserByEmail(session()->user->user_email);

        if (is_string($userData) && json_decode($userData) != null) {
            echo $userData;
            die;
        }

        $user->setId($userData->id);
        $cashFlow = new CashFlow();
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
            }
        }

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
            $cashFlow = new CashFlow();

            $response = $cashFlow->persistData([
                "id_user" => $user,
                "entry" => $requestPost["launchValue"],
                "history" => $requestPost["releaseHistory"],
                "entry_type" => $requestPost["entryType"],
                "created_at" => date("Y-m-d"),
                "updated_at" => date("Y-m-d")
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

    public function logout(array $data)
    {
        if (empty($data['request'])) {
            die;
        }

        if (is_string($data['request']) && json_decode($data['request']) != null) {
            $data = json_decode($data['request'], true);

            if (!empty($data['logout'])) {
                session()->unset('user');
                echo json_encode(["logout_success" => true]);
            }
        }

    }

    public function login()
    {
        if ($this->getServer()->getServerByKey("REQUEST_METHOD") == "POST") {
            $requestPost = $this->getRequests()->getAllPostData();
            
            $user = new User();
            $userDataOrErrorMessage = $user
                ->login($requestPost["userEmail"], $requestPost["userPassword"]);

            if (is_string($userDataOrErrorMessage) && json_decode($userDataOrErrorMessage) !== null) {
                echo $userDataOrErrorMessage;
                die;
            }

            if (isset($requestPost["remember"]) && $requestPost["remember"] == "on") {
                setcookie("user_email", $userDataOrErrorMessage->user_email, time() + 3600);
                setcookie("user_password", $requestPost["userPassword"], time() + 3600);
            }

            session()->set("user", [
                "user_full_name" => $userDataOrErrorMessage->user_full_name,
                "user_nick_name" => $userDataOrErrorMessage->user_nick_name,
                "user_email" => $userDataOrErrorMessage->user_email
            ]);
            
            echo json_encode(["login_success" => true, "url" => url("/admin")]);
            die;
        }

        echo $this->view->render("admin/login", []);
    }

    public function index()
    {
        if (empty(session()->user)) {
            redirect("/admin/login");
        }

        echo $this->view->render("admin/home", [
            "userFullName" => showUserFullName(),
            "endpoints" => []
        ]);
    }

    public function error()
    {
        redirect("/admin");
    }

}
