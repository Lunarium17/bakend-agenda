<?php
include_once '../config/cors.php';
include_once '../config/db.php';
include_once '../config/jwt.php';

$database = new Database();
$db = $database->getConnection();
$data = json_decode(file_get_contents("php://input"));

if(isset($data->nombre_de_usuario) && isset($data->password)) {
    $query = "SELECT id, password FROM usuarios WHERE nombre_de_usuario = :user LIMIT 1";
    $stmt = $db->prepare($query);
    $stmt->bindParam(":user", $data->nombre_de_usuario);
    $stmt->execute();

    if($stmt->rowCount() > 0) {
        $row = $stmt->fetch(PDO::FETCH_ASSOC);
        if(password_verify($data->password, $row['password'])) {
            $jwt = new JWTHandler();
            $token = $jwt->generateToken(['id' => $row['id'], 'user' => $data->nombre_de_usuario]);
            
            echo json_encode(["message" => "Login exitoso", "token" => $token, "usuario_id" => $row['id']]);
        } else {
            http_response_code(401);
            echo json_encode(["message" => "Contraseña incorrecta"]);
        }
    } else {
        http_response_code(404);
        echo json_encode(["message" => "Usuario no encontrado"]);
    }
}
?>