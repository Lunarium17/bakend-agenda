<?php
// index.php — punto de entrada global

header("Content-Type: application/json; charset=utf-8");
// Habilita CORS si será consumido desde otro dominio
header("Access-Control-Allow-Origin: *");
header("Access-Control-Allow-Methods: GET, POST, PUT, DELETE, OPTIONS");
header("Access-Control-Allow-Headers: Content-Type, Authorization");

// Si es una petición OPTIONS (preflight), respondemos y salimos
if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') {
    http_response_code(200);
    exit;
}

$requestUri = $_SERVER['REQUEST_URI'];
$requestMethod = $_SERVER['REQUEST_METHOD'];

// Quitar query string si existe
$uri = parse_url($requestUri, PHP_URL_PATH);

// Aquí defines tus rutas
if ($uri === '/api/auth/login' && $requestMethod === 'POST') {
    require __DIR__ . '/api/auth/login.php';
    exit;
}

if ($uri === '/api/contactos' && $requestMethod === 'GET') {
    require __DIR__ . '/api/contactos/get_all.php';
    exit;
}

// ... más rutas según tu estructura ...

/*/ Si no coincide ninguna ruta:
http_response_code(404);
echo json_encode([
    "ok" => false,
    "message" => "Ruta no encontrada"
]);*/

