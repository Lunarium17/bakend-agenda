<?php
include_once '../config/cors.php';
include_once '../config/db.php';
include_once '../config/jwt.php';

// 1. Validar Token
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

// 2. Conectar BD y recibir datos
$database = new Database();
$db = $database->getConnection();
$data = json_decode(file_get_contents("php://input"));

if(!empty($data->nombre_de_usuario)) {
    
    // VALIDACIÓN DE CONTRASEÑA REPETIDA
    if(!empty($data->password)) {
        // Primero obtenemos la contraseña actual de la BD
        $qCurrent = "SELECT password FROM usuarios WHERE id = :id";
        $stmtCurrent = $db->prepare($qCurrent);
        $stmtCurrent->bindParam(":id", $userData->id);
        $stmtCurrent->execute();
        $row = $stmtCurrent->fetch(PDO::FETCH_ASSOC);

        // Verificamos si la NUEVA contraseña coincide con el HASH actual
        if(password_verify($data->password, $row['password'])) {
            http_response_code(400); // Bad Request
            echo json_encode(["message" => "La nueva contraseña no puede ser igual a la actual."]);
            exit(); // Detenemos todo aquí
        }
    }

    // --- SI PASA LA VALIDACIÓN, ACTUALIZAMOS ---
    
    $query = "UPDATE usuarios SET nombre_de_usuario = :user";
    
    if(!empty($data->password)) {
        $query .= ", password = :pass";
    }
    
    $query .= " WHERE id = :id";
    
    $stmt = $db->prepare($query);
    $stmt->bindParam(":user", $data->nombre_de_usuario);
    $stmt->bindParam(":id", $userData->id);
    
    if(!empty($data->password)) {
        // Encriptamos la nueva contraseña
        $password_hash = password_hash($data->password, PASSWORD_BCRYPT);
        $stmt->bindParam(":pass", $password_hash);
    }

    try {
        if($stmt->execute()) {
            echo json_encode(["message" => "Perfil actualizado exitosamente."]);
        } else {
            http_response_code(503);
            echo json_encode(["message" => "No se pudo actualizar."]);
        }
    } catch(PDOException $e) {
        http_response_code(409);
        echo json_encode(["message" => "El nombre de usuario ya existe."]);
    }

} else {
    http_response_code(400);
    echo json_encode(["message" => "Datos incompletos."]);
}
?>