<?php

header('Access-Control-Allow-Origin: *');
header('Content-Type: application/json; charset-utf-8');
header("Access-Control-Allow-Methods: *");
//header("Access");

include_once __DIR__ . '/../../Database.php';
include_once __DIR__ . '/../../controllers/Pages.php';

$database = new Database();
$db = $database->getConnect();

$item = new Pages($db);

if ($item->update($_POST)) {
    http_response_code(200);
    echo json_encode(
        array(
            "type" => "success",
            "title" => "success",
            "message" => "The page updated successfully"
        )
    );
} else {
    http_response_code(404);
    echo json_encode(
        array(
            "type" => "failed",
            "title" => "failed",
            "message" => "The page did not update successfully"
        )
    );
}
