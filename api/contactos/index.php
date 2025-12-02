<?php
include_once '../config/cors.php';
include_once '../config/db.php';
include_once '../config/jwt.php';

// Validar Token
$headers = getallheaders();
if(!isset($headers['Authorization'])) {
    http_response_code(401);
    echo json_encode(["message" => "Acceso denegado. Falta Token."]);
    exit();
}

$jwt = str_replace("Bearer ", "", $headers['Authorization']);
$jwtHandler = new JWTHandler();
$userData = $jwtHandler->validateToken($jwt);

if(!$userData) {
    http_response_code(401);
    echo json_encode(["message" => "Token inválido o expirado."]);
    exit();
}

// Obtener contactos SOLO del usuario autenticado
$database = new Database();
$db = $database->getConnection();

$query = "SELECT * FROM contactos WHERE usuario_id = :uid";
$stmt = $db->prepare($query);
$stmt->bindParam(":uid", $userData->id);
$stmt->execute();
$contactos = $stmt->fetchAll(PDO::FETCH_ASSOC);

echo json_encode($contactos);
?>