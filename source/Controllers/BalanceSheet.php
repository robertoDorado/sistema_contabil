<?php

namespace Source\Controllers;

use PhpOffice\PhpSpreadsheet\IOFactory;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;
use Ramsey\Uuid\Nonstandard\Uuid;
use Source\Core\Connect;
use Source\Core\Controller;
use Source\Core\Model;
use Source\Domain\Model\ChartOfAccount;
use Source\Domain\Model\ChartOfAccountGroup;
use Source\Domain\Model\ChartOfAccountModel;
use Source\Support\Message;

/**
 * BalanceSheet Controllers
 * @link 
 * @author Roberto Dorado <robertodorado7@gmail.com>
 * @package Source\Controllers
 */
class BalanceSheet extends Controller
{
    /**
     * BalanceSheet constructor
     */
    public function __construct()
    {
        parent::__construct();
    }

    public function chartOfAccountGroupBackup()
    {
        if ($this->getServer()->getServerByKey("REQUEST_METHOD") == "POST") {
            verifyRequestHttpOrigin($this->getServer()->getServerByKey("HTTP_ORIGIN"));
            $requestPost = $this->getRequests()->setRequiredFields(["uuid", "action"])->getAllPostData();

            $chartOfAccountGroup = new ChartOfAccountGroup();
            $chartOfAccountGroup->setUuid($requestPost["uuid"]);
            $chartOfAccountGroupData = $chartOfAccountGroup->findChartOfAccountGroupByUuid(["id"]);

            if (empty($chartOfAccountGroupData)) {
                http_response_code(500);
                echo json_encode(["error" => "registro não encontrado"]);
                die;
            }

            $response = new \stdClass();
            $response->message = new Message();
            $response->message->error("erro interno ao tentar modificar o registro");
            $response->error = true;

            $verifyAction = [
                "restore" => function(Model $model) use ($response) {
                    $model->setRequiredFields(["deleted"]);
                    $model->deleted = 0;
                    
                    $response->error = !$model->save() ? true : false;
                    $response->error ? $response->message->error("erro ao tentar restaurar o registro") : 
                    $response->message->success("registro restaurado com sucesso");
                },
                "delete" => function(Model $model) use ($response) {
                    $response->error = !$model->destroy() ? true : false;
                    $response->error ? $response->message->error("erro ao tentar excluir o registro") : 
                    $response->message->success("registro excluído com sucesso");
                },
            ];

            if (!empty($verifyAction[$requestPost["action"]])) {
                $verifyAction[$requestPost["action"]]($chartOfAccountGroupData);
            }

            if ($response->error) {
                http_response_code(500);
                echo $response->message->json();
                die;
            }

            echo $response->message->json();
            die;
        }

        $responseInitializeUserAndCompany = initializeUserAndCompanyId();
        $chartOfAccountGroup = new ChartOfAccountGroup();
        $chartOfAccountGroupData = $chartOfAccountGroup->findAllChartOfAccountGroup(
            [
                "uuid",
                "account_name",
                "account_number"
            ],
            [
                "id_user" => $responseInitializeUserAndCompany["user"]->getId(),
                "id_company" => $responseInitializeUserAndCompany["company_id"],
                "deleted" => 1
            ],
        );

        echo $this->view->render("admin/chart-of-account-group-backup", [
            "userFullName" => showUserFullName(),
            "endpoints" => ["/admin/balance-sheet/chart-of-account-group/backup"],
            "chartOfAccountGroupData" => $chartOfAccountGroupData
        ]);
    }

    public function chartOfAccountGroupDelete()
    {
        verifyRequestHttpOrigin($this->getServer()->getServerByKey("HTTP_ORIGIN"));
        $requestPost = $this->getRequests()->setRequiredFields(["uuid"])->getAllPostData();

        $chartOfAccountGroup = new ChartOfAccountGroup();
        $response = $chartOfAccountGroup->updateChartOfAccountGroupByUuid([
            "uuid" => $requestPost["uuid"],
            "deleted" => 1
        ]);

        if (!$response) {
            http_response_code(500);
            echo $chartOfAccountGroup->message->json();
            die;
        }

        echo json_encode(["success" => "grupo plano de contas removido com sucesso"]);
    }

