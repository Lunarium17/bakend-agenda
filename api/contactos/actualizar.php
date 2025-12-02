<?php
include_once '../config/cors.php';
include_once '../config/db.php';
include_once '../config/jwt.php';

// --- INICIO BLOQUE DE SEGURIDAD ---
$headers = getallheaders();
if(!isset($headers['Authorization'])) {
    http_response_code(401); exit();
}
$jwt = str_replace("Bearer ", "", $headers['Authorization']);
$jwtHandler = new JWTHandler();
$userData = $jwtHandler->validateToken($jwt);
if(!$userData) {
    http_response_code(401); exit();
}
// --- FIN BLOQUE DE SEGURIDAD ---

$database = new Database();
$db = $database->getConnection();
$data = json_decode(file_get_contents("php://input"));

// Verificar que venga el ID en la URL y datos en el cuerpo
if(isset($_GET['id']) && !empty($data->nombre)) {
    
    $query = "UPDATE contactos 
              SET nombre = :nom, apellido = :ape, telefono = :tel, 
                  email = :email, direccion = :dir, notas = :notas
              WHERE id = :id AND usuario_id = :uid";
    
    $stmt = $db->prepare($query);

    // Bindings
    $stmt->bindParam(":nom", $data->nombre);
    $stmt->bindParam(":ape", $data->apellido);
    $stmt->bindParam(":tel", $data->telefono);
    $stmt->bindParam(":email", $data->email);
    $stmt->bindParam(":dir", $data->direccion);
    $stmt->bindParam(":notas", $data->notas);
    
    $stmt->bindParam(":id", $_GET['id']);
    $stmt->bindParam(":uid", $userData->id); // Seguridad extra

    if($stmt->execute()) {
        if($stmt->rowCount() > 0){
             echo json_encode(["message" => "Contacto actualizado."]);
        } else {
             // Si no afectó filas, puede que el ID no exista O que no sea dueño del contacto
             echo json_encode(["message" => "No se actualizó. Verifica si el contacto existe y es tuyo."]);
        }
    } else {
        http_response_code(503);
        echo json_encode(["message" => "Error al actualizar."]);
    }
} else {
    http_response_code(400);
    echo json_encode(["message" => "Faltan datos o ID."]);
}
?>