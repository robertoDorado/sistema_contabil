<?php

function setCurrentMenuActive(array $endpoints = [])
{
    if (!empty($endpoints)) {
        return in_array($_SERVER['REQUEST_URI'], $endpoints) ? true : false;
    }
    return false;
}

function showUserFullName(): string
{
    $user = new Source\Domain\Model\User();
    $userData = $user->findUserByEmail(session()->user->user_email);

    if (is_string($userData) && json_decode($userData) != null) {
        throw new \Exception($userData);
    }

    $userFullNameData = explode(" ", $userData->user_full_name);
    $userFullName = [];
    $keys = [0, 1];

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
    if (empty($value)) {
        throw new \Exception("Valor a ser convertido não pode estar vazio.");
    }

    $value = preg_replace("/[^\d\.,]+/", "", $value);
    $value = str_replace(".", "", $value);
    $value = str_replace(",", ".", $value);
    $value = floatval($value);

    if (gettype($value) !== "double") {
        throw new \Exception("Erro na conversão do valor para float");
    }

    return $value;
}

function validateModelProperties(string $class, array $data)
{
    $reflectionClass = new ReflectionClass($class);

    $properties = $reflectionClass->getProperties();
    $properties = array_filter($properties, function ($property) use ($reflectionClass) {
        if ($property->getDeclaringClass()->getName() == $reflectionClass->getName()) {
            return $property->getName();
        }
    });

    $properties = transformCamelCaseToSnakeCase($properties);

    foreach ($properties as &$value) {
        $value = preg_replace('/^.*\\$([A-Za-z0-9_]+).*/', '$1', trim($value));
    }

    $properties = array_filter($properties, function ($property) {
        if (!empty($property)) {
            return $property;
        }
    });

    if (!empty($properties)) {
        for ($i = 0; $i < count($properties); $i++) {
            if (!isset($data[$properties[$i]])) {
                throw new \Exception("esta propriedade " . $properties[$i] . " foi passado de maneira incorreta");
            }
        }
    }
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
