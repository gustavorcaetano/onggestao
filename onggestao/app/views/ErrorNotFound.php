<?php

http_response_code(404);
header('Content-Type: application/json');
header('Content-Type: text/html; charset=utf-8');
header('HTTP/1.0 404 Not Found');
$response = [
    'error' => '404 - Recurso requerido não encontrado!',
    'errorno' => 404,
];
echo json_encode($response, JSON_UNESCAPED_UNICODE);

?>