    public function chartOfAccountGroupUpdate(array $data)
    {
        if ($this->getServer()->getServerByKey("REQUEST_METHOD") == "POST") {
            verifyRequestHttpOrigin($this->getServer()->getServerByKey("HTTP_ORIGIN"));
            $requestPost = $this->getRequests()->setRequiredFields(
                [
                    "uuid",
                    "accountName",
                    "accountNumber",
                    "csrfToken"
                ]
            )->getAllPostData();

            $responseInitializeUserAndCompany = initializeUserAndCompanyId();
            if (empty($responseInitializeUserAndCompany["company_id"])) {
                http_response_code(500);
                echo json_encode(["error" => "selecione uma empresa antes de atualizar uma categoria de contas"]);
                die;
            }

            $chartOfAccountGroup = new ChartOfAccountGroup();
            $response = $chartOfAccountGroup->updateChartOfAccountGroupByUuid([
                "uuid" => $requestPost["uuid"],
                "account_name" => $requestPost["accountName"],
                "account_number" => $requestPost["accountNumber"]
            ]);

            if (!$response) {
                http_response_code(500);
                echo $chartOfAccountGroup->message->json();
                die;
            }

            echo json_encode(["success" => true, "url" => url("/admin/balance-sheet/chart-of-account-group")]);
            die;
        }

        if (empty($data["uuid"])) {
            redirect("/admin/balance-sheet/chart-of-account-group");
        }

        if (!Uuid::isValid($data["uuid"])) {
            redirect("/admin/balance-sheet/chart-of-account-group");
        }

        $chartOfAccountGroup = new ChartOfAccountGroup();
        $chartOfAccountGroup->setUuid($data["uuid"]);
        $chartOfAccountGroupData = $chartOfAccountGroup->findChartOfAccountGroupByUuid(
            [
                "uuid",
                "account_name",
                "account_number"
            ]
        );

        if (empty($chartOfAccountGroupData)) {
            redirect("/admin/balance-sheet/chart-of-account-group");
        }

        echo $this->view->render("admin/chart-of-account-group-update", [
            "userFullName" => showUserFullName(),
            "endpoints" => ["/admin/balance-sheet/chart-of-account-group/update"],
            "chartOfAccountGroupData" => $chartOfAccountGroupData
        ]);
    }

    public function chartOfAccountGroupForm()
    {
        if ($this->getServer()->getServerByKey("REQUEST_METHOD") == "POST") {
            verifyRequestHttpOrigin($this->getServer()->getServerByKey("HTTP_ORIGIN"));
            $requestPost = $this->getRequests()->setRequiredFields(
                [
                    "accountName",
                    "accountNumber",
                    "csrfToken"
                ]
            )->getAllPostData();

            $responseInitializeUserAndCompany = initializeUserAndCompanyId();
            if (empty($responseInitializeUserAndCompany["company_id"])) {
                http_response_code(500);
                echo json_encode(["error" => "selecione uma empresa antes de criar uma categoria de contas"]);
                die;
            }

            $chartOfAccountGroup = new ChartOfAccountGroup();
            $response = $chartOfAccountGroup->persistData([
                "uuid" => Uuid::uuid4(),
                "id_user" => $responseInitializeUserAndCompany["user"],
                "id_company" => $responseInitializeUserAndCompany["company_id"],
                "account_name" => $requestPost["accountName"],
                "account_number" => $requestPost["accountNumber"],
                "deleted" => 0
            ]);

            if (empty($response)) {
                http_response_code(500);
                echo $chartOfAccountGroup->message->json();
                die;
            }

            echo json_encode(["success" => "categoria de contas criada com sucesso"]);
            die;
        }

        echo $this->view->render("admin/chart-of-account-group-form", [
            "userFullName" => showUserFullName(),
            "endpoints" => ["/admin/balance-sheet/chart-of-account-group/form"]
        ]);
    }

    public function chartOfAccountGroup()
    {
        $chartOfAccountGroup = new ChartOfAccountGroup();
        $responseUserAndCompany = initializeUserAndCompanyId();
        $chartOfAccountGroupData = $chartOfAccountGroup->findAllChartOfAccountGroup(
            [
                "uuid",
                "account_name",
                "account_number"
            ],
            [
                "id_user" => $responseUserAndCompany["user"]->getId(),
                "id_company" => $responseUserAndCompany["company_id"],
                "deleted" => 0
            ]
        );

        echo $this->view->render("admin/chart-of-account-group", [
            "userFullName" => showUserFullName(),
            "endpoints" => ["/admin/balance-sheet/chart-of-account-group"],
            "chartOfAccountGroupData" => $chartOfAccountGroupData
        ]);
    }

