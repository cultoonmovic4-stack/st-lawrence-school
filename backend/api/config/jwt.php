<?php
// JWT configuration
class JWT {
    private static $secret_key = "st_lawrence_school_secret_key_2026";
    private static $issuer = "st_lawrence_school";
    private static $audience = "st_lawrence_admin";
    
    // Generate JWT token
    public static function encode($data) {
        $issuedAt = time();
        $expire = $issuedAt + 3600; // Token valid for 1 hour
        
        $token = array(
            "iss" => self::$issuer,
            "aud" => self::$audience,
            "iat" => $issuedAt,
            "exp" => $expire,
            "data" => $data
        );
        
        return self::generateToken($token);
    }
    
    // Decode JWT token
    public static function decode($jwt) {
        try {
            $token = self::verifyToken($jwt);
            
            if ($token->exp < time()) {
                return null; // Token expired
            }
            
            return $token->data;
        } catch (Exception $e) {
            return null;
        }
    }
    
    // Simple JWT generation (base64 encoding)
    private static function generateToken($payload) {
        $header = json_encode(['typ' => 'JWT', 'alg' => 'HS256']);
        $payload = json_encode($payload);
        
        $base64UrlHeader = self::base64UrlEncode($header);
        $base64UrlPayload = self::base64UrlEncode($payload);
        
        $signature = hash_hmac('sha256', $base64UrlHeader . "." . $base64UrlPayload, self::$secret_key, true);
        $base64UrlSignature = self::base64UrlEncode($signature);
        
        return $base64UrlHeader . "." . $base64UrlPayload . "." . $base64UrlSignature;
    }
    
    // Verify JWT token
    private static function verifyToken($jwt) {
        $tokenParts = explode('.', $jwt);
        $header = base64_decode($tokenParts[0]);
        $payload = base64_decode($tokenParts[1]);
        $signatureProvided = $tokenParts[2];
        
        $base64UrlHeader = self::base64UrlEncode($header);
        $base64UrlPayload = self::base64UrlEncode($payload);
        $signature = hash_hmac('sha256', $base64UrlHeader . "." . $base64UrlPayload, self::$secret_key, true);
        $base64UrlSignature = self::base64UrlEncode($signature);
        
        if ($base64UrlSignature !== $signatureProvided) {
            throw new Exception("Invalid token signature");
        }
        
        return json_decode($payload);
    }
    
    private static function base64UrlEncode($text) {
        return str_replace(['+', '/', '='], ['-', '_', ''], base64_encode($text));
    }
}
?>
