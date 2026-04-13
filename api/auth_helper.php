<?php
// Chave secreta para gerar/validar tokens HMAC
// Em produção, mova para variável de ambiente
define('AUTH_SECRET_KEY', 'lanches_express_secret_key_2024_#xK9!');

/**
 * Gera um token para o usuário dado id + email
 */
function gerarToken(int $usuario_id, string $email): string {
    $payload = $usuario_id . ':' . $email;
    return base64_encode($payload . ':' . hash_hmac('sha256', $payload, AUTH_SECRET_KEY));
}

/**
 * Valida o token do header Authorization: Bearer {token}
 * Fallback: aceita X-User-Id + X-User-Email se token estiver ausente
 * Retorna o usuario_id se válido, ou false se inválido
 */
function validarToken(): int|false {
    $headers = getallheaders();
    $authHeader = $headers['Authorization'] ?? $headers['authorization'] ?? '';

    // --- Estratégia 1: Bearer token ---
    if (preg_match('/^Bearer\s+(.+)$/i', $authHeader, $matches)) {
        $tokenRaw = $matches[1];
        $decoded  = base64_decode($tokenRaw, true);
        if ($decoded === false) return false;

        $lastColon = strrpos($decoded, ':');
        if ($lastColon === false) return false;

        $payload  = substr($decoded, 0, $lastColon);
        $hmac     = substr($decoded, $lastColon + 1);
        $expected = hash_hmac('sha256', $payload, AUTH_SECRET_KEY);

        if (!hash_equals($expected, $hmac)) return false;

        $firstColon = strpos($payload, ':');
        if ($firstColon === false) return false;

        $usuarioId = (int) substr($payload, 0, $firstColon);
        return $usuarioId > 0 ? $usuarioId : false;
    }

    // --- Estratégia 2: X-User-Id + X-User-Email (fallback para sessões sem token) ---
    $xUserId    = $headers['X-User-Id']    ?? $headers['x-user-id']    ?? '';
    $xUserEmail = $headers['X-User-Email'] ?? $headers['x-user-email'] ?? '';

    if ($xUserId !== '' && $xUserEmail !== '') {
        $usuarioId = (int) $xUserId;
        if ($usuarioId <= 0 || !filter_var($xUserEmail, FILTER_VALIDATE_EMAIL)) {
            return false;
        }
        // Verificar se o par id+email realmente existe no banco
        try {
            $db   = getDB();
            $stmt = $db->prepare("SELECT id FROM usuarios WHERE id = ? AND email = ? AND ativo = TRUE LIMIT 1");
            $stmt->execute([$usuarioId, $xUserEmail]);
            return $stmt->fetch() ? $usuarioId : false;
        } catch (Exception $e) {
            return false;
        }
    }

    return false;
}
