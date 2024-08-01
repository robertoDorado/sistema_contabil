<?php

namespace Source\Domain\Model;

use DateTime;
use Exception;
use Ramsey\Uuid\Nonstandard\Uuid;
use Source\Domain\Support\Tools;
use Source\Models\BalanceSheet as ModelsBalanceSheet;

/**
 * BalanceSheet Domain\Model
 * @link 
 * @author Roberto Dorado <robertodorado7@gmail.com>
 * @package Source\Domain\Model
 */
class BalanceSheet
{
    /** @var int Id da tabela balanço patrimonial */
    private int $id;

    /** @var string Uuid do registro */
    private string $uuid;

    /** @var object|null */
    private object $data;

    /** @var ModelsBalanceSheet */
    private ModelsBalanceSheet $balanceSheet;

    /** @var array|null */
    private $balanceSheetReport;

    /**
     * BalanceSheet constructor
     */
    public function __construct()
    {
        $this->balanceSheet = new ModelsBalanceSheet();
        $this->data = new \stdClass();
    }

    public function __set($name, $value)
    {
        $this->data->$name = $value;
    }

    public function __get($name)
    {
        return $this->data->$name ?? null;
    }

    /** @var ModelsBalanceSheet[] */
    public function findAllBalanceSheet(array $columnsA, array $columnsB, array $columnsC, array $params, bool $onlyData = false): array
    {
        $columnsA = empty($columnsA) ? "*" : implode(", ", $columnsA);
        $columnsB = empty($columnsB) ? "*" : implode(", ", $columnsB);
        $columnsC = empty($columnsC) ? "*" : implode(", ", $columnsC);

        $terms = "id_user=:id_user AND id_company=:id_company AND deleted=:deleted";
        $paramsQuery = ":id_company=" . $params["id_company"] . "&:id_user=" . $params['id_user'] . "&:deleted=" . $params['deleted'] . "";

        $response = $this->balanceSheet->find(
            $terms,
            $paramsQuery,
            $columnsA
        )->join(
            CONF_DB_NAME . ".chart_of_account",
            "id",
            $terms,
            $paramsQuery,
            $columnsB,
            "id_chart_of_account",
            CONF_DB_NAME . ".balance_sheet"
        )->join(
            CONF_DB_NAME . ".chart_of_account_group",
            "id",
            $terms,
            $paramsQuery,
            $columnsC,
            "id_chart_of_account_group",
            CONF_DB_NAME . ".chart_of_account"
        )->between(
            "created_at",
            CONF_DB_NAME . ".balance_sheet",
            $params["date"]
        )->fetch(true);

        if (empty($response)) {
            return [];
        }

        if ($onlyData) {
            $response = array_map(function ($item) {
                return (array) $item->data();
            }, $response);
        }

        return $response;
    }

    /** @var ModelsBalanceSheet[] */
    public function findAllShareholdersEquity(array $columnsA, array $columnsB, array $columnsC, array $params): array
    {
        $columnsA = empty($columnsA) ? "*" : implode(", ", $columnsA);
        $columnsB = empty($columnsB) ? "*" : implode(", ", $columnsB);
        $columnsC = empty($columnsC) ? "*" : implode(", ", $columnsC);

        $terms = "id_user=:id_user AND id_company=:id_company AND deleted=:deleted";
        $queryParams = ":id_user={$params['id_user']}&:id_company={$params['id_company']}&:deleted={$params['deleted']}";

        $this->balanceSheetReport = $this->balanceSheet->find($terms, $queryParams, $columnsA)
            ->join(
                CONF_DB_NAME . ".chart_of_account",
                "id",
                $terms,
                $queryParams,
                $columnsB,
                "id_chart_of_account",
                CONF_DB_NAME . ".balance_sheet"
            )
            ->join(
                CONF_DB_NAME . ".chart_of_account_group",
                "id",
                $terms,
                $queryParams,
                $columnsC,
                "id_chart_of_account_group",
                CONF_DB_NAME . ".chart_of_account"
            )->between(
                "created_at",
                CONF_DB_NAME . ".balance_sheet",
                $params["date"]
            )->fetch(true);

        if (empty($this->balanceSheetReport)) {
            return ["data" => [], "total" => 0];
        }

        return $this->dataProcessing("patrimonio liquido");
    }

