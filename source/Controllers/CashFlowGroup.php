<?php
namespace Source\Controllers;

use Exception;
use Ramsey\Uuid\Nonstandard\Uuid;
use Source\Core\Controller;
use Source\Domain\Model\CashFlowGroup as ModelCashFlowGroup;
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

    public function cashFlowGroupFormUpdate(array $data)
    {
        if (empty(session()->user)) {
            redirect("/admin/login");
        }

        $cashFlowGroup = new ModelCashFlowGroup();
        $cashFlowGroupData = $cashFlowGroup->findCashFlowGroupByUuid($data["uuid"]);

        if (is_string($cashFlowGroupData) && json_decode($cashFlowGroupData) != null) {
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

        $cashFlowGroup = new ModelCashFlowGroup();
        $user = new User();
        $userData = $user->findUserByEmail(session()->user->user_email, []);

        if (is_string($userData) && json_decode($userData) != null) {
            throw new Exception($userData);
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

        if ($this->getServer()->getServerByKey("REQUEST_METHOD") == "POST") {
            $requestPost = $this->getRequests()
            ->setRequiredFields(["csrfToken", "accountGroup"])->getAllPostData();

            $user = new User();
            $userData = $user->findUserByEmail(session()->user->user_email);

            if (is_string($userData) && json_decode($userData) != null) {
                throw new Exception($userData);
            }

            $user->setId($userData->id);
            $cashFlowGroup = new ModelCashFlowGroup();
            
            $response = $cashFlowGroup->persistData([
                "uuid" => Uuid::uuid6(),
                "id_user" => $user,
                "group_name" => $requestPost["accountGroup"],
                "created_at" => date("Y-m-d"),
                "updated_at" => date("Y-m-d"),
                "deleted" => 0
            ]);

            if (is_string($response) && json_decode($response) != null) {
                echo $response;
                die;
            }

            echo $response ? json_encode(["success" => "grupo criado com sucesso"]) :
            json_encode(["error" => "erro interno ao tentar criar o grupo"]);
            die;
        }

        echo $this->view->render("admin/cash-flow-group-form", [
            "userFullName" => showUserFullName(),
            "endpoints" => ["/admin/cash-flow-group/form", "/admin/cash-flow-group/report"]
        ]);
    }
}
