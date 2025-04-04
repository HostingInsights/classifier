<?php

include_once __DIR__ . '/../vendor/autoload.php';

if (array_key_exists(1, $argv)) {
    $filename = $argv[1];
} else {
    $filename = __DIR__ . '/zonefile.txt';
}

if (array_key_exists(2, $argv)) {
    $startWith = $argv[2];
} else {
    $startWith = 0;
}

$ipStart = 1000;

$lastRange = ['from' => 0, 'to' => 0];
$lastAs = 0;

$start = time();

function getAsn($longIp): int
{
    global $asCollection;
    global $lastRange;
    global $lastAs;

    $longIp = (int)$longIp;

    if ($longIp > $lastRange['from'] && $longIp < $lastRange['to']) {
        // echo "last_hit\n";
        return $lastAs;
    }

    $query = [
        'ranges' => [
            '$elemMatch' => [
                'from' => ['$lte' => $longIp],
                'to' => ['$gte' => $longIp],
            ]
        ]
    ];

    $as = $asCollection->findOne($query);

    if ($as) {
        $ipRanges = $as['ranges'];
        foreach ($ipRanges as $ipRange) {
            if ($longIp < $ipRange['to'] && $longIp > $ipRange['from']) {
                $lastRange = $ipRange;
                $lastAs = $as['as'];
                break;
            }
        }

        // echo "db_hit\n";

        return $as['as'];
    } else {
        // echo "miss | $longIp \n";
        // var_dump('NOT FOUND ' . $ip);
        return false;
    }
}

$asn = [];

$handle = fopen($filename, 'r');

$count = 0;
$blockSize = 2000;
$found = 0;

$documents = [];

$mongoDBUrl = "mongodb://localhost:27017";

$client = new \MongoDB\Client($mongoDBUrl);

$database = $client->selectDatabase('classifier');

$collection = $database->selectCollection('internet');
$asCollection = $database->selectCollection('as');

$domains = [];
$operations = [];

$stats = [
    'domains' => 0,
    'withoutAsn' => 0,
    'new' => 0,
    'updated' => 0
];

function processData($domains, $documents): void
{
    global $collection;
    global $stats;

    $operations = [];

    $knownDomains = $collection->find(['domain' => ['$in' => $domains]]);

    $lastIp = 0;
    $lastAs = "";

    $fetchAsn = true;

    foreach ($knownDomains as $knownDomain) {
        if ($knownDomain['ip'] != $documents[$knownDomain['domain']]['ip']) {

            $stats['updated']++;

            $newIp = (int)$documents[$knownDomain['domain']]['ip'];

            if ($newIp === $lastIp) {
                $as = $lastAs;
            } else {
                $as = getAsn($newIp);
                $lastIp = $newIp;
                $lastAs = $as;
            }

            $historyIp = [
                'date' => new \MongoDB\BSON\UTCDateTime(),
                'value' => $newIp
            ];

            if ($as && property_exists($knownDomain, 'as') && $as != $knownDomain['as']) {
                $historyAs = [
                    'date' => new \MongoDB\BSON\UTCDateTime(),
                    'value' => $as
                ];
                $operations[] = ['updateOne' => [['_id' => $knownDomain['_id']], ['$set' => ['ip' => $newIp, 'as' => $as, 'lastAs' => $knownDomain['as']], '$push' => ['history.ip' => $historyIp, 'history.as' => $historyAs]]]];
            } else {
                $stats['withoutAsn']++;
                $operations[] = ['updateOne' => [['_id' => $knownDomain['_id']], ['$set' => ['ip' => $newIp, 'as' => false], '$push' => ['history.ip' => $historyIp]]]];
            }
        }

        unset($documents[$knownDomain['domain']]);
    }

    $lastIp = 0;
    $lastAs = "";

    foreach ($documents as $document) {

        if ($document['ip'] === $lastIp) {
            $as = $lastAs;
        } else {
            if ($fetchAsn) {
                $as = getAsn($document['ip']);
            } else {
                $as = false;
            }
            $lastIp = $document['ip'];
            $lastAs = $as;
        }

        if ($as === false) {
            $fetchAsn = false;
        }

        if ($as) {
            $document['as'] = $as;
            $document['history']['as'][] = [
                'date' => new \MongoDB\BSON\UTCDateTime(),
                'value' => $as
            ];
        } else {
            unset($document['history']['as']);
            unset($document['as']);
        }

        $stats['new']++;

        $operations[] = ['insertOne' => [$document]];
    }

    if (count($operations) > 0) {
        $collection->bulkWrite($operations);
    }
}

while ($data = fgetcsv($handle)) {
    $stats['domains']++;

    $count++;
    if ($count >= $startWith) {

        $ip = $data[0];

        if ($ip < $ipStart) continue;

        $domain = $data[1];

        if ($ip) {
            $domains[] = $domain;
            $found++;

            $documents[$domain] = [
                'domain' => $domain,
                'ip' => (int)$ip,
                'as' => false,
                'discovery_date' => new \MongoDB\BSON\UTCDateTime(new \DateTime($data[2])),
                'history' => [
                    'ip' => [
                        [
                            'date' => new \MongoDB\BSON\UTCDateTime(),
                            'value' => (int)$ip
                        ]
                    ],
                    'as' => [

                    ]
                ]
            ];

            if ($data[3]) {
                $documents[$domain]['majestic'] = (int)$data[3];
            }

            if ($found % $blockSize == 0) {
                // echo "\nPersisting dataset #" . $count;
                processData($domains, $documents);

                $documents = [];
                $domains = [];
            }
        }
    }
}

// echo "\nPersisting dataset #" . $count;
processData($domains, $documents);

echo "Finished " . date('Y-m-d') . " -> Statistics: duration: " . (int)((time() - $start) / 60) . " minutes, domains: " . $stats['domains'] . ', new: ' . $stats['new'] . ', updated: ' . $stats['updated'] . ', without asn: ' . $stats['withoutAsn'] . '.';