    /** @var ModelsBalanceSheet[] */
    public function findAllNonCurrentLiabilities(array $columnsA, array $columnsB, array $columnsC, array $params): array
    {
        $columnsA = empty($columnsA) ? "*" : implode(", ", $columnsA);
        $columnsB = empty($columnsB) ? "*" : implode(", ", $columnsB);
        $columnsC = empty($columnsC) ? "*" : implode(", ", $columnsC);

        $terms = "id_user=:id_user AND id_company=:id_company AND deleted=:deleted";
        $queryParams = ":id_user={$params['id_user']}&:id_company={$params['id_company']}&:deleted={$params['deleted']}";

        $this->balanceSheetReport = $this->balanceSheet->find($terms, $queryParams, $columnsA)
            ->join(
                CONF_DB_NAME . ".chart_of_account",
                "id",
                $terms,
                $queryParams,
                $columnsB,
                "id_chart_of_account",
                CONF_DB_NAME . ".balance_sheet"
            )
            ->join(
                CONF_DB_NAME . ".chart_of_account_group",
                "id",
                $terms,
                $queryParams,
                $columnsC,
                "id_chart_of_account_group",
                CONF_DB_NAME . ".chart_of_account"
            )->between(
                "created_at",
                CONF_DB_NAME . ".balance_sheet",
                $params["date"]
            )->fetch(true);

        if (empty($this->balanceSheetReport)) {
            return ["data" => [], "total" => 0];
        }

        return $this->dataProcessing("passivo nao circulante");
    }

    /** @var ModelsBalanceSheet[] */
    public function findAllCurrentLiabilities(array $columnsA, array $columnsB, array $columnsC, array $params): array
    {
        $columnsA = empty($columnsA) ? "*" : implode(", ", $columnsA);
        $columnsB = empty($columnsB) ? "*" : implode(", ", $columnsB);
        $columnsC = empty($columnsC) ? "*" : implode(", ", $columnsC);

        $terms = "id_user=:id_user AND id_company=:id_company AND deleted=:deleted";
        $queryParams = ":id_user={$params['id_user']}&:id_company={$params['id_company']}&:deleted={$params['deleted']}";

        $this->balanceSheetReport = $this->balanceSheet->find($terms, $queryParams, $columnsA)
            ->join(
                CONF_DB_NAME . ".chart_of_account",
                "id",
                $terms,
                $queryParams,
                $columnsB,
                "id_chart_of_account",
                CONF_DB_NAME . ".balance_sheet"
            )
            ->join(
                CONF_DB_NAME . ".chart_of_account_group",
                "id",
                $terms,
                $queryParams,
                $columnsC,
                "id_chart_of_account_group",
                CONF_DB_NAME . ".chart_of_account"
            )->between(
                "created_at",
                CONF_DB_NAME . ".balance_sheet",
                $params["date"]
            )->fetch(true);

        if (empty($this->balanceSheetReport)) {
            return ["data" => [], "total" => 0];
        }

        return $this->dataProcessing("passivo circulante");
    }

    /** @var ModelsBalanceSheet[] */
    public function findAllNonCurrentAssets(array $columnsA, array $columnsB, array $columnsC, array $params): array
    {
        $columnsA = empty($columnsA) ? "*" : implode(", ", $columnsA);
        $columnsB = empty($columnsB) ? "*" : implode(", ", $columnsB);
        $columnsC = empty($columnsC) ? "*" : implode(", ", $columnsC);

        $terms = "id_user=:id_user AND id_company=:id_company AND deleted=:deleted";
        $queryParams = ":id_user={$params['id_user']}&:id_company={$params['id_company']}&:deleted={$params['deleted']}";

        $this->balanceSheetReport = $this->balanceSheet->find($terms, $queryParams, $columnsA)
            ->join(
                CONF_DB_NAME . ".chart_of_account",
                "id",
                $terms,
                $queryParams,
                $columnsB,
                "id_chart_of_account",
                CONF_DB_NAME . ".balance_sheet"
            )
            ->join(
                CONF_DB_NAME . ".chart_of_account_group",
                "id",
                $terms,
                $queryParams,
                $columnsC,
                "id_chart_of_account_group",
                CONF_DB_NAME . ".chart_of_account"
            )->between(
                "created_at",
                CONF_DB_NAME . ".balance_sheet",
                $params["date"]
            )->fetch(true);

        if (empty($this->balanceSheetReport)) {
            return ["data" => [], "total" => 0];
        }

        return $this->dataProcessing("ativo nao circulante");
    }

