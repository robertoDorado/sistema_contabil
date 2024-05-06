<?php
namespace Source\Controllers;

use CnabPHP\Remessa;
use Exception;
use Source\Core\Controller;

/**
 * CnabBill Controllers
 * @link 
 * @author Roberto Dorado <robertodorado7@gmail.com>
 * @package Source\Controllers
 */
class CnabBill extends Controller
{
    /**
     * CnabBill constructor
     */
    public function __construct()
    {
        parent::__construct();
    }

    public function generateCnabFile()
    {
        header("Content-Type: application/json; charset=utf-8");
        $data = @file_get_contents("php://input");
        $data = json_decode($data, true);

        if (empty($data)) {
            http_response_code(500);
            echo json_encode(["error" => "Dados de envio não podem estar vazio"]);
            die;
        }

        $requiredFields = ["banco", "banco_numero", "numero_inscricao", "tipo_inscricao", "detalhe"];
        foreach($requiredFields as $field) {
            if (empty($data[$field])) {
                http_response_code(500);
                echo json_encode(["error" => "Campo {$field} é obrigatório"]);
                die;
            }
        }

        if (!preg_match("/^\d+$/", $data["banco_numero"])) {
            http_response_code(500);
            echo json_encode(["error" => "A chave banco_numero é inválida"]);
            die;
        }

        $validateDataObject = [
            "numero_inscricao" => function($value) {
                $maskDocument = [
                    "/^(\d{3})\.(\d{3})\.(\d{3})-(\d{2})$/", 
                    "/^(\d{2})\.(\d{3})\.(\d{3})\/(\d{4})-(\d{2})$/"
                ];

                foreach ($maskDocument as $maskValue) {
                    if (preg_match($maskValue, $value)) {
                        return $value;
                    }
                }
                
                http_response_code(500);
                echo json_encode(["error" => "Número de inscrição inválido"]);
                die;
            },

            "tipo_inscricao" => function($value) use ($data) {
                if (!preg_match("/^\d{1}$/", $value)) {
                    http_response_code(500);
                    echo json_encode(["error" => "Tipo de inscrição inválida"]);
                    die;
                }

                $validSubscription = [1, 2];
                if (!in_array($value, $validSubscription)) {
                    http_response_code(500);
                    echo json_encode(["error" => "Valor de inscrição inválida"]);
                    die;
                }

                if (preg_match("/^(\d{3})\.(\d{3})\.(\d{3})-(\d{2})$/", $data["numero_inscricao"])) {
                    if ($value == 2) {
                        http_response_code(500);
                        echo json_encode(["error" => "Tipo de inscrição não aponta para cpf."]);
                        die;
                    }
                }

                if (preg_match("/^(\d{2})\.(\d{3})\.(\d{3})\/(\d{4})-(\d{2})$/", $data["numero_inscricao"])) {
                    if ($value == 1) {
                        http_response_code(500);
                        echo json_encode(["error" => "Tipo de inscrição não aponta para cnpj."]);
                        die;
                    }
                }

                return $value;
            }
        ];

        $verifyLayoutBank = [
            "banco" => [
                "bancoDoBrasil" => function($value) {
                    $value = "Cnab240";
                    return $value;
                },

                "bradesco" => function($value) {
                    $value = "Cnab400";
                    return $value;
                },

                "caixa" => function($value) {
                    $value = "Cnab240_SIGCB";
                    return $value;
                },

                "itau" => function($value) {
                    $value = "Cnab400";
                    return $value;
                },

                "santander" => function($value) {
                    $value = "Cnab240";
                    return $value;
                },

                "siccob" => function($value) {
                    $value = "Cnab400";
                    return $value;
                },

                "sicredi" => function($value) {
                    $value = "Cnab400";
                    return $value;
                },

                "uniPrime" => function($value) {
                    $value = "Cnab400";
                    return $value;
                },
                
                "unicred" => function($value) {
                    $value = "Cnab400";
                    return $value;
                },

                "c6Bank" => function($value) {
                    $value = "Cnab400";
                    return $value;
                },

                "bancoAbc" => function($value) {
                    $value = "Cnab240";
                    return $value;
                },

                "bancoVotorantim" => function($value) {
                    $value = "Cnab240";
                    return $value;
                }
            ]
        ];

        foreach ($data as $key => &$value) {
            if (!empty($validateDataObject[$key])) {
                $value = $validateDataObject[$key]($value);
            }

            if (!is_array($value)) {
                if (!empty($verifyLayoutBank["banco"][$value])) {
                    $value = $verifyLayoutBank["banco"][$value]($value);
                }
            }
        }
        
        try {
            $bankLayout = $data["banco"];
            unset($data["banco"]);
            
            $file = new Remessa($data["banco_numero"], $bankLayout, $data);
            $part = $file->addLote(array('tipo_servico' => 1));
            
            $part->inserirDetalhe($data["detalhe"]);
            echo json_encode(["success" => $part->getText()]);
        } catch (\Exception $e) {
            echo json_encode(["error" => "Erro interno ao processar a requisição"]);
            throw new Exception($e->getMessage());
        }
    }
}
