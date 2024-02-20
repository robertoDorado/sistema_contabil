<?php
namespace Source\Domain\Model;

use Exception;
use PDOException;
use Source\Core\Connect;
use Source\Models\CashFlow as ModelsCashFlow;

/**
 * CashFlow Domain\Model
 * @link 
 * @author Roberto Dorado <robertodorado7@gmail.com>
 * @package Source\Domain\Model
 */
class CashFlow
{
    /** @var ModelsCashFlow Objeto de persistencia na tabela cash_flow */
    private ModelsCashFlow $cashFlow;

    /** @var int Id da tabela cash_flow */
    private int $id;

    /**
     * CashFlow constructor
     */
    public function __construct()
    {
        $this->cashFlow = new ModelsCashFlow();
    }

    public function findAllCashFlow(array $columns = [])
    {
        $columns = empty($columns) ? "*" : implode(", ", $columns);
        $data = $this->cashFlow->find("", "", $columns)->fetch(true);
        if (empty($data)) {
            return json_encode(["cash_flow_empty" => "nenhum registro foi encontrado"]);
        }
        return $data;
    }

    public function getId()
    {
        if (empty($this->id)) {
            throw new Exception("id não atribuido");
        }
        return $this->id;
    }

    public function setId(int $id)
    {
        $this->id = $id;
    }

    public function findCashFlowById(array $columns = [])
    {
        $columns = empty($columns) ? "*" : implode(", ", $columns);
        $data = $this->cashFlow->findById($this->getId(), $columns);
        
        if (empty($data)) {
            return json_encode(["cashflow_not_found" => "registro fluxo de caixa não encontrado"]);
        }

        return $data;
    }

    public function dropCashFlowById(int $id)
    {
        $cashFlowData = $this->cashFlow->findById($id);
        if (!$cashFlowData->destroy()) {
            throw new PDOException($cashFlowData->fail()->getMessage());
        }
    }

    public function calculateBalance()
    {
        $data = $this->cashFlow->find("", "")->fetch(true);
        $balance = 0;
        
        if (!empty($data)) {
            foreach ($data as $value) {
                $balance += $value->getEntry();
            }
        }

        return $balance;
    }

    public function persistData(array $data)
    {
        if (empty($data)) {
            return json_encode(["invalid_persist_data" => "dados inválidos"]);
        }

        validateModelProperties(ModelsCashFlow::class, $data);

        if (!preg_match("/^[\d\\.,]+$/", $data["entry"])) {
            throw new \Exception("valor de entrada inválido");
        }
        
        $entry = convertCurrencyRealToFloat($data["entry"]);
        $entry = $data["entry_type"] == 0 ? ($entry * -1) : $entry;
        $data["entry"] = $entry;

        foreach ($data as $key => &$value) {
            if ($key == "id_user") {
                if (!$value instanceof User) {
                    throw new Exception("Instancia inválida");
                }

                $value = $value->getId();
            }

            $this->cashFlow->$key = $value;
        }

        if (!$this->cashFlow->save()) {
            throw new PDOException($this->cashFlow->fail()->getMessage());
        }

        $this->setId(Connect::getInstance()->lastInsertId());
        return true;
    }
}
