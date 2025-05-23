<?php

$from = $argv[1];
$to = $argv[2];

if (!file_exists($from)) {
    $from .= $from . '.gz';
}

$fromHandle = fopen($from, 'r');
$toHandle = fopen($to, 'w');


$count = 0;

while ($data = fgetcsv($fromHandle)) {
    $count++;

    $ip = ip2long($data[2]);

    if ($ip) {
        $normalized = [
            'ip' => $ip,
            'domain' => $data[0],
            'discovery_date' => $data[11],
            'majestic' => $data[10]
        ];

        fputcsv($toHandle, $normalized);
    }

    if ($count % 10000000 == 0) {
        echo "Count: " . $count . "\n";
    }
}