    public function chartOfAccountBackup()
    {
        if ($this->getServer()->getServerByKey("REQUEST_METHOD") == "POST") {
            verifyRequestHttpOrigin($this->getServer()->getServerByKey("HTTP_ORIGIN"));
            $requestPost = $this->getRequests()->setRequiredFields(["uuid", "action"])->getAllPostData();
            $chartOfAccount = new ChartOfAccount();

            $chartOfAccount->setUuid($requestPost["uuid"]);
            $chartOfAccountData = $chartOfAccount->findChartOfAccountByUuid(["id"]);

            if (!empty($chartOfAccountData)) {
                $chartOfAccountData->setRequiredFields(["deleted"]);
            }

            $response = new \stdClass();
            $response->error = true;
            $response->message = new Message();
            $response->message->error("erro interno ao modificar o registro");

            $verifyAction = [
                "restore" => function (Model $model) use ($response) {
                    $model->deleted = 0;
                    if ($model->save()) {
                        $response->error = false;
                        $response->message->success("registro restaurado com sucesso");
                    }
                },

                "delete" => function (Model $model) use ($response) {
                    if ($model->destroy()) {
                        $response->error = false;
                        $response->message->success("registro removido com sucesso");
                    }
                }
            ];

            if (!empty($verifyAction[$requestPost["action"]])) {
                $verifyAction[$requestPost["action"]]($chartOfAccountData);
            }

            if ($response->error) {
                http_response_code(500);
                echo $response->message->json();
                die;
            }

            echo $response->message->json();
            die;
        }

        $chartOfAccount = new ChartOfAccount();
        $responseInitializeUserAndCompany = initializeUserAndCompanyId();
        $chartOfAccountData = $chartOfAccount->findAllChartOfAccount(
            [
                "account_name",
                "account_number",
                "deleted",
                "uuid"
            ],
            [
                "id_company" => $responseInitializeUserAndCompany["company_id"],
                "id_user" => $responseInitializeUserAndCompany["user"]->getId(),
                "deleted" => 1
            ]
        );

        echo $this->view->render("admin/chart-of-account-backup", [
            "userFullName" => showUserFullName(),
            "endpoints" => ["/admin/balance-sheet/chart-of-account/backup"],
            "chartOfAccountData" => $chartOfAccountData
        ]);
    }

