<?php
define('API_URL', 'http://api.est.dev');
define('API_KEY', 'key1234567890');
define('BASE_PATH', __DIR__);
define('STATUS_DONE', 3);
define('STATUS_ERROR', 4);

require BASE_PATH . '/../vendor/autoload.php';

$parameters = [
    'result_file_type' => 'xls',
    'benchmark_model' => 'mm',
    'return_type' => 'log',
    'non_trading_days' => 'later',
    'test_statistics' => [
        'art' => '1',
        'cart' => '1',
        'aart' => '1',
        'caart' => '1',
        'abhart' => '1',
        'aarptlz' => '1',
        'caarptlz' => '1',
        'aaraptlz' => '1',
        'caaraptlz' => '1',
        'aarbmpz' => '1',
        'caarbmpz' => '1',
        'aarabmpz' => '1',
        'caarabmpz' => '1',
        'aarskewadjt' => '1',
        'caarskewadjt' => '1',
        'abharskewadjt' => '1',
        'aarrankz' => '1',
        'caarrankz' => '1',
        'aargrankt' => '1',
        'caargrankt' => '1',
        'aargrankz' => '1',
        'caargrankz' => '1',
        'aargsignz' => '1',
        'caargsignz' => '1',
        'aarcdat' => '1',
        'aarjackknivet' => '1',
    ],
    'datasources' => [
        'request_file' => 'xlsx',
        'firm_data' => 'csv_zip',
        'market_data' => 'xls_zip'
    ]
];

$api = new \EventStudyTools\ApiWrapper\ApiWrapper(API_URL);

if ($api->authentication(API_KEY)) {
    $api->configureTask(new \EventStudyTools\ApiWrapper\ApplicationInput\ArcApplicationInput($parameters));
    $api->uploadFile('firm_data', BASE_PATH . '/data/firm_data.csv.zip');
    $api->uploadFile('market_data', BASE_PATH . '/data/market_data.xls.zip');
    $api->uploadFile('request_file', BASE_PATH . '/data/request_file.xlsx');
    $api->commitData();

    do {
        sleep(15);
        $status = $api->getTaskStatus();
    } while (!in_array($status->status, array(STATUS_DONE, STATUS_ERROR)));

    switch ($status->status) {
        case STATUS_DONE:
            $results = $api->getTaskResults();

            echo "Task \"" . $api->getToken() . "\" was terminated successfully\n";
            echo "Links:\n";
            if (!empty($results->log)) echo "log: " . $results->log . "\n";
            foreach ($results->results as $result) echo $result . "\n";

            break;

        case STATUS_ERROR:
            echo "Task \"" . $api->getToken() . "\" was terminated with error: " . $status->message . "\n";
            break;

        default:
            echo "Invalid status \"" . $status->status . "\"\n";
            break;
    }
}