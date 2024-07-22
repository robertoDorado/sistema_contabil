<?php
$system = [
    [
        "date" => "2024-07-22",
        "memo" => "ifood",
        "value" => "-233,5"
    ],
    [
        "date" => "2024-08-22",
        "memo" => "ifood",
        "value" => "-233,6"
    ]
];

$file = [
    [
        "date" => "2024-07-22",
        "memo" => "ifood",
        "value" => "-233,5"
    ],
    [
        "date" => "2024-08-22",
        "memo" => "ifood",
        "value" => "-233,63"
    ],
];

$diffDate = array_udiff($file, $system, function ($a, $b) {
    if ($a["date"] == $b["date"]) {
        return 0;
    }
    return $a["date"] < $b["date"] ? -1 : 1;
});

$diffMemo = array_udiff($file, $system, function ($a, $b) {
    if ($a["memo"] == $b["memo"]) {
        return 0;
    }
    return $a["memo"] < $b["memo"] ? -1 : 1;
});

$diffValue = array_udiff($file, $system, function ($a, $b) {
    if ($a["value"] == $b["value"]) {
        return 0;
    }
    return $a["value"] < $b["value"] ? -1 : 1;
});

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

function arrayWithMostItems(array ...$arrays) {
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

$diff = arrayWithMostItems($diffDate, $diffMemo, $diffValue);
print_r($diff);
