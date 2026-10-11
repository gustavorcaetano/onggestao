<?php
// Descobre automaticamente a URL base do projeto (funciona no MAMP e no cPanel)
$protocolo = isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] === 'on' ? "https" : "http";
$host = $_SERVER['HTTP_HOST'];
// Se estiver em subpasta, ajusta de forma dinâmica
$scriptName = dirname($_SERVER['SCRIPT_NAME']);
define('BASE_URL', $protocolo . '://' . $host . ($scriptName == '/' ? '' : $scriptName) . '/');
?>