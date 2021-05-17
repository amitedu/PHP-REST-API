<?php
header('Access-Control-Allow-Origin: *');
header("Content-Type: application/json; charset-UTF-8");

include_once __DIR__ . '/../../Database.php';
include_once __DIR__ . '/../../controllers/Pages.php';

$database = new Database();
$db       = $database->getConnect();

$stmt  = new Pages($db);
$items = $stmt->read();

if ($items) {
    $itemCount = $items->rowCount();
    if ($itemCount > 0) {
        http_response_code(200);

        $arr             = array();
        $arr['response'] = array();
        $arr['count']    = $itemCount;

        while ($row = $items->fetch()) {
            $arr['response'][] = $row;
        }
        try {
            echo json_encode($arr, JSON_THROW_ON_ERROR);
        } catch (JsonException $e) {
            echo 'Can not create Json';
        }

    } else {
        http_response_code(404);

        try {
            echo json_encode(array(
                'Type' => 'danger',
                'title' => 'Failed',
                'message' => 'No records found'
            ), JSON_THROW_ON_ERROR);
        } catch (JsonException $e) {
            echo 'can not create Json';
        }
    }
} else {
    http_response_code(404);

    try {
        echo json_encode(array(
            'Type' => 'danger',
            'title' => 'Failed',
            'message' => 'Your token did not match.'
        ), JSON_THROW_ON_ERROR);
    } catch (JsonException $e) {
        echo 'Can not create Json';
    }
}
