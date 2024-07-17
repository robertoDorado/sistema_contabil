<?php

namespace Source\Controllers;

use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;
use Ramsey\Uuid\Nonstandard\Uuid;
use Source\Core\Controller;
use Source\Domain\Model\ChartOfAccount;
use Source\Domain\Model\ChartOfAccountModel;

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
            $requestPost = $this->getRequests()->setRequiredFields([
                "accountValue",
                "accountName",
                "csrfToken"
            ])->getAllPostData();

            if (empty($responseInitializeUserAndCompany["company_id"])) {
                http_response_code(500);
                echo json_encode(["error" => "selecione uma empresa antes de lançar uma conta nova"]);
                die;
            }

            $uuid = Uuid::uuid4();
            $chartOfAccount = new ChartOfAccount();
            $response = $chartOfAccount->persistData([
                "id_user" => $responseInitializeUserAndCompany["user"],
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
                "accountValue" => $chartOfAccountData->account_number,
                "editBtn" => '<a class="icons" href="' . url("/admin/balance-sheet/chart-of-account/" . $chartOfAccountData->getUuid() . "") . '"><i class="fas fa-edit" aria-hidden="true"></i></a>',
                "excludeBtn" => '<a class="icons" href="#"><i style="color:#ff0000" class="fa fa-trash" aria-hidden="true"></i></a>'
            ]]);
            die;
        }

        $chartOfAccount = new ChartOfAccount();
        $chartOfAccountData = $chartOfAccount->findAllChartOfAccount(
            [
                "uuid",
                "account_name",
                "account_number"
            ],
            [
                "id_company" => $responseInitializeUserAndCompany["company_id"],
                "id_user" => $responseInitializeUserAndCompany["user"]->getId(),
                "deleted" => 0
            ]
        );

        echo $this->view->render("admin/chart-of-account", [
            "userFullName" => showUserFullName(),
            "endpoints" => ["/admin/balance-sheet/chart-of-account"],
            "chartOfAccountData" => $chartOfAccountData
        ]);
    }
}
