<?php
namespace Source\Controllers;

use Source\Core\Controller;

/**
 * Admin Controllers
 * @link 
 * @author Roberto Dorado <robertodorado7@gmail.com>
 * @package Source\Controllers
 */
class Admin extends Controller
{
    /**
     * Admin constructor
     */
    public function __construct()
    {
        parent::__construct();
    }

    public function login()
    {
        echo $this->view->render("admin/login", []);
    }

    public function index()
    {
        echo $this->view->render("admin/home", []);
    }

}
