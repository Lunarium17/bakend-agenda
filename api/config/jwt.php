<?php
class JWTHandler {
    private $secret_key = "IGLOIWTR*5mark"; // Cambia esto

    // Generar Token
    public function generateToken($data) {
        $header = json_encode(['typ' => 'JWT', 'alg' => 'HS256']);
        $payload = json_encode($data);
        
        $base64UrlHeader = str_replace(['+', '/', '='], ['-', '_', ''], base64_encode($header));
        $base64UrlPayload = str_replace(['+', '/', '='], ['-', '_', ''], base64_encode($payload));
        
        $signature = hash_hmac('sha256', $base64UrlHeader . "." . $base64UrlPayload, $this->secret_key, true);
        $base64UrlSignature = str_replace(['+', '/', '='], ['-', '_', ''], base64_encode($signature));
        
        return $base64UrlHeader . "." . $base64UrlPayload . "." . $base64UrlSignature;
    }

    // Validar Token y devolver datos del usuario
    public function validateToken($jwt) {
        $tokenParts = explode('.', $jwt);
        if (count($tokenParts) != 3) return null;

        $header = base64_decode($tokenParts[0]);
        $payload = base64_decode($tokenParts[1]);
        $signature_provided = $tokenParts[2];

        // Verificar firma
        $base64UrlHeader = str_replace(['+', '/', '='], ['-', '_', ''], base64_encode($header));
        $base64UrlPayload = str_replace(['+', '/', '='], ['-', '_', ''], base64_encode($payload));
        $signature = hash_hmac('sha256', $base64UrlHeader . "." . $base64UrlPayload, $this->secret_key, true);
        $base64UrlSignature = str_replace(['+', '/', '='], ['-', '_', ''], base64_encode($signature));

        if ($base64UrlSignature === $signature_provided) {
            return json_decode($payload);
        }
        return null;
    }
}
?>