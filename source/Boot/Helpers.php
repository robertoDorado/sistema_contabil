<?php

function formaStripetTextFreeTrial() {
    $days = preg_match("/^\d+$/", $_GET['free_days'] ?? "") ? $_GET['free_days'] : 7;
    return !empty($_GET['free_days']) ? "({$days} dias gratuito)" : "";
}

function formatStripeIntervalPeriod(bool $isFormatted = false, string $period = DEFAULT_PERIOD) {
    $period = $_GET['period'] ?? $period;
    $checkIntervalPeriod = [
        "month" => function () use ($isFormatted) {
            return !$isFormatted ? ["interval" => "month"] : "mês";
        },
        "year" => function () use ($isFormatted) {
            return !$isFormatted ? ["interval" => "year"] : "ano";
        },
        "week" => function () use ($isFormatted) {
            return !$isFormatted ? ["interval" => "week"] : "semana";
        },
        "day" => function () use ($isFormatted) {
            return !$isFormatted ? ["interval" => "day"] : "dia";
        }
    ];

    if (empty($checkIntervalPeriod[$period])) {
        return $checkIntervalPeriod['month']();
    }

    return $checkIntervalPeriod[$period]();
}

function formatStripePriceInFloatValue(bool $isFormatted = false, string $value = DEFAULT_PRICE_VALUE) {
    $value = $_GET['value'] ?? $value;
    return $isFormatted ? preg_replace("/(\d+)(\d{2})$/", "$1.$2", $value) : $value;
}

function dumpAndDie($data) {
    var_dump($data);
    die;
}

function printData($data) {
    echo "<pre>";
    print_r($data);
    die;
}

function removeAccets(string $string)
{
    $map = array(
        'á' => 'a',
        'à' => 'a',
        'ã' => 'a',
        'â' => 'a',
        'ä' => 'a',
        'é' => 'e',
        'è' => 'e',
        'ê' => 'e',
        'ë' => 'e',
        'í' => 'i',
        'ì' => 'i',
        'î' => 'i',
        'ï' => 'i',
        'ó' => 'o',
        'ò' => 'o',
        'õ' => 'o',
        'ô' => 'o',
        'ö' => 'o',
        'ú' => 'u',
        'ù' => 'u',
        'û' => 'u',
        'ü' => 'u',
        'ç' => 'c',
        'Á' => 'A',
        'À' => 'A',
        'Ã' => 'A',
        'Â' => 'A',
        'Ä' => 'A',
        'É' => 'E',
        'È' => 'E',
        'Ê' => 'E',
        'Ë' => 'E',
        'Í' => 'I',
        'Ì' => 'I',
        'Î' => 'I',
        'Ï' => 'I',
        'Ó' => 'O',
        'Ò' => 'O',
        'Õ' => 'O',
        'Ô' => 'O',
        'Ö' => 'O',
        'Ú' => 'U',
        'Ù' => 'U',
        'Û' => 'U',
        'Ü' => 'U',
        'Ç' => 'C'
    );

    $pattern = '/[áàãâäéèêëíìîïóòõôöúùûüçÁÀÃÂÄÉÈÊËÍÌÎÏÓÒÕÔÖÚÙÛÜÇ]/u';
    return preg_replace_callback($pattern, function ($matches) use ($map) {
        return $map[$matches[0]];
    }, $string);
}

function arrayWithMostItems(array ...$arrays)
{
    $maxCount = 0;
    $resultArray = [];

    foreach ($arrays as $array) {
        $currentCount = countItems($array);
        if ($currentCount > $maxCount) {
            $maxCount = $currentCount;
            $resultArray = $array;
        }
    }

    return $resultArray;
};

function countItems(array $array)
{
    $count = 0;
    foreach ($array as $element) {
        if (is_array($element)) {
            $count += countItems($element);
        } else {
            $count++;
        }
    }
    return $count;
};

function financialIndicators(): array
{
    return [
        "recebimentos de clientes",
        "pagamentos a fornecedores e empregados",
        "despesas de capital",
        "emissão de dívidas ou ações",
        "pagamento de dívidas ou dividendos",
        "compra de ativos fixos",
        "venda de investimentos",
        "pagamentos de juros",
        "pagamentos de dívidas",
        "lucro líquido",
        "receita líquida",
        "período médio de cobrança",
        "período médio de estoque",
        "período médio de pagamento"
    ];
}
function initializeUserAndCompanyId(): array
{
    $verifyUserType = [
        "0" => new \Source\Domain\Model\User(),
        "1" => new \Source\Domain\Model\Support(),
    ];

    $user = $verifyUserType[session()->user->user_type];
    $user->setEmail(session()->user->user_email);
    $userData = $user->findUserByEmail();

    $user->setId($userData->id);
    $companyId = empty(session()->user->company_id) ? 0 : session()->user->company_id;

    return [
        "user" => $user,
        "company_id" => $companyId,
        "user_data" => $userData
    ];
}

