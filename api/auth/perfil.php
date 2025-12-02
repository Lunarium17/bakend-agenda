<?php
include_once '../config/cors.php';
include_once '../config/db.php';
include_once '../config/jwt.php';

// 1. SEGURIDAD: Validar Token
$headers = getallheaders();
if(!isset($headers['Authorization'])) {
    http_response_code(401); exit();
}
$jwt = str_replace("Bearer ", "", $headers['Authorization']);
$jwtHandler = new JWTHandler();
$userData = $jwtHandler->validateToken($jwt);

if(!$userData) {
    http_response_code(401);
    echo json_encode(["message" => "Token inválido."]);
    exit();
}

// 2. OBTENER DATOS DEL USUARIO
$database = new Database();
$db = $database->getConnection();

// Seleccionamos datos seguros (NO devolvemos la contraseña)
$query = "SELECT id, nombre_de_usuario, fecha_registro FROM usuarios WHERE id = :id";
$stmt = $db->prepare($query);
$stmt->bindParam(":id", $userData->id);
$stmt->execute();

if($stmt->rowCount() > 0) {
    $row = $stmt->fetch(PDO::FETCH_ASSOC);
    echo json_encode($row);
} else {
    http_response_code(404);
    echo json_encode(["message" => "Usuario no encontrado."]);
}
?>