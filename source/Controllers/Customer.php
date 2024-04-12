<?php
namespace Source\Controllers;

use Source\Core\Controller;

/**
 * Customer Controllers
 * @link 
 * @author Roberto Dorado <robertodorado7@gmail.com>
 * @package Source\Controllers
 */
class Customer extends Controller
{
    /**
     * Customer constructor
     */
    public function __construct()
    {
        parent::__construct();
    }

    public function customerSubscribeForm()
    {
        echo $this->view->render("admin/subscribe-form", []);
    }
}
