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

if(isset($_GET['id'])) {
    
    $query = "DELETE FROM contactos WHERE id = :id AND usuario_id = :uid";
    $stmt = $db->prepare($query);
    
    $stmt->bindParam(":id", $_GET['id']);
    $stmt->bindParam(":uid", $userData->id);

    if($stmt->execute()) {
        if($stmt->rowCount() > 0) {
            echo json_encode(["message" => "Contacto eliminado."]);
        } else {
            echo json_encode(["message" => "No se pudo eliminar. El contacto no existe o no tienes permiso."]);
        }
    } else {
        http_response_code(503);
        echo json_encode(["message" => "Error al eliminar."]);
    }
} else {
    http_response_code(400);
    echo json_encode(["message" => "Falta el ID del contacto."]);
}
?>