    public function chartOfAccountImportFile()
    {
        verifyRequestHttpOrigin($this->getServer()->getServerByKey("HTTP_ORIGIN"));
        $file = $this->getRequestFiles()->getFile("excelFile");
        $verifyExtensions = ["xls", "xlsx"];

        $fileExtension = explode(".", $file["name"]);
        $fileExtension = strtolower(array_pop($fileExtension));

        if (!in_array($fileExtension, $verifyExtensions)) {
            throw new \Exception("tipo de arquivo inválido", 500);
        }

        $spreadSheetFile = IOFactory::load($file["tmp_name"]);
        $data = $spreadSheetFile->getActiveSheet()->toArray();
        $responseUserAndCompany = initializeUserAndCompanyId();

        if (empty($responseUserAndCompany["company_id"])) {
            http_response_code(500);
            echo json_encode(["error" => "selecione uma empresa antes de importar o plano de contas"]);
            die;
        }

        if (empty($data)) {
            http_response_code(500);
            echo json_encode(["error" => "o arquivo plano de contas está vazio"]);
            die;
        }

        Connect::getInstance()->beginTransaction();
        $chartOfAccountGroupFile = array_filter($data, function ($array) {
            if (preg_match("/^\d+$/", $array[1])) {
                return $array;
            }
        });

        $chartOfAccountGroupFile = array_values($chartOfAccountGroupFile);
        $params = [
            "id_company" => $responseUserAndCompany["company_id"],
            "id_user" => $responseUserAndCompany["user"],
            "deleted" => 0
        ];

        foreach ($chartOfAccountGroupFile as $accountGroup) {
            $chartOfAccountGroup = new ChartOfAccountGroup();
            $params["uuid"] = Uuid::uuid4();
            $params["account_name"] = $accountGroup[0];
            $params["account_number"] = $accountGroup[1];
            $response = $chartOfAccountGroup->persistData($params);

            if (!$response) {
                http_response_code(500);
                echo $chartOfAccountGroup->message->json();
                Connect::getInstance()->rollBack();
                die;
            }
        }

        $chartOfAccountGroup = new ChartOfAccountGroup();
        $chartOfAccountGroupData = $chartOfAccountGroup->findAllChartOfAccountGroup(
            [
                "id",
                "account_number"
            ],
            [
                "id_company" => $responseUserAndCompany["company_id"],
                "id_user" => $responseUserAndCompany["user"]->getId(),
                "deleted" => 0
            ]
        );

        if (empty($chartOfAccountGroupData)) {
            http_response_code(500);
            echo json_encode(["error" => "grupo de contas não foi importado corretamente"]);
            die;
        }

        $chartOfAccountGroupData = array_map(function ($item) {
            return (array) $item->data();
        }, $chartOfAccountGroupData);

        $groupChartOfAccount = [];
        $groupValue = "";

        foreach ($data as $array) {
            if (preg_match("/^\d+$/", $array[1])) {
                $groupValue = $array[1];
            }

            if (preg_match("/[\d\.,]+/", $array[1])) {
                if (!empty($groupValue)) {
                    $groupChartOfAccount[$groupValue][] = $array;
                }
            }
        }

        foreach ($chartOfAccountGroupData as $value) {
            if (!empty($groupChartOfAccount[$value["account_number"]])) {
                $groupChartOfAccount[$value["account_number"]] = array_map(function ($array) use ($value) {
                    $array[] = $value["id"];
                    return $array;
                }, $groupChartOfAccount[$value["account_number"]]);
            }
        }

        foreach ($groupChartOfAccount as &$array) {
            array_shift($array);
        }

        foreach ($groupChartOfAccount as $arrayA) {
            foreach ($arrayA as &$arrayB) {
                $arrayB = array_map(function ($item) {
                    $item[1] = preg_replace("/,/", ".", $item[1]);
                    return $item;
                }, $arrayB);
            }
        }

        foreach ($groupChartOfAccount as $arrayA) {
            foreach ($arrayA as $arrayB) {
                $chartOfAccount = new ChartOfAccount();
                $params["uuid"] = Uuid::uuid4();
                $params["account_name"] = $arrayB[0];
                $params["account_number"] = $arrayB[1];
                $params["id_chart_of_account_group"] = $arrayB[2];

                $response = $chartOfAccount->persistData($params);
                if (!$response) {
                    http_response_code(500);
                    echo $chartOfAccount->message->json();
                    Connect::getInstance()->rollBack();
                    die;
                }
            }
        }

        Connect::getInstance()->commit();
        $chartOfAccount = new ChartOfAccount();
        $chartOfAccountData = $chartOfAccount->findAllChartOfAccountJoinChartOfAccountGroup(
            [
                "uuid",
                "account_name",
                "account_number"
            ],
            [
                "account_name AS account_name_group"
            ],
            [
                "id_company" => $responseUserAndCompany["company_id"],
                "id_user" => $responseUserAndCompany["user"]->getId(),
                "deleted" => 0
            ]
        );

        if (empty($chartOfAccountData)) {
            http_response_code(500);
            echo json_encode(["error" => "registro plano de contas não encontrado"]);
            Connect::getInstance()->rollBack();
            die;
        }

        $chartOfAccountData = array_map(function ($item) {
            $item->edit_btn = '<a class="icons" href="' . url("/admin/balance-sheet/chart-of-account/update/" . $item->getUuid() . "") . '"><i class="fas fa-edit" aria-hidden="true"></i></a>';
            $item->delete_btn = '<a class="icons" href="#"><i style="color:#ff0000" class="fa fa-trash" aria-hidden="true"></i></a>';
            $item->uuid = $item->getUuid();
            return (array) $item->data();
        }, $chartOfAccountData);

        echo json_encode(["success" => "arquivo importado com sucesso", "data" => $chartOfAccountData]);
    }