function verifyRequestHttpOrigin(?string $serverOrigin)
{
    $allowedOrigin = [
        CONF_URL_BASE,
        CONF_URL_TEST
    ];

    $origin = !empty($serverOrigin) ? $serverOrigin : '';
    if (!in_array($origin, $allowedOrigin)) {
        header("Content-Type: application/json");
        http_response_code(403);
        echo json_encode([
            'error' => 'acesso negado',
            'code' => 403
        ]);
        die;
    }
}

function monthsInPortuguese(): array
{
    return [
        1 => 'Janeiro',
        2 => 'Fevereiro',
        3 => 'Março',
        4 => 'Abril',
        5 => 'Maio',
        6 => 'Junho',
        7 => 'Julho',
        8 => 'Agosto',
        9 => 'Setembro',
        10 => 'Outubro',
        11 => 'Novembro',
        12 => 'Dezembro'
    ];
}

function getCompanysNameByUserId(): array
{
    $user = new \Source\Domain\Model\User();
    $user->setEmail(session()->user->user_email);
    $userData = $user->findUserByEmail();

    $company = new \Source\Domain\Model\Company();
    $company->id_user = $userData->id;
    $dataCompany = $company->findAllCompanyByUserId(["company_name", "deleted", "id"]);
    return empty($dataCompany) ? [] : $dataCompany;
}

function userHasCompany(): bool
{
    if (empty(session()->user->user_type)) {
        $user = new \Source\Domain\Model\User();
        $user->setEmail(session()->user->user_email);
        $userData = $user->findUserByEmail();

        $company = new \Source\Domain\Model\Company();
        $company->id_user = $userData->id;
        $dataCompany = $company->findCompanyByUserId();

        return empty($dataCompany) ? false : true;
    } else {
        return false;
    }
}

function validateRequestData(array $requiredKeys, array &$requestData)
{
    if (empty($requestData)) {
        throw new \Exception("dados do cliente não pode estar vazio");
    }

    if (empty($requiredKeys)) {
        throw new \Exception("dados de verificação não pode estar vazio");
    }

    $requestDataKeys = array_keys($requestData);
    foreach ($requiredKeys as $value) {
        if (!in_array($value, $requestDataKeys)) {
            throw new \Exception("a chave {$value} é obrigatória");
        }
    }

    foreach ($requestData as $key => $value) {
        if (empty($value)) {
            throw new \Exception("chave da requisição {$key} não pode estar vazio");
        }
    }
}

function showUserFullName(): string
{
    $verifyUserType = [
        "0" => new Source\Domain\Model\User(),
        "1" => new Source\Domain\Model\Support()
    ];

    $user  = $verifyUserType[session()->user->user_type];
    $user->setEmail(session()->user->user_email);
    $userData = $user->findUserByEmail();

    if (empty($userData)) {
        echo $user->message->json();
        die;
    }

    $userFullNameData = explode(" ", $userData->user_full_name);
    $userFullName = [];
    $keys = [0, 1, 2];

    foreach ($userFullNameData as $key => $value) {
        if (in_array($key, $keys)) {
            array_push($userFullName, $value);
        }
    }

    $userFullName = implode(" ", $userFullName);
    $userFullName = ucwords($userFullName);
    return $userFullName;
}

function message()
{
    return new \Source\Support\Message();
}

function session()
{
    return new \Source\Core\Session();
}

function convertCurrencyRealToFloat(string $value)
{
    $value = trim($value);
    $value = preg_replace("/^(.+)(,\d{1,2}|\.\d{1,2})$/", "$1;$2", $value);
    $value = preg_replace("/[^\d;]+/", "", $value);
    $value = preg_replace("/;/", ".", $value);
    return $value;
}

function executeMigrations(string $instance)
{
    echo "------------ CLASSE: " . $instance . " -----------------\n";
    $object = new $instance();
    $methods = array_reverse(get_class_methods($object));

    foreach ($methods as $method) {
        if ($method != "__construct") {
            echo "EXECUTANDO: " . $method . "\n";
            $object->$method();
        }
    }
    echo "----------------------------------------------\n";
}

