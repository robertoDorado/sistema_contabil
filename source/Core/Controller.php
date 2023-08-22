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

    /** @var array Armazena a variavel global $_GET */
    private $parameters;

    /**
     * @param string $viewPath
     * @return void
     */
    public function __construct(string $viewPath = CONF_VIEW_PATH . "/" . CONF_VIEW_THEME)
    {
        $this->parameters = $_GET;
        $this->message = new Message();
        $this->view = new View($viewPath);
    }

    public function get($key, $default = null) {
        if (isset($this->parameters[$key])) {
            return $this->parameters[$key];
        } else {
            return $default;
        }
    }

    public function has($key) {
        return isset($this->parameters[$key]);
    }
}