    public function chartOfAccountFormDelete()
    {
        verifyRequestHttpOrigin($this->getServer()->getServerByKey("HTTP_ORIGIN"));
        $requestPost = $this->getRequests()->setRequiredFields(["uuid"])->getAllPostData();

        $chartOfAccount = new ChartOfAccount();
        $response = $chartOfAccount->updateChartOfAccountByUuid([
            "uuid" => $requestPost["uuid"],
            "deleted" => 1
        ]);

        if (!$response) {
            http_response_code(500);
            echo $chartOfAccount->message->json();
            die;
        }

        echo json_encode(["success" => "registro deletado com sucesso"]);
    }

    public function chartOfAccountFormUpdate(array $data)
    {
        if ($this->getServer()->getServerByKey("REQUEST_METHOD") == "POST") {
            verifyRequestHttpOrigin($this->getServer()->getServerByKey("HTTP_ORIGIN"));
            $requestPost = $this->getRequests()->setRequiredFields([
                "csrfToken",
                "accountName",
                "accountValue",
                "chartOfAccountGroupSelect",
                "uuid"
            ])->getAllPostData();

            $chartOfAccountGroup = new ChartOfAccountGroup();
            $chartOfAccountGroup->setUuid($requestPost["chartOfAccountGroupSelect"]);
            $chartOfAccountGroupData = $chartOfAccountGroup->findChartOfAccountGroupByUuid(["id"]);

            if (empty($chartOfAccountGroupData)) {
                http_response_code(500);
                echo json_encode(["error" => "grupo de contas inexistente"]);
                die;
            }

            $chartOfAccount = new ChartOfAccount();
            $response = $chartOfAccount->updateChartOfAccountByUuid([
                "uuid" => $requestPost["uuid"],
                "id_chart_of_account_group" => $chartOfAccountGroupData->id,
                "account_name" => $requestPost["accountName"],
                "account_number" => $requestPost["accountValue"]
            ]);

            if (!$response) {
                http_response_code(500);
                echo $chartOfAccount->message->json();
                die;
            }

            echo json_encode(["success" => true, "url" => url("/admin/balance-sheet/chart-of-account")]);
            die;
        }

        if (!Uuid::isValid($data["uuid"])) {
            redirect("/admin/balance-sheet/chart-of-account");
        }

        $responseInitializeUserAndCompany = initializeUserAndCompanyId();
        $chartOfAccountGroup = new ChartOfAccountGroup();
        $chartOfAccountGroupData = $chartOfAccountGroup->findAllChartOfAccountGroup(
            [
                "id",
                "uuid",
                "account_name"
            ],
            [
                "id_user" => $responseInitializeUserAndCompany["user"]->getId(),
                "id_company" => $responseInitializeUserAndCompany["company_id"],
                "deleted" => 0
            ]
        );

        $chartOfAccount = new ChartOfAccount();
        $chartOfAccount->setUuid($data["uuid"]);
        $chartOfAccountData = $chartOfAccount->findChartOfAccountByUuid(["id_chart_of_account_group", "account_name", "account_number"]);

        echo $this->view->render("admin/chart-of-account-update", [
            "userFullName" => showUserFullName(),
            "chartOfAccountData" => $chartOfAccountData,
            "chartOfAccountGroupData" => $chartOfAccountGroupData,
            "endpoints" => []
        ]);
    }

    public function exportChartOfAccountModelExcelFile()
    {
        verifyRequestHttpOrigin($this->getServer()->getServerByKey("HTTP_ORIGIN"));
        $chartOfAccountModel = new ChartOfAccountModel();
        $chartOfAccountModelData = $chartOfAccountModel->findAllChartOfAccountModel(["account_name", "account_number"], true);

        $responseData = [];
        foreach ($chartOfAccountModelData as &$array) {
            $array = array_values($array);
            $responseData[] = $array;
        }

        $spreadSheet = new Spreadsheet();
        $sheet = $spreadSheet->getActiveSheet();

        foreach ($responseData as $rowKey => $row) {
            foreach ($row as $colKey => $col) {
                $sheet->setCellValue([$colKey + 1, $rowKey + 1], $col);
            }
        }

        header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
        header('Content-Disposition: attachment;filename="modelo-plano-de-contas.xlsx"');
        header('Cache-Control: max-age=0');

        $writer = new Xlsx($spreadSheet);
        $writer->save('php://output');
    }

