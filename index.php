<?php

use App\Database\PDODatabaseConnection;
use App\Database\PDOQueryBuilder;
use App\Helpers\Config;

require_once './vendor/autoload.php';

$config = Config::get('database', 'pdo_testing');

$pdoConnection = new PDODatabaseConnection($config);
$queryBuilder = new PDOQueryBuilder($pdoConnection->connect());

function json_response($data = null, $statusCode = 200)
{
    header_remove();
    header('Content-Type: application/json');
    http_response_code($statusCode);
    echo json_encode($data);
    exit();
}

function request()
{
    return json_decode(file_get_contents('php://input'), true);
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $queryBuilder->table('bugs')->create(request());

    json_response(null, 200);
}