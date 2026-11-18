<?php
http_response_code(401);
header('Content-Type: application/json');
header('Content-Type: text/html; charset=utf-8');
header("HTTP/1.0 401 Unauthorized and Level");
$response = [
    'error' => '401 - Unauthorized and Level',
    'errorno' => 401,
];
echo json_encode($response, JSON_UNESCAPED_UNICODE);
?>