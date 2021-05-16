<?php

require("../vendor/autoload.php");
$openapi = \OpenApi\scan($_SERVER['DOCUMENT_ROOT'] . '/controllers');
header('Content-Type: application/json');
echo $openapi->toJSON();
