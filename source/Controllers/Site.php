<?php
namespace Source\Controllers;

use Source\Core\Controller;
use Source\Domain\Model\Company;
use Source\Domain\Model\User;

/**
 * Site C:\php-projects\sistema-contabil\source\Controllers
 * @link 
 * @author Roberto Dorado <robertodorado7@gmail.com>
 * @package Controllers
 */
class Site extends Controller
{
    /**
     * Site constructor
     */
    public function __construct()
    {
        parent::__construct();
    }

    public function index()
    {
        redirect("/admin");
    }

    public function admin()
    {
        if (empty(session()->user)) {
            redirect("/admin/login");
        }

        $user = new User();
        $user->setEmail(session()->user->user_email);
        $userData = $user->findUserByEmail();

        if (empty($userData)) {
            redirect("/admin/login");
        }

        $company = new Company();
        $company->id_user = $userData->id;
        $dataCompany = $company->findCompanyByUserId();
        
        if (empty($dataCompany)) {
            redirect("/admin/warning/empty-company");
        }

        echo $this->view->render("admin/home", [
            "userFullName" => showUserFullName(),
            "endpoints" => []
        ]);
    }

    public function error(array $data)
    {
        redirect("/admin");
    }
}
