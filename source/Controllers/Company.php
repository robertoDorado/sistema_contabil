<?php
namespace Source\Controllers;

use Source\Core\Controller;

/**
 * Company Controllers
 * @link 
 * @author Roberto Dorado <robertodorado7@gmail.com>
 * @package Source\Controllers
 */
class Company extends Controller
{
    /**
     * Company constructor
     */
    public function __construct()
    {
        parent::__construct();
    }

    public function companyRegister()
    {
        if (empty(session()->user)) {
            redirect("/admin/login");
        }

        echo $this->view->render("admin/company-form", [
            "userFullName" => showUserFullName(),
            "endpoints" => []
        ]);
    }

    public function warningEmptyCompany()
    {
        if (empty(session()->user)) {
            redirect("/admin/login");
        }

        echo $this->view->render("admin/warning-empty-company", [
            "userFullName" => showUserFullName(),
            "endpoints" => []
        ]);
    }
}
