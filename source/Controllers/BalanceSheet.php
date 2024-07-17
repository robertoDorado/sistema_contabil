<?php
namespace Source\Controllers;

use Source\Core\Controller;

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

    public function chartOfAccount()
    {
        echo $this->view->render("admin/chart-of-account", [
            "userFullName" => showUserFullName(),
            "endpoints" => ["/admin/balance-sheet/chart-of-account"]
        ]);
    }
}
