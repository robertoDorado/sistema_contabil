<?php
namespace Source\Controllers;

use Source\Core\Controller;
use Source\Domain\Model\Customer as ModelCustomer;

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

    public function updateDataCustomerForm()
    {
        if (empty(session()->user)) {
            redirect("/admin/login");
        }

        $customer = new ModelCustomer();
        $customer->email = session()->user->user_email;
        $customerData = $customer->findCustomerByEmail();
        
        echo $this->view->render("admin/customer-update-data-form", [
            "userFullName" => showUserFullName(),
            "endpoints" => ["/admin/customer/update-data/form"],
            "customerData" => $customerData
        ]);
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
