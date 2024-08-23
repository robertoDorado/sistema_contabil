<?php

namespace Source\Controllers;

use Source\Core\Controller;
use Source\Domain\Model\Company;
use Source\Support\Invoice as SupportInvoice;

/**
 * Invoice Controllers
 * @link 
 * @author Roberto Dorado <robertodorado7@gmail.com>
 * @package Source\Controllers
 */
class Invoice extends Controller
{
    /**
     * Invoice constructor
     */
    public function __construct()
    {
        parent::__construct();
    }

    public function invoiceReport()
    {
        $responseInitializaUserAndCompany = initializeUserAndCompanyId();
        if ($this->getServer()->getServerByKey("REQUEST_METHOD") == "POST") {
            $requestPost = $this->getRequests()->getAllPostData();
            $requestFile = $this->getRequestFiles()->getAllFiles();

            if (empty($responseInitializaUserAndCompany["company_id"])) {
                http_response_code(500);
                echo json_encode(["error" => "selecione uma empresa antes de emitir uma nota fiscal"]);
                die;
            }

            $requestPost["companyDocument"] = preg_replace("/[^\d]+/", "", $requestPost["companyDocument"]);
            $certificate = $requestFile["pfxFile"]["tmp_name"];

            $invoice = new SupportInvoice([
                "tpAmb" => $requestPost["environment"],
                "companyName" => $requestPost["companyName"],
                "companyDocument" => $requestPost["companyDocument"],
                "companyState" => $requestPost["companyState"],
                "certPfx" => $certificate,
                "certPassword" => $requestPost["certPassword"],
            ]);
            $response = $invoice->isValidCertPfx();
            var_dump($response);
            die;
        }

        $company = new Company();
        $company->setId($responseInitializaUserAndCompany["company_id"]);
        $companyData = $company->findCompanyById();

        echo $this->view->render("admin/invoice-form", [
            "userFullName" => showUserFullName(),
            "endpoints" => ["/admin/invoice/form"],
            "companyData" => $companyData
        ]);
    }
}
