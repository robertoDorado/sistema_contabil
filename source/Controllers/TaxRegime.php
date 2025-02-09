<?php

namespace Source\Controllers;

use Ramsey\Uuid\Nonstandard\Uuid;
use Source\Core\Controller;
use Source\Domain\Model\TaxRegimeModel;

/**
 * TaxRegime Controllers
 * @link 
 * @author Roberto Dorado <robertodorado7@gmail.com>
 * @package Source\Controllers
 */
class TaxRegime extends Controller
{
    /**
     * TaxRegime constructor
     */
    public function __construct()
    {
        parent::__construct();
    }

    public function taxRegimeForm()
    {
        if ($this->getServer()->getServerByKey("REQUEST_METHOD") === "POST") {
            verifyRequestHttpOrigin($this->getServer()->getServerByKey("HTTP_ORIGIN"));
            $autoFillTrigger = $this->getRequests()->getPost("fill");

            if (empty($autoFillTrigger)) {
                http_response_code(400);
                echo json_encode([
                    "error" => "valor do auto preenchimento inválido"
                ]);
                die;
            }

            $responseInitializeUserAndCompany = initializeUserAndCompanyId();
            if (empty($responseInitializeUserAndCompany["company_id"])) {
                http_response_code(400);
                echo json_encode([
                    "error" => "selecione uma empresa antes de realizar o auto preenchimento tributário"
                ]);
                die;
            }

            $allTaxRegimes = ["Simples Nacional", "Lucro Presumido", "Lucro Real", "MEI"];
            $taxRegimeModel = new TaxRegimeModel();
            $responseTaxRegimeModel = null;

            $arrayUuid = [];
            $arrayEdit = [];
            $arrayDelete = [];
            $taxRegimeValue = [];
            $foundData = [];

            foreach ($allTaxRegimes as $taxRegime) {
                $taxRegimeModel = new TaxRegimeModel();
                $id = $taxRegimeModel->findTaxRegimeByName($taxRegime);

                if (!empty($id)) {
                    array_push($foundData, $taxRegime);
                    continue;
                }

                $uuid = Uuid::uuid4();
                $responseTaxRegimeModel = $taxRegimeModel->persistData([
                    "uuid" => $uuid,
                    "id_user" => $responseInitializeUserAndCompany["user"],
                    "id_company" => $responseInitializeUserAndCompany["company_id"],
                    "tax_regime_value" => $taxRegime,
                    "created_at" => date("Y-m-d H:i:s"),
                    "updated_at" => date("Y-m-d H:i:s"),
                    "deleted" => 0
                ]);

                if (empty($responseTaxRegimeModel)) {
                    http_response_code(400);
                    echo $responseTaxRegimeModel->message->json();
                    die;
                }

                array_push($arrayUuid, $uuid);
                array_push($taxRegimeValue, $taxRegime);
                array_push($arrayEdit, "<a class='icons' href=" . url("/admin/tax-regime/form/{$uuid}") . "><i class='fas fa-edit' aria-hidden='true'></i>");
                array_push($arrayDelete, "<a class='icons' href='#'><i style='color:#ff0000' class='fa fa-trash' aria-hidden='true'></i></a>");
            }

            $responseData = [];
            $responseData['uuid'] = $arrayUuid;
            $responseData['tax_regime_value'] = $taxRegimeValue;
            $responseData['edit'] = $arrayEdit;
            $responseData['delete'] = $arrayDelete;

            echo empty($foundData) ? json_encode([
                "success" => "auto preenchimento realizado com sucesso",
                "data" => json_encode($responseData)
            ]) : json_encode([
                "warning" => "as contas " . implode(", ", $foundData) . " não foram importadas no sistema",
                "data" => json_encode($responseData)
            ]);
            die;
        }

        $taxRegimeModel = new TaxRegimeModel();
        $taxRegimeModelData = $taxRegimeModel->findAllTaxRegimeModel([
            "uuid",
            "tax_regime_value"
        ]);

        echo $this->view->render("admin/tax-regime", [
            "userFullName" => showUserFullName(),
            "endpoints" => ["/admin/tax-regime/form"],
            "taxRegimeModelData" => $taxRegimeModelData
        ]);
    }
}
