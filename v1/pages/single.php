<?php

header('Access-Control-Allow-Origin: *');
header("Content-Type: application/json; charset-UTF-8");

include_once __DIR__ . '/../../Database.php';
include_once __DIR__ . '/../../controllers/Pages.php';

$database = new Database();
$db = $database->getConnect();

$stmt = new Pages($db);
$item = $stmt->single($_POST['slug']);
$itemCount = $item->rowCount();

if ($itemCount > 0) {
    http_response_code(200);

    $arr = array();
    $arr['response'] = array();
    $arr['count'] = $itemCount;

    while ($row = $item->fetch()) {
        $arr['response'][] = $row;
    }

    echo json_encode($arr);
} else {
    http_response_code(404);

    echo json_encode(array(
        'Type' => 'danger',
        'title' => 'Failed',
        'message' => 'No records found'
    ));
}