function transformCamelCaseToSnakeCase(array $args)
{
    foreach ($args as &$originalString) {
        $transformedString = preg_replace('/([a-z])([A-Z])/', '$1_$2', $originalString);
        $originalString = strtolower($transformedString);
    }
    return $args;
}

/**
 * @param string $path
 * @return string
 */
function url(string $path = null): string
{
    if (str_replace("www.", "", $_SERVER['HTTP_HOST']) == "localhost") {
        if ($path) {
            return CONF_URL_TEST .
                "/" .
                ($path[0] == "/" ? mb_substr($path, 1) : $path);
        }
        return CONF_URL_TEST;
    }

    if ($path) {
        return CONF_URL_BASE .
            "/" .
            ($path[0] == "/" ? mb_substr($path, 1) : $path);
    }

    return CONF_URL_BASE;
}

/**
 * @param string|null $path
 * @param string $theme
 * @return string
 */
function theme(string $path = null, string $theme = CONF_VIEW_THEME): string
{
    if (str_replace("www.", "", $_SERVER['HTTP_HOST']) == "localhost") {
        if ($path) {
            return CONF_URL_TEST . "/themes/{$theme}/" . ($path[0] == "/" ? mb_substr($path, 1) : $path);
        }
        return CONF_URL_TEST . "/themes/{$theme}";
    }

    if ($path) {
        return CONF_URL_BASE . "/themes/{$theme}/" . ($path[0] == "/" ? mb_substr($path, 1) : $path);
    }
    return CONF_URL_BASE . "/themes/{$theme}";
}

/**
 * @param string $url
 * @return void
 */
function redirect(string $url): void
{
    header("HTTP/1.1 302 Redirect");
    if (filter_var($url, FILTER_VALIDATE_URL)) {
        header("Location: {$url}");
        exit();
    }

    if (filter_input(INPUT_GET, "route", FILTER_DEFAULT) != $url) {
        $location = url($url);
        header("Location: {$location}");
        exit();
    }
}

/**
 * filter_type: Principais campos de consulta para os relatórios. O que não estiver
 * nessa lista será tratado como FILTER_SANITIZE_STRIPPED pelos helpers de filtro
 * @return null|array
 */
function filter_type(): array
{
    $filterFields = [
        "route" => FILTER_SANITIZE_STRIPPED,
        "product" => FILTER_SANITIZE_NUMBER_INT,
        "product_id" => FILTER_SANITIZE_NUMBER_INT,
        "country" => FILTER_SANITIZE_STRIPPED,
        "device" => FILTER_SANITIZE_NUMBER_INT,
        "redirect" => FILTER_SANITIZE_ENCODED,
        "status" => FILTER_SANITIZE_NUMBER_INT,
        "upsell" => FILTER_SANITIZE_NUMBER_INT,
        "paymentMethod" => FILTER_SANITIZE_NUMBER_INT,
        "company" => FILTER_SANITIZE_NUMBER_INT,
        "affiliate" => FILTER_SANITIZE_NUMBER_INT,
        "level" => FILTER_SANITIZE_NUMBER_INT,
        "group_id" => FILTER_SANITIZE_NUMBER_INT,
        "report_id" => FILTER_SANITIZE_NUMBER_INT,
    ];
    return $filterFields;
}

/**
 * filter_array: Filtrar campos de array ou globais GET e POST
 * @param array $array
 * @return array
 */
function filter_array(array $array): array
{
    $filterFields = filter_type();

    foreach ($array as $key => $value) {
        if (in_array($key, array_keys($filterFields))) {
            $filterArr[$key] = $filterFields[$key];
        } else {
            $filterArr[$key] = FILTER_SANITIZE_STRIPPED;
        }
    }
    return filter_var_array($array, $filterArr);
}

/**
 * @param string $string
 * @param string $type = int, string, chars, etc
 * @return string
 */
function filter_variable(string $string, $type = null): string
{
    if (!empty($type)) {
        $type = mb_convert_case($type, MB_CASE_LOWER);

        if ($type == 'default') {
            return filter_var($string, FILTER_DEFAULT);
        } elseif ($type == 'int') {
            return filter_var($string, FILTER_SANITIZE_NUMBER_INT);
        } elseif ($type == 'string') {
            return filter_var($string, FILTER_SANITIZE_STRING);
        } elseif ($type == 'chars') {
            return filter_var($string, FILTER_SANITIZE_SPECIAL_CHARS);
        } elseif ($type == 'mail' || $type == 'email') {
            return filter_var($string, FILTER_VALIDATE_EMAIL);
        }
    }
    return filter_var($string, FILTER_SANITIZE_STRIPPED);
}
