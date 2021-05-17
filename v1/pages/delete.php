<?php

header('Access-Control-Allow-Origin: *');
header('Content-Type: application/json; charset-utf-8');
header("Access-Control-Allow-Methods: *");

include_once __DIR__ . '/../../Database.php';
include_once __DIR__ . '/../../controllers/Pages.php';

$database = new Database();
$db = $database->getConnect();

$item = new Pages($db);
try {
    $data = json_decode(file_get_contents("php://input"), true, 512, JSON_THROW_ON_ERROR);
} catch (JsonException $e) {
}

$status = $item->delete($data);
if ($status === 'tokenUnsuccessful') {
    try {
        echo json_encode(array(
            "type" => "Failed",
            "title" => "tokenStatus",
            "message" => "Token match unsuccessful"
        ), JSON_THROW_ON_ERROR);
    } catch (JsonException $e) {
        echo 'Can not create Json format';
    }
} else if ($status) {
    http_response_code(200);
    try {
        echo json_encode(array(
            "type" => "success",
            "title" => "success",
            "message" => "The page deleted successfully"
        ), JSON_THROW_ON_ERROR);
    } catch (JsonException $e) {
    }
} else {
    http_response_code(404);
    try {
        echo json_encode(array(
            "type" => "failed",
            "title" => "failed",
            "message" => "The page did not deleted successfully"
        ), JSON_THROW_ON_ERROR);
    } catch (JsonException $e) {
    }
}
