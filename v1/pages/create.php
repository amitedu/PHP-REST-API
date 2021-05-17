<?php

header('Content-Type: application/json');
header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Methods: POST');

include_once __DIR__ . '/../../Database.php';
include_once __DIR__ . '/../../controllers/Pages.php';

$database = new Database();
$db       = $database->getConnect();
$data     = [];
$item     = new Pages($db);
try {
    $data = json_decode(file_get_contents("php://input"), true, 512, JSON_THROW_ON_ERROR);
} catch (JsonException $e) {
    echo 'Json creation unsuccessful';
}

$status = $item->create($data);

if ($status === 'tokenUnsuccessful') {
    echo "Token match unsuccessful";
} else if ($status) {
    http_response_code(200);
    try {
        echo json_encode(array(
            "type" => "success",
            "title" => "success",
            "message" => "The page created successfully"
        ), JSON_THROW_ON_ERROR);
    } catch (JsonException $e) {
    }
} else {
    http_response_code(404);
    try {
        echo json_encode(array(
            "type" => "failed",
            "title" => "failed",
            "message" => "The page did not created successfully"
        ), JSON_THROW_ON_ERROR);
    } catch (JsonException $e) {
    }
}
