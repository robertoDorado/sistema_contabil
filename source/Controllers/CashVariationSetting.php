<?php
namespace Source\Controllers;

use Source\Core\Controller;
use Source\Domain\Model\CashFlowGroup;
use Source\Domain\Model\OperatingCashFlow;
use Source\Domain\Model\User;

/**
 * CashVariationSetting Controllers
 * @link 
 * @author Roberto Dorado <robertodorado7@gmail.com>
 * @package Source\Controllers
 */
class CashVariationSetting extends Controller
{
    /**
     * CashVariationSetting constructor
     */
    public function __construct()
    {
        parent::__construct();
    }

    public function operatingCashFlowForm()
    {
        if ($this->getServer()->getServerByKey("REQUEST_METHOD") == "POST") {
            $requestPost = $this->getRequests()->setRequiredFields(["csrfToken", "accountGroup"])->getAllPostData();
            
            $cashFlowGroup = new CashFlowGroup();
            $cashFlowGroup->setUuid($requestPost["accountGroup"]);
            $cashFlowGroupData = $cashFlowGroup->findCashFlowGroupByUuid();
            
            if (empty($cashFlowGroupData)) {
                echo $cashFlowGroup->message->json();
                die;
            }

            $operatingCashFlow = new OperatingCashFlow();
            $operatingCashFlow->setUuid($requestPost["accountGroup"]);
            $operatingCashFlowData = $operatingCashFlow->findOperatingCashFlowByUuid(["uuid", "deleted"]);
            
            if (!empty($operatingCashFlowData)) {
                echo json_encode(["error" => "este grupo de contas já existe"]);
                die;
            }

            $operatingCashFlow = new OperatingCashFlow();
            $response = $operatingCashFlow->persistData([
                "uuid" => $requestPost["accountGroup"],
                "cash_flow_group_id" => $cashFlowGroupData->id,
                "group_name" => $cashFlowGroupData->group_name,
                "deleted" => 0
            ]);

            if (empty($response)) {
                echo $operatingCashFlow->message->json();
                die;
            }

            echo json_encode(["success" => "lançamento realizado com sucesso"]);
            die;
        }

        $user = new User();
        $user->setEmail(session()->user->user_email);
        $userData = $user->findUserByEmail(["id", "deleted"]);
        
        $user->setId($userData->id);
        $companyId = empty(session()->user->company_id) ? 0 : session()->user->company_id;

        $cashFlowGroup = new CashFlowGroup();
        $cashFlowGroupData = $cashFlowGroup->findCashFlowGroupByUser(["deleted", "uuid", "group_name"], $user, $companyId);

        echo $this->view->render("admin/operating-cash-flow-form", [
            "userFullName" => showUserFullName(),
            "endpoints" => ["/admin/cash-variation-setting/operating-cash-flow/form"],
            "cashFlowGroupData" => $cashFlowGroupData
        ]);
    }
}
