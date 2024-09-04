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
        echo $this->view->render("admin/home", [
            "userFullName" => showUserFullName(),
            "endpoints" => []
        ]);
    }

    public function error()
    {
        echo $this->view->render("admin/404", [
            "userFullName" => showUserFullName(),
            "endpoints" => []
        ]);
    }
}
