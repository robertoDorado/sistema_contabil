<?php

namespace Source\Controllers;

use Ramsey\Uuid\Nonstandard\Uuid;
use Source\Core\Controller;
use Source\Domain\Model\TaxRegime as ModelTaxRegime;
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

    public function setTaxRegime()
    {
        verifyRequestHttpOrigin($this->getServer()->getServerByKey("HTTP_ORIGIN"));
        $responseInitializeUserAndCompany = initializeUserAndCompanyId();

        if (empty($responseInitializeUserAndCompany["company_id"])) {
            http_response_code(400);
            echo json_encode([
                "error" => "selecione uma empresa antes de criar um regime tributário"
            ]);
            die;
        }

        $postData = $this->getRequests()->setRequiredFields(["taxRegimeSelectMultiple", "csrfToken"])->getAllPostData();
        if (empty($postData["taxRegimeSelectMultiple"]) || empty($postData["csrfToken"])) {
            http_response_code(400);
            echo json_encode(["error" => "formulário inválido"]);
            die;
        }

        $taxRegimeModel = new TaxRegimeModel();
        $taxRegimeModelData = $taxRegimeModel->findTaxRegimeByUuid(["id"], $postData["taxRegimeSelectMultiple"]);
        
        if (empty($taxRegimeModelData)) {
            http_response_code(400);
            echo json_encode([
                "error" => "modelo de regime tributário não encontrado"
            ]);
            die;
        }

        $uuid = Uuid::uuid4();
        if (!empty($postData["updateTaxRegime"])) {
            $taxRegime = new ModelTaxRegime();
            $establishedTaxRegime = $taxRegime->findTaxRegimeByUuid([], $postData["updateTaxRegime"]);
            $establishedTaxRegime->setRequiredFields(["tax_regime_id"]);
            $establishedTaxRegime->tax_regime_id = $taxRegimeModelData->id;
            $establishedTaxRegime->save();

            echo json_encode(["success" => "regime tributário atualizado com sucesso"]);
            die;
        }
        
        $taxRegime = new ModelTaxRegime();
        $responseTaxRegime = $taxRegime->persistData([
            "uuid" => $uuid,
            "id_user" => $responseInitializeUserAndCompany["user"],
            "id_company" => $responseInitializeUserAndCompany["company_id"],
            "tax_regime_id" => $taxRegimeModelData->id,
            "created_at" => date("Y-m-d H:i:s"),
            "updated_at" => date("Y-m-d H:i:s"),
            "deleted" => 0
        ]);

        if (empty($responseTaxRegime)) {
            http_response_code(400);
            echo $taxRegime->message->json();
            die;
        }

        echo json_encode(
            [
                "success" => "regime tributário da empresa configurado com sucesso", 
                "last_uuid" => $uuid
            ]
        );
    }

    public function taxRegimeFormCreate()
    {
        verifyRequestHttpOrigin($this->getServer()->getServerByKey("HTTP_ORIGIN"));
        $responseInitializeUserAndCompany = initializeUserAndCompanyId();

        if (empty($responseInitializeUserAndCompany["company_id"])) {
            http_response_code(400);
            echo json_encode([
                "error" => "selecione uma empresa antes de criar um regime tributário"
            ]);
            die;
        }

        $postData = $this->getRequests()->setRequiredFields(["csrfToken", "taxRegimeValue"])->getAllPostData();
        $validatePostData = array_filter($postData, function ($item) {
            return empty($item);
        });

        if (!empty($validatePostData)) {
            http_response_code(400);
            echo json_encode(["error" => "formulário inválido"]);
            die;
        }

        $taxRegimeModel = new TaxRegimeModel();
        $responseTaxRegimeModel = $taxRegimeModel->persistData([
            "uuid" => Uuid::uuid4(),
            "id_user" => $responseInitializeUserAndCompany["user"],
            "id_company" => $responseInitializeUserAndCompany["company_id"],
            "tax_regime_value" => $postData["taxRegimeValue"],
            "created_at" => date("Y-m-d H:i:s"),
            "updated_at" => date("Y-m-d H:i:s"),
            "deleted" => 0
        ]);

        if (empty($responseTaxRegimeModel)) {
            http_response_code(400);
            echo $responseTaxRegimeModel->message->json();
            die;
        }

        $taxRegimeModelData = $taxRegimeModel->findTaxRegimeById(
            [
                "uuid",
                "tax_regime_value"
            ],
            $taxRegimeModel->getId()
        );

        if (empty($taxRegimeModelData)) {
            http_response_code(400);
            echo json_encode(["error" => "registro não identificado"]);
            die;
        }

        echo json_encode(
            [
                "success" => "registro criado com sucesso",
                "data" => json_encode([
                    "uuid" => $taxRegimeModelData->getUuid(),
                    "tax_regime_value" => $taxRegimeModelData->tax_regime_value,
                    "edit" => "<a class='icons' href=" . url("/admin/tax-regime/form/{$taxRegimeModelData->getUuid()}") . "><i class='fas fa-edit' aria-hidden='true'></i>",
                    "delete" => "<a class='icons' href='#'><i style='color:#ff0000' class='fa fa-trash' aria-hidden='true'></i></a>"
                ])
            ]
        );
    }

    public function taxRegimeForm()
    {
        $responseInitializeUserAndCompany = initializeUserAndCompanyId();
        $params = [
            "id_company" => $responseInitializeUserAndCompany["company_id"],
            "id_user" => $responseInitializeUserAndCompany["user"]->getId()
        ];

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

            if (empty($responseInitializeUserAndCompany["company_id"])) {
                http_response_code(400);
                echo json_encode([
                    "error" => "selecione uma empresa antes de realizar o auto preenchimento tributário"
                ]);
                die;
            }

            $allTaxRegimes = ["Simples Nacional", "Lucro Presumido", "Lucro Real", "MEI"];
            $responseTaxRegimeModel = null;

            $arrayUuid = [];
            $arrayEdit = [];
            $arrayDelete = [];
            $taxRegimeValue = [];
            $foundData = [];

            foreach ($allTaxRegimes as $taxRegime) {
                $taxRegimeModel = new TaxRegimeModel();
                $id = $taxRegimeModel->findTaxRegimeByName($taxRegime, $params);

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
                "warning" => "as contas " . implode(", ", $foundData) . " já existem no sistema",
                "data" => json_encode($responseData)
            ]);
            die;
        }

        $taxRegimeModel = new TaxRegimeModel();
        $taxRegimeModelData = $taxRegimeModel->findAllTaxRegimeModel(
            [
                "id",
                "uuid",
                "tax_regime_value"
            ],
            $params
        );

        $taxRegime = new ModelTaxRegime();
        $establishedTaxRegime = $taxRegime->findTaxRegimeByTaxRegimeModelId(
            [
                "uuid AS uuid_tax_regime"
            ], 
            [
                "uuid AS uuid_tax_regime_model"
            ], 
            $params
        );

        echo $this->view->render("admin/tax-regime", [
            "userFullName" => showUserFullName(),
            "endpoints" => ["/admin/tax-regime/form"],
            "taxRegimeModelData" => $taxRegimeModelData,
            "establishedTaxRegime" => $establishedTaxRegime
        ]);
    }
}
