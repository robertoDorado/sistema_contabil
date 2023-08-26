<?php

namespace Source\Support;

use Source\Core\Session;

/**
 * RequestPost Source\Support
 * @link 
 * @author Roberto Dorado <robertodorado7@gmail.com>
 * @package Source\Support
 */
class RequestPost
{
    /** @var array Aramazena a variavel global $_POST */
    protected array $post;

    /** @var Session */
    protected $session;

    /**
     * RequestPost constructor
     */
    public function __construct(Session $session)
    {
        $this->post = $_POST;
        $this->post = array_map(function ($value) {
            return filter_var($value, FILTER_SANITIZE_FULL_SPECIAL_CHARS);
        }, $this->post);
        $this->session = !empty($session) ? $session : null;
        array_walk($this->post, [$this, 'formConfigure']);
    }

    private function formConfigure(&$value, $key)
    {
        if ($key == "csrfToken" || $key == "csrf_token") {
            if ($value != $this->session->csrf_token) {
                throw new \Exception("Token csrf inválido");
            }
        }

        if ($key == "email" || $key == "confirmEmail") {
            if (!preg_match("/^[a-zA-Z0-9._%+-]+@[a-zA-Z0-9.-]+\.[a-zA-Z]{2,}$/", $value)) {
                throw new \Exception("Endereço de e-mail inválido");
            }
        }

        if ($key == "userName") {
            $userName = explode(" ", $value);
            $value = count($userName) > 0 ? implode("", $userName) : $value;
        }

        if ($key === 'password' || $key === 'confirmPassword') {
            $value = password_hash($value, PASSWORD_DEFAULT);
        }
    }

    public function getPost($key)
    {
        if (isset($this->post[$key]) && !empty($this->post[$key])) {
            return $this->post[$key];
        } else {
            throw new \Exception("chave " . $key . " POST não existe");
        }
    }

    public function getAllPostData()
    {
        return $this->post;
    }
}
