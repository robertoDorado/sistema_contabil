<?php

namespace Source\Controllers;

use Ramsey\Uuid\Nonstandard\Uuid;
use Source\Core\Controller;
use Source\Domain\Model\CashFlowGroup;
use Source\Domain\Model\FinancingCashFlow;
use Source\Domain\Model\InvestmentCashFlow;
use Source\Domain\Model\OperatingCashFlow;
use Source\Domain\Model\User;
use Source\Models\CashFlowGroup as ModelsCashFlowGroup;
use Source\Support\Message;

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

    public function cashVariationFormUpdate(array $data)
    {
        if ($this->getServer()->getServerByKey("REQUEST_METHOD") == "POST") {
            $requestPost = $this->getRequests()
            ->setRequiredFields(["csrfToken", "accountGroup", "accountGroupVariation", "currentUuid"])->getAllPostData();
            echo "<pre>";
            print_r($requestPost);
            die;
        }

        if (!Uuid::isValid($data["uuid"])){
            redirect("/admin/cash-variation-setting/report");
        }
        
        $user = new User();
        $user->setEmail(session()->user->user_email);
        $userData = $user->findUserByEmail(["id", "deleted"]);
        
        $user->setId($userData->id);
        $companyId = empty(session()->user->company_id) ? 0 : session()->user->company_id;
        
        $allClass = [
            OperatingCashFlow::class,
            FinancingCashFlow::class,
            InvestmentCashFlow::class
        ];

        $columns = [["id"], ["deleted", "group_name", "uuid"]];
        $checkInstance = [
            OperatingCashFlow::class => function(string $uuid, array $columns) {
                $operatingCashFlow = new OperatingCashFlow();
                $operatingCashFlow->setUuid($uuid);
                $response = $operatingCashFlow->findOperatingCashFlowByUuid(...$columns);
                empty($response) ? null : $response->operating = true;
                return $response;
            },
            FinancingCashFlow::class => function(string $uuid, array $columns) {
                $financingCashFlow = new FinancingCashFlow();
                $financingCashFlow->setUuid($uuid);
                $response = $financingCashFlow->findFinancingCashFlowByUuid(...$columns);
                empty($response) ? null : $response->financing = true;
                return $response;
            },
            InvestmentCashFlow::class => function(string $uuid, array $columns) {
                $investmentCashFlow = new InvestmentCashFlow();
                $investmentCashFlow->setUuid($uuid);
                $response = $investmentCashFlow->findInvestmentCashFlowByUuid(...$columns);
                empty($response) ? null : $response->investment = true;
                return $response;
            }
        ];

        $cashVariationData = array_map(function($class) use ($checkInstance, $data, $columns) {
            return $checkInstance[$class]($data["uuid"], $columns);
        }, $allClass);

        $cashVariationData = array_filter($cashVariationData, function($object) {
            if (!empty($object)) {
                return $object;
            }
        });

        $cashVariationData = array_values($cashVariationData);
        $cashVariationData = $cashVariationData[0] ?? null;

        $cashFlowGroup = new CashFlowGroup();
        $cashFlowGroup->setUuid($data["uuid"]);
        $cashFlowGroupData = $cashFlowGroup->findCashFlowGroupByUser(["uuid", "group_name", "deleted"], $user, $companyId);

        echo $this->view->render("admin/cash-variation-form-update", [
            "userFullName" => showUserFullName(),
            "cashVariationData" => $cashVariationData,
            "cashFlowGroupData" => $cashFlowGroupData,
            "endpoints" => []
        ]);
    }

    public function cashVariationReport()
    {
        $user = new User();
        $user->setEmail(session()->user->user_email);
        $userData = $user->findUserByEmail(["id", "deleted"]);
        $user->setId($userData->id);

        $companyId = empty(session()->user->company_id) ? 0 : session()->user->company_id;
        if ($this->getServer()->getServerByKey("REQUEST_METHOD") == "POST") {
            $requestPost = $this->getRequests()->setRequiredFields(["csrfToken", "accountGroupVariation"])->getAllPostData();
            
            $verifyAccountGroupVariation = [
                1 => function() use ($user, $companyId) {
                    $operatingCashFlow = new OperatingCashFlow();
                    return $operatingCashFlow->findOperatingCashFlowJoinCashFlowGroup(
                        ["id"],
                        ["uuid", "group_name"],
                        $user,
                        $companyId
                    );
                },

                2 => function() use ($user, $companyId) {
                    $investmentCashFlow = new InvestmentCashFlow();
                    return $investmentCashFlow->findInvestmentCashFlowJoinCashFlowGroup(
                        ["id"],
                        ["uuid", "group_name"],
                        $user,
                        $companyId
                    );
                },

                3 => function() use ($user, $companyId) {
                    $financingCashFlow = new FinancingCashFlow();
                    return $financingCashFlow->findFinancingCashFlowJoinCashFlowGroup(
                        ["id"],
                        ["uuid", "group_name"],
                        $user,
                        $companyId
                    );
                }
            ];

            $response = null;
            if (!empty($verifyAccountGroupVariation[$requestPost["accountGroupVariation"]])) {
                $response = $verifyAccountGroupVariation[$requestPost["accountGroupVariation"]]();
            }

            session()->user->account_group_variation = $response;
            session()->user->account_group_variation_id = $requestPost["accountGroupVariation"];
            echo json_encode(["success" => true]);
            die;
        }

        $responseData = empty(session()->user->account_group_variation) ? 
        (new OperatingCashFlow())->findOperatingCashFlowJoinCashFlowGroup(
            ["id"],
            ["uuid", "group_name"],
            $user,
            $companyId
        ) : session()->user->account_group_variation;

        echo $this->view->render("admin/cash-variation-report", [
            "userFullName" => showUserFullName(),
            "endpoints" => ["/admin/cash-variation-setting/report"],
            "responseData" => $responseData
        ]);
    }

    public function cashVariationForm()
    {
        if ($this->getServer()->getServerByKey("REQUEST_METHOD") == "POST") {
            $requestPost = $this->getRequests()->setRequiredFields(["csrfToken", "accountGroup", "accountGroupVariation"])->getAllPostData();

            $cashFlowGroup = new CashFlowGroup();
            $cashFlowGroup->setUuid($requestPost["accountGroup"]);
            $cashFlowGroupData = $cashFlowGroup->findCashFlowGroupByUuid();

            if (empty($cashFlowGroupData)) {
                echo $cashFlowGroup->message->json();
                die;
            }

            $operatingCashFlow = new OperatingCashFlow();
            $operatingCashFlow->setUuid($requestPost["accountGroup"]);
            $columnsData = [["id"], ["uuid", "deleted"]];

            $operatingCashFlowData = $operatingCashFlow->findOperatingCashFlowByUuid(...$columnsData);
            if (!empty($operatingCashFlowData)) {
                echo json_encode(["error" => "este grupo de contas já existe no fluxo de caixa operacional"]);
                die;
            }

            $investmentCashFlow = new InvestmentCashFlow();
            $investmentCashFlow->setUuid($requestPost["accountGroup"]);
            $investmentCashFlowData = $investmentCashFlow->findInvestmentCashFlowByUuid(...$columnsData);

            if (!empty($investmentCashFlowData)) {
                echo json_encode(["error" => "este grupo de contas já existe no fluxo de caixa de investimentos"]);
                die;
            }

            $financingCashFlow = new FinancingCashFlow();
            $financingCashFlow->setUuid($requestPost["accountGroup"]);
            $financingCashFlowData = $financingCashFlow->findFinancingCashFlowByUuid(...$columnsData);

            if (!empty($financingCashFlowData)) {
                echo json_encode(["error" => "este grupo de contas já existe no fluxo de caixa de financiamento"]);
                die;
            }

            $verifyAccountGroupVariation = [
                1 => function (ModelsCashFlowGroup $cashFlowGroupData): array {
                    $operatingCashFlow = new OperatingCashFlow();
                    $response = $operatingCashFlow->persistData([
                        "cash_flow_group_id" => $cashFlowGroupData->id,
                        "deleted" => 0
                    ]);
                    return [$response, $operatingCashFlow];
                },

                2 => function (ModelsCashFlowGroup $cashFlowGroupData): array {
                    $investmentCashFlow = new InvestmentCashFlow();
                    $response = $investmentCashFlow->persistData([
                        "cash_flow_group_id" => $cashFlowGroupData->id,
                        "deleted" => 0
                    ]);
                    return [$response, $investmentCashFlow];
                },

                3 => function (ModelsCashFlowGroup $cashFlowGroupData): array {
                    $financingCashFlow = new FinancingCashFlow();
                    $response = $financingCashFlow->persistData([
                        "cash_flow_group_id" => $cashFlowGroupData->id,
                        "deleted" => 0
                    ]);
                    return [$response, $financingCashFlow];
                },
            ];

            $dataMessage = new \stdClass();
            $dataMessage->message = new Message();
            $dataMessage->message->error("nenhum registro foi inserido");
            $response = [false, $dataMessage];

            if (!empty($verifyAccountGroupVariation[$requestPost["accountGroupVariation"]])) {
                $response = $verifyAccountGroupVariation[$requestPost["accountGroupVariation"]]($cashFlowGroupData);
            }

            if (empty($response[0])) {
                echo $response[1]->message->json();
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

        echo $this->view->render("admin/cash-variation-form", [
            "userFullName" => showUserFullName(),
            "endpoints" => ["/admin/cash-variation-setting/form"],
            "cashFlowGroupData" => $cashFlowGroupData
        ]);
    }
}
