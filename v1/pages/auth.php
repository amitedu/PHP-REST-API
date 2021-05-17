<?php


header('Access-Control-Allow-Origin: *');
header("Content-Type: application/json; charset-UTF-8");

include_once __DIR__ . '/../../Database.php';
include_once __DIR__ . '/../../controllers/Pages.php';

$database = new Database();
$db       = $database->getConnect();
$token      = new Pages($db);

if ($token->auth()) {
    http_response_code(200);
    echo json_encode($token->auth());
} else {
    http_response_code(404);

    echo json_encode(array(
        'Type' => 'danger',
        'title' => 'Failed',
        'message' => 'Could not create token'
    ));
}