    public function chartOfAccount()
    {
        $responseInitializeUserAndCompany = initializeUserAndCompanyId();
        if ($this->getServer()->getServerByKey("REQUEST_METHOD") == "POST") {
            verifyRequestHttpOrigin($this->getServer()->getServerByKey("HTTP_ORIGIN"));
            $requestPost = $this->getRequests()->setRequiredFields([
                "accountValue",
                "accountName",
                "chartOfAccountGroupSelect",
                "csrfToken"
            ])->getAllPostData();

            if (empty($responseInitializeUserAndCompany["company_id"])) {
                http_response_code(500);
                echo json_encode(["error" => "selecione uma empresa antes de lançar uma conta nova"]);
                die;
            }

            $chartOfAccountGroup = new ChartOfAccountGroup();
            $chartOfAccountGroup->setUuid($requestPost["chartOfAccountGroupSelect"]);
            $chartOfAccountGroupData = $chartOfAccountGroup->findChartOfAccountGroupByUuid(
                [
                    "id",
                    "account_name"
                ]
            );

            if (empty($chartOfAccountGroupData)) {
                http_response_code(500);
                echo json_encode(["error" => "grupo de contas inexistente"]);
                die;
            }

            $uuid = Uuid::uuid4();
            $chartOfAccount = new ChartOfAccount();
            $response = $chartOfAccount->persistData([
                "id_user" => $responseInitializeUserAndCompany["user"],
                "id_chart_of_account_group" => $chartOfAccountGroupData->id,
                "id_company" => $responseInitializeUserAndCompany["company_id"],
                "uuid" => $uuid,
                "account_number" => $requestPost["accountValue"],
                "account_name" => $requestPost["accountName"],
                "deleted" => 0
            ]);

            if (empty($response)) {
                http_response_code(500);
                echo $chartOfAccount->message->json();
                die;
            }

            $chartOfAccount = new ChartOfAccount();
            $chartOfAccount->setUuid($uuid);
            $chartOfAccountData = $chartOfAccount->findChartOfAccountByUuid();

            if (empty($chartOfAccountData)) {
                http_response_code(500);
                echo json_encode(["error" => "Erro ao tentar encontrar o registro"]);
                die;
            }

            echo json_encode(["success" => "conta criada com sucesso", "data" => [
                "uuid" => $chartOfAccountData->getUuid(),
                "accountName" => $chartOfAccountData->account_name,
                "accountNameGroup" => $chartOfAccountGroupData->account_name,
                "accountValue" => $chartOfAccountData->account_number,
                "editBtn" => '<a class="icons" href="' . url("/admin/balance-sheet/chart-of-account/update/" . $chartOfAccountData->getUuid() . "") . '"><i class="fas fa-edit" aria-hidden="true"></i></a>',
                "excludeBtn" => '<a class="icons" href="#"><i style="color:#ff0000" class="fa fa-trash" aria-hidden="true"></i></a>'
            ]]);
            die;
        }

        $params = [
            "id_company" => $responseInitializeUserAndCompany["company_id"],
            "id_user" => $responseInitializeUserAndCompany["user"]->getId(),
            "deleted" => 0
        ];

        $chartOfAccount = new ChartOfAccount();
        $chartOfAccount = new ChartOfAccount();
        $chartOfAccountData = $chartOfAccount->findAllChartOfAccountJoinChartOfAccountGroup(
            [
                "uuid",
                "account_name",
                "account_number"
            ],
            [
                "account_name AS account_name_group"
            ],
            $params
        );

        $chartOfAccountGroup = new ChartOfAccountGroup();
        $chartOfAccountGroupData = $chartOfAccountGroup->findAllChartOfAccountGroup(
            [
                "uuid",
                "account_name",
            ],
            $params
        );

        echo $this->view->render("admin/chart-of-account", [
            "userFullName" => showUserFullName(),
            "endpoints" => ["/admin/balance-sheet/chart-of-account"],
            "chartOfAccountData" => $chartOfAccountData,
            "chartOfAccountGroupData" => $chartOfAccountGroupData
        ]);
    }
}
