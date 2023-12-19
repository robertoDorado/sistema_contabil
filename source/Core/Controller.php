<?php

namespace Source\Core;

use Source\Core\View;
use Source\Support\Message;
use Source\Support\RequestFiles;
use Source\Support\RequestPost;

class Controller
{
    /** @var View */
    protected $view;

    /** @var Message */
    protected $message;

    /** @var array Armazena a variavel global $_GET */
    protected $getData;

    /** @var Server Objeto Server */
    protected $server;

    /** @var RequestFiles */
    protected $fileData;

    /** @var RequestPost */
    protected $postData;

    /** @var Session */
    protected $session;

    /**
     * @param string $viewPath
     * @return void
     */
    public function __construct(string $viewPath = CONF_VIEW_PATH . "/" . CONF_VIEW_THEME)
    {
        $this->server = new Server();
        $this->getData = $_GET;
        $this->message = new Message();
        $this->view = new View($viewPath);
        $this->fileData = new RequestFiles();
        $this->session = new Session();
        $this->session->csrf();
        $this->postData = new RequestPost($this->session);
    }

    public function getCurrentSession()
    {
        return $this->session;
    }

    public function getRequestPost(): RequestPost
    {
        return $this->postData;
    }

    public function getRequestFiles(): RequestFiles
    {
        return $this->fileData;
    }

    public function getServer(): Server
    {
        return $this->server;
    }

    public function get($key, $default = null)
    {
        if (isset($this->getData[$key])) {
            return $this->getData[$key];
        } else {
            return $default;
        }
    }

    public function has($key): bool
    {
        return isset($this->getData[$key]);
    }
}
