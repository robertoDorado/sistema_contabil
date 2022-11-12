<?php

namespace Source\Core;

use Source\Core\View;
use Source\Support\Message;

class Controller
{

    /** @var View */
    protected $view;

    /** @var Message */
    protected $message;

    /**
     * @param string $viewPath
     * @return void
     */
    public function __construct(string $viewPath = CONF_VIEW_PATH . "/" . CONF_VIEW_THEME)
    {
        $this->message = new Message();
        $this->view = new View($viewPath);
    }
}
