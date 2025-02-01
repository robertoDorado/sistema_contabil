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

    public function error(array $data = [])
    {
        $checkErrorByStatusCode = [
            400 => "Solicitação Inválida: Verifique os Dados Enviados",
            401 => "Acesso Negado: Autenticação Necessária",
            403 => "Permissão Negada: Você Não Tem Acesso a Este Recurso",
            404 => "Página Não Encontrada: O Recurso Solicitado Não Existe",
            405 => "Operação Inválida: Método HTTP Não Suportado",
            408 => "Tempo Esgotado: A Solicitação Demorou Muito para Responder",
            429 => "Limite de Requisições Excedido: Tente Novamente em Instantes",
            500 => "Erro Interno: Algo Deu Errado no Servidor",
            501 => "Funcionalidade Não Suportada: O Servidor Não Reconhece Esta Solicitação",
            502 => "Falha de Comunicação: Erro de Gateway ou Proxy",
            503 => "Serviço Indisponível: O Sistema Está Temporariamente Fora do Ar",
            504 => "Tempo de Espera Excedido: O Servidor Não Respondeu a Tempo",
            505 => "Versão HTTP Incompatível: Atualize Seu Cliente ou API",
        ];

        $data["error_message"] = $checkErrorByStatusCode[$data["code"]] ?? "Erro desconhecido";
        echo $this->view->render("admin/error", [
            "userFullName" => showUserFullName(),
            "endpoints" => [],
            "errorMessage" => $data["error_message"],
            "code" => $data["code"] ?? "??"
        ]);
    }
}
