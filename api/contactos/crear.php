<?php
include_once '../config/cors.php';
include_once '../config/db.php';
include_once '../config/jwt.php';

// --- INICIO BLOQUE DE SEGURIDAD ---
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
    echo json_encode(["message" => "Token inválido."]);
    exit();
}
// --- FIN BLOQUE DE SEGURIDAD ---

// Lógica de Crear
$database = new Database();
$db = $database->getConnection();
$data = json_decode(file_get_contents("php://input"));

// Validar que vengan los datos obligatorios
if(!empty($data->nombre) && !empty($data->telefono)) {
    
    $query = "INSERT INTO contactos 
              (usuario_id, nombre, apellido, telefono, email, direccion, notas) 
              VALUES (:uid, :nom, :ape, :tel, :email, :dir, :notas)";
    
    $stmt = $db->prepare($query);

    // Asignar valores
    // IMPORTANTE: El usuario_id viene del Token ($userData), no del formulario
    $stmt->bindParam(":uid", $userData->id);
    $stmt->bindParam(":nom", $data->nombre);
    $stmt->bindParam(":ape", $data->apellido);
    $stmt->bindParam(":tel", $data->telefono);
    $stmt->bindParam(":email", $data->email);
    $stmt->bindParam(":dir", $data->direccion);
    $stmt->bindParam(":notas", $data->notas);

    if($stmt->execute()) {
        http_response_code(201);
        echo json_encode(["message" => "Contacto creado exitosamente."]);
    } else {
        http_response_code(503);
        echo json_encode(["message" => "No se pudo crear el contacto."]);
    }
} else {
    http_response_code(400);
    echo json_encode(["message" => "Datos incompletos. Nombre y teléfono son obligatorios."]);
}
?>