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
    private ModelsCashFlow $chashFlow;

    private int $id;

    /**
     * CashFlow constructor
     */
    public function __construct()
    {
        $this->chashFlow = new ModelsCashFlow();
    }

    public function getId()
    {
        return $this->id;
    }

    public function setId(int $id)
    {
        $this->id = $id;
    }

    public function findCashFlowById($id)
    {
        return $this->chashFlow->findById($id);
    }

    public function dropCashFlowById(int $id)
    {
        $chashFlowData = $this->chashFlow->findById($id);
        if (!$chashFlowData->destroy()) {
            throw new PDOException($chashFlowData->fail());
        }
    }

    public function calculateBalance(): float
    {
        $data = $this->chashFlow->find("")->fetch(true);
        $balance = 0;

        if (!empty($data)) {
            foreach ($data as $value) {
                $balance += $value->entry;
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

            $this->chashFlow->$key = $value;
        }

        if (!$this->chashFlow->save()) {
            throw new PDOException($this->chashFlow->fail());
        }

        $this->setId(Connect::getInstance()->lastInsertId());
        return true;
    }
}
