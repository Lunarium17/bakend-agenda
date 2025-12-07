<?php
// Este archivo será el punto de entrada de tu API en Render

// Habilitar CORS
require_once __DIR__ . "/api/config/cors.php";

// Router simple: detecta la ruta solicitada
$uri = parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH);

// Elimina el prefijo si Render agrega una ruta extra
$uri = str_replace("/api", "", $uri);

// Rutas principales
if (strpos($uri, "/auth") === 0) {
    require __DIR__ . "/api/auth" . str_replace("/auth", "", $uri) . ".php";
    exit;
}

if (strpos($uri, "/contactos") === 0) {
    require __DIR__ . "/api/contactos" . str_replace("/contactos", "", $uri) . ".php";
    exit;
}

// Si no coincide con nada
http_response_code(404);
echo json_encode(["ok" => false, "message" => "Ruta no encontrada"]);