    private function dataProcessing(string $referenceName): array
    {
        $data = array_map(function ($item) {
            $item->uuid = $item->getUuid();
            $item->created_at = (new DateTime($item->created_at))->format("d/m/Y");
            $item->account_name = $item->account_number . " " . $item->account_name;
            $item->account_name_group = strtolower(removeAccets($item->account_name_group));
            return (array)$item->data();
        }, $this->balanceSheetReport);

        $data = array_filter($data, function ($item) use ($referenceName) {
            if ($item["account_name_group"] == $referenceName) {
                return $item;
            }
        });

        $data = array_map(function ($item) use ($referenceName) {
            if (preg_match("/(ativo)/", $referenceName)) {
                $item["account_value"] = empty($item["account_type"]) ? $item["account_value"] : $item["account_value"] * -1;
            }else {
                $item["account_value"] = empty($item["account_type"]) ? $item["account_value"] * -1 : $item["account_value"];
            }
            return $item;
        }, $data);

        $grouppedData = [];
        foreach ($data as $value) {
            if (empty($grouppedData[$value["account_name"]])) {
                $grouppedData[$value["account_name"]] = $value;
                $grouppedData[$value["account_name"]]["account_value"] = 0;
            }

            $grouppedData[$value["account_name"]]["account_value"] += $value["account_value"];
        }

        $total = array_reduce($grouppedData, function ($acc, $item) {
            $acc += $item["account_value"];
            return $acc;
        }, 0);

        $grouppedData = array_map(function ($item) {
            $item["account_value_format"] = "R$ " . number_format($item["account_value"], 2, ",", ".");
            return $item;
        }, $grouppedData);

        return ["data" => $grouppedData, "total" => $total];
    }

    /** @var ModelsBalanceSheet[] */
    public function findAllCurrentAssets(array $columnsA, array $columnsB, array $columnsC, array $params): array
    {
        $columnsA = empty($columnsA) ? "*" : implode(", ", $columnsA);
        $columnsB = empty($columnsB) ? "*" : implode(", ", $columnsB);
        $columnsC = empty($columnsC) ? "*" : implode(", ", $columnsC);

        $terms = "id_user=:id_user AND id_company=:id_company AND deleted=:deleted";
        $queryParams = ":id_user={$params['id_user']}&:id_company={$params['id_company']}&:deleted={$params['deleted']}";

        $this->balanceSheetReport = $this->balanceSheet->find($terms, $queryParams, $columnsA)
            ->join(
                CONF_DB_NAME . ".chart_of_account",
                "id",
                $terms,
                $queryParams,
                $columnsB,
                "id_chart_of_account",
                CONF_DB_NAME . ".balance_sheet"
            )
            ->join(
                CONF_DB_NAME . ".chart_of_account_group",
                "id",
                $terms,
                $queryParams,
                $columnsC,
                "id_chart_of_account_group",
                CONF_DB_NAME . ".chart_of_account"
            )->between(
                "created_at",
                CONF_DB_NAME . ".balance_sheet",
                $params["date"]
            )->fetch(true);

        if (empty($this->balanceSheetReport)) {
            return ["data" => [], "total" => 0];
        }

        return $this->dataProcessing("ativo circulante");
    }

    public function updateBalanceSheetDataByUuid(array $data): bool
    {
        $tools = new Tools($this->balanceSheet, ModelsBalanceSheet::class);
        $response = $tools->updateData(
            "uuid=:uuid",
            ":uuid={$data['uuid']}",
            $data,
            "registro do balanço patrimonial não encontrado"
        );
        $this->data->message = !empty($tools->message) ? $tools->message : "";
        return !empty($response) ? true : false;
    }

    public function getUuid(): string
    {
        return $this->uuid;
    }

    public function setUuid(string $uuid): void
    {
        if (!Uuid::isValid($uuid)) {
            throw new Exception("uuid inválido");
        }
        $this->uuid = $uuid;
    }

    public function getId(): int
    {
        if (empty($this->id)) {
            throw new Exception("id não atribuido");
        }
        return $this->id;
    }

    public function setId(int $id): void
    {
        $this->id = $id;
    }

    public function persistData(array $data): bool
    {
        $tools = new Tools($this->balanceSheet, ModelsBalanceSheet::class);
        $response = $tools->persistData($data);
        $this->data->message = !empty($tools->message) ? $tools->message : "";

        !empty($response) ? $this->setId($tools->lastId) : null;
        return !empty($response) ? true : false;
    }
}
