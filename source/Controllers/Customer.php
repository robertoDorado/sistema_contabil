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

    public function thanksPurchase()
    {
        $message = "Agradecemos pela sua compra, 
        você pode fazer login usando o seu nome de usuário ou e-mail.";
        echo $this->view->render("site/thanks-purchase", [
            "message" => $message
        ]);
    }

    public function customerSubscribeForm()
    {
        $csrfToken = session()->csrf_token;
        echo $this->view->render("site/subscribe-form", [
            "csrfToken" => $csrfToken
        ]);
    }
}
