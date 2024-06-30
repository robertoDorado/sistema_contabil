<?php

namespace Source\Controllers;

use Exception;
use Ramsey\Uuid\Nonstandard\Uuid;
use Source\Core\Controller;
use Source\Core\Model;
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

    public function cashVariationBackupReport()
    {
        if ($this->getServer()->getServerByKey("REQUEST_METHOD") == "POST") {
            $requestPost = $this->getRequests()->setRequiredFields(
                [
                    "csrfToken",
                    "uuid",
                    "changeType"
                ]
            )->getAllPostData();

            $cashFlowGroup = new CashFlowGroup();
            $cashFlowGroup->setUuid($requestPost["uuid"]);
            $cashFlowGroupData = $cashFlowGroup->findCashFlowGroupByUuid();

            if (empty($cashFlowGroupData)) {
                throw new Exception($cashFlowGroup->message->json());
                die;
            }

            $params = [[], $cashFlowGroupData->id];
            $operatingCashFlow = new OperatingCashFlow();
            $operatingCashFlowData = $operatingCashFlow->findOperatingCashFlowByCashFlowGroupId(...$params);

            $verifyChangeType = [
                "restore" => function (Model $model): void {
                    $model->deleted = 0;
                    $model->save();
                },
                "delete" => function (Model $model): void {
                    $model->destroy();
                }
            ];

            if (!empty($operatingCashFlowData)) {
                if (!empty($verifyChangeType[$requestPost["changeType"]])) {
                    $verifyChangeType[$requestPost["changeType"]]($operatingCashFlowData);
                }
            }

            $financingCashFlow = new FinancingCashFlow();
            $financingCashFlowData = $financingCashFlow->findFinancingCashFlowByCashFlowGroupId(...$params);

            if (!empty($financingCashFlowData)) {
                if (!empty($verifyChangeType[$requestPost["changeType"]])) {
                    $verifyChangeType[$requestPost["changeType"]]($financingCashFlowData);
                }
            }

            $investmentCashFlow = new InvestmentCashFlow();
            $investmentCashFlowData = $investmentCashFlow->findInvestmentCashFlowByCashFlowGroupId(...$params);

            if (!empty($investmentCashFlowData)) {
                if (!empty($verifyChangeType[$requestPost["changeType"]])) {
                    $verifyChangeType[$requestPost["changeType"]]($investmentCashFlowData);
                }
            }

            echo json_encode(["success" => true]);
            die;
        }

        $user = new User();
        $user->setEmail(session()->user->user_email);
        $userData = $user->findUserByEmail(["id", "deleted"]);
        $user->setId($userData->id);
        $companyId = empty(session()->user->company_id) ? 0 : session()->user->company_id;

        $accountGroupVariationSession = is_object(session()->account_group_variation) ?
            (array) session()->account_group_variation : session()->account_group_variation;

        $responseData = empty(session()->account_group_variation) ?
            (new OperatingCashFlow())->findOperatingCashFlowJoinCashFlowGroup(
                ["deleted AS variation_deleted"],
                ["uuid", "group_name"],
                $user,
                $companyId
            ) : $accountGroupVariationSession;
        
        $responseData = array_filter($responseData, function ($item) {
            if (!empty($item->variation_deleted)) {
                return $item;
            }
        });

        echo $this->view->render("admin/cash-variation-backup", [
            "userFullName" => showUserFullName(),
            "endpoints" => ["/admin/cash-variation-setting/backup"],
            "responseData" => $responseData
        ]);
    }

    public function cashVariationRemoveData()
    {
        $requestPost = $this->getRequests()->setRequiredFields(["csrfToken", "uuid"])->getAllPostData();
        $cashFlowGroup = new CashFlowGroup();
        $cashFlowGroup->setUuid($requestPost["uuid"]);
        $cashFlowGroupData = $cashFlowGroup->findCashFlowGroupByUuid();

        if (empty($cashFlowGroupData)) {
            throw new Exception($cashFlowGroup->message->json());
            die;
        }

        $params = [[], $cashFlowGroupData->id];
        $operatingCashFlow = new OperatingCashFlow();
        $operatingCashFlowData = $operatingCashFlow->findOperatingCashFlowByCashFlowGroupId(...$params);

        if (!empty($operatingCashFlowData)) {
            $operatingCashFlowData->deleted = 1;
            $operatingCashFlowData->save();
        }

        $financingCashFlow = new FinancingCashFlow();
        $financingCashFlowData = $financingCashFlow->findFinancingCashFlowByCashFlowGroupId(...$params);

        if (!empty($financingCashFlowData)) {
            $financingCashFlowData->deleted = 1;
            $financingCashFlowData->save();
        }

        $investmentCashFlow = new InvestmentCashFlow();
        $investmentCashFlowData = $investmentCashFlow->findInvestmentCashFlowByCashFlowGroupId(...$params);

        if (!empty($investmentCashFlowData)) {
            $investmentCashFlowData->deleted = 1;
            $investmentCashFlowData->save();
        }

        echo json_encode(["success" => "alteração realizada com sucesso"]);
    }

    public function cashVariationFormUpdate(array $data)
    {
        if ($this->getServer()->getServerByKey("REQUEST_METHOD") == "POST") {
            $requestPost = $this->getRequests()->setRequiredFields(
                [
                    "csrfToken",
                    "accountGroup",
                    "accountGroupVariation",
                    "currentUuid"
                ]
            )->getAllPostData();

            if ($requestPost["currentUuid"] != $requestPost["accountGroup"]) {
                http_response_code(500);
                echo json_encode(["error" => "grupo de contas inválido"]);
                die;
            }

            $cashFlowGroup = new CashFlowGroup();
            $cashFlowGroup->setUuid($requestPost["currentUuid"]);
            $cashFlowGroupData = $cashFlowGroup->findCashFlowGroupByUuid();

            if (empty($cashFlowGroupData)) {
                http_response_code(500);
                echo $cashFlowGroup->message->json();
                die;
            }

            $defaultParams = [[], $cashFlowGroupData->id];
            $operatingCashFlow = new OperatingCashFlow();
            $operatingCashFlowData = $operatingCashFlow->findOperatingCashFlowByCashFlowGroupId(...$defaultParams);

            if (!empty($operatingCashFlowData)) {
                $operatingCashFlowData->destroy();
            }

            $investmentCashFlow = new InvestmentCashFlow();
            $investmentCashFlowData = $investmentCashFlow->findInvestmentCashFlowByCashFlowGroupId(...$defaultParams);

            if (!empty($investmentCashFlowData)) {
                $investmentCashFlowData->destroy();
            }

            $financingCashFlow = new FinancingCashFlow();
            $financingCashFlowData = $financingCashFlow->findFinancingCashFlowByCashFlowGroupId(...$defaultParams);

            if (!empty($financingCashFlowData)) {
                $financingCashFlowData->destroy();
            }

            $cashFlowGroup = new CashFlowGroup();
            $cashFlowGroup->setUuid($requestPost["accountGroup"]);
            $cashFlowGroupData = $cashFlowGroup->findCashFlowGroupByUuid();

            if (empty($cashFlowGroupData)) {
                http_response_code(500);
                echo json_encode(["error" => "grupo de caixa não existe"]);
                die;
            }

            $persistDataParams = [
                "cash_flow_group_id" => $cashFlowGroupData->id,
                "deleted" => 0
            ];
            $verifyPersistData = [
                1 => function () use ($persistDataParams) {
                    $operatingCashFlow = new OperatingCashFlow();
                    $response = $operatingCashFlow->persistData($persistDataParams);
                    return [$response, $operatingCashFlow];
                },
                2 => function () use ($persistDataParams) {
                    $investmentCashFlow = new InvestmentCashFlow();
                    $response = $investmentCashFlow->persistData($persistDataParams);
                    return [$response, $investmentCashFlow];
                },
                3 => function () use ($persistDataParams) {
                    $financingCashFlow = new FinancingCashFlow();
                    $response = $financingCashFlow->persistData($persistDataParams);
                    return [$response, $financingCashFlow];
                }
            ];

            $responseData = new \stdClass();
            $responseData->message = new Message();
            $responseData->message->error("não foi alterado nenhum registro");
            $response = [false, $responseData];

            if (!empty($verifyPersistData[$requestPost["accountGroupVariation"]])) {
                $response = $verifyPersistData[$requestPost["accountGroupVariation"]]();
            }

            if (empty($response[0])) {
                http_response_code(500);
                echo $response[1]->message->json();
                die;
            }

            echo json_encode(["redirect" => url("/admin/cash-variation-setting/report")]);
            die;
        }

        if (!Uuid::isValid($data["uuid"])) {
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

        $columns = [["id"], ["group_name", "uuid"]];
        $checkInstance = [
            OperatingCashFlow::class => function (string $uuid, array $columns) {
                $operatingCashFlow = new OperatingCashFlow();
                $operatingCashFlow->setUuid($uuid);
                $response = $operatingCashFlow->findOperatingCashFlowByUuid(...$columns);
                empty($response) ? null : $response->operating = true;
                return $response;
            },
            FinancingCashFlow::class => function (string $uuid, array $columns) {
                $financingCashFlow = new FinancingCashFlow();
                $financingCashFlow->setUuid($uuid);
                $response = $financingCashFlow->findFinancingCashFlowByUuid(...$columns);
                empty($response) ? null : $response->financing = true;
                return $response;
            },
            InvestmentCashFlow::class => function (string $uuid, array $columns) {
                $investmentCashFlow = new InvestmentCashFlow();
                $investmentCashFlow->setUuid($uuid);
                $response = $investmentCashFlow->findInvestmentCashFlowByUuid(...$columns);
                empty($response) ? null : $response->investment = true;
                return $response;
            }
        ];

        $cashVariationData = array_map(function ($class) use ($checkInstance, $data, $columns) {
            return $checkInstance[$class]($data["uuid"], $columns);
        }, $allClass);

        $cashVariationData = array_filter($cashVariationData, function ($object) {
            if (!empty($object)) {
                return $object;
            }
        });

        $cashVariationData = array_values($cashVariationData);
        $cashVariationData = $cashVariationData[0] ?? null;

        $cashFlowGroup = new CashFlowGroup();
        $cashFlowGroup->setUuid($data["uuid"]);
        $cashFlowGroupData = $cashFlowGroup->findCashFlowGroupByUser(["uuid", "group_name"], $user, $companyId);

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
                1 => function () use ($user, $companyId) {
                    $operatingCashFlow = new OperatingCashFlow();
                    return $operatingCashFlow->findOperatingCashFlowJoinCashFlowGroup(
                        ["deleted AS variation_deleted"],
                        ["uuid", "group_name"],
                        $user,
                        $companyId
                    );
                },

                2 => function () use ($user, $companyId) {
                    $investmentCashFlow = new InvestmentCashFlow();
                    return $investmentCashFlow->findInvestmentCashFlowJoinCashFlowGroup(
                        ["deleted AS variation_deleted"],
                        ["uuid", "group_name"],
                        $user,
                        $companyId
                    );
                },

                3 => function () use ($user, $companyId) {
                    $financingCashFlow = new FinancingCashFlow();
                    return $financingCashFlow->findFinancingCashFlowJoinCashFlowGroup(
                        ["deleted AS variation_deleted"],
                        ["uuid", "group_name"],
                        $user,
                        $companyId
                    );
                }
            ];

            $response = [];
            if (!empty($verifyAccountGroupVariation[$requestPost["accountGroupVariation"]])) {
                $response = $verifyAccountGroupVariation[$requestPost["accountGroupVariation"]]();
            }

            session()->set("account_group_variation", $response);
            session()->set("account_group_variation_id", $requestPost["accountGroupVariation"]);
            echo json_encode(["success" => true]);
            die;
        }

        $accountGroupVariationSession = is_object(session()->account_group_variation) ?
            (array) session()->account_group_variation : session()->account_group_variation;

        $responseData = empty(session()->account_group_variation) ?
            (new OperatingCashFlow())->findOperatingCashFlowJoinCashFlowGroup(
                ["deleted AS variation_deleted"],
                ["uuid", "group_name"],
                $user,
                $companyId
            ) : $accountGroupVariationSession;

        $responseData = array_filter($responseData, function ($item) {
            if (empty($item->variation_deleted)) {
                return $item;
            }
        });

        echo $this->view->render("admin/cash-variation-report", [
            "userFullName" => showUserFullName(),
            "endpoints" => ["/admin/cash-variation-setting/report"],
            "responseData" => $responseData
        ]);
    }

    public function cashVariationForm()
    {
        if ($this->getServer()->getServerByKey("REQUEST_METHOD") == "POST") {
            $requestPost = $this->getRequests()->setRequiredFields(
                [
                    "csrfToken",
                    "accountGroup",
                    "accountGroupVariation"
                ]
            )->getAllPostData();

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
