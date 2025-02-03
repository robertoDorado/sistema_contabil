<?php
namespace Source\Controllers;

use Source\Core\Controller;

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
        echo $this->view->render("admin/tax-regime", [
            "userFullName" => showUserFullName(),
            "endpoints" => ["/admin/tax-regime/form"],
        ]);
    }
}
