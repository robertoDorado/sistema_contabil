<?php
namespace Source\Controllers;

use Source\Core\Controller;

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

        echo $this->view->render("admin/home", [
            "userFullName" => showUserFullName(),
            "endpoints" => []
        ]);
    }

    public function error()
    {
        redirect("/admin");
    }
}
