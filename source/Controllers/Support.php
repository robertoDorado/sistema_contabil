<?php
namespace Source\Controllers;

use Source\Core\Controller;

/**
 * Support Controllers
 * @link 
 * @author Roberto Dorado <robertodorado7@gmail.com>
 * @package Source\Controllers
 */
class Support extends Controller
{
    /**
     * Support constructor
     */
    public function __construct()
    {
        parent::__construct();
    }

    public function openTicket()
    {
        echo $this->view->render("admin/open-ticket", [
            "userFullName" => showUserFullName(),
            "endpoints" => ["/admin/support/dashboard"]
        ]);
    }

    public function supportDashboard()
    {
        echo $this->view->render("admin/support-dashboard", [
            "userFullName" => showUserFullName(),
            "endpoints" => ["/admin/support/dashboard"]
        ]);
    }
}
