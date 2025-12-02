<?php
// 1. Incluir configuraciones básicas
include_once '../config/cors.php';
include_once '../config/db.php';

// 2. Conexión a Base de Datos
$database = new Database();
$db = $database->getConnection();

// 3. Obtener los datos enviados (JSON)
$data = json_decode(file_get_contents("php://input"));

// 4. Validar que los datos no estén vacíos
if(!empty($data->nombre_de_usuario) && !empty($data->password)) {

    // A. VERIFICAR SI EL USUARIO YA EXISTE
    // Antes de insertar, revisamos si ese nombre ya está ocupado
    $checkQuery = "SELECT id FROM usuarios WHERE nombre_de_usuario = :user LIMIT 1";
    $checkStmt = $db->prepare($checkQuery);
    $checkStmt->bindParam(":user", $data->nombre_de_usuario);
    $checkStmt->execute();

    if($checkStmt->rowCount() > 0){
        // El usuario ya existe, devolvemos error "Conflict" (409)
        http_response_code(409);
        echo json_encode(["message" => "El nombre de usuario ya existe. Elige otro."]);
    } else {
        // B. CREAR EL NUEVO USUARIO
        $query = "INSERT INTO usuarios (nombre_de_usuario, password) VALUES (:user, :pass)";
        $stmt = $db->prepare($query);

        // C. ENCRIPTAR LA CONTRASEÑA (¡Muy Importante!)
        // Nunca guardes contraseñas en texto plano. Usamos BCRYPT.
        $password_hash = password_hash($data->password, PASSWORD_BCRYPT);

        // Asignar valores
        $stmt->bindParam(":user", $data->nombre_de_usuario);
        $stmt->bindParam(":pass", $password_hash);

        if($stmt->execute()) {
            // Éxito: Creado (201)
            http_response_code(201);
            echo json_encode(["message" => "Usuario registrado exitosamente."]);
        } else {
            // Error del servidor (503)
            http_response_code(503);
            echo json_encode(["message" => "No se pudo registrar el usuario."]);
        }
    }
} else {
    // Datos incompletos (400)
    http_response_code(400);
    echo json_encode(["message" => "Datos incompletos. Se requiere usuario y contraseña."]);
}
?>