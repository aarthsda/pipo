<?php

if (session_status() !== PHP_SESSION_ACTIVE) {
    session_set_cookie_params([
        'lifetime' => 0,
        'path' => '/',
        'secure' => (!empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off'),
        'httponly' => true,
        'samesite' => 'Lax',
    ]);
    session_start();
}

function loginUser(string $type, int $id): void {
    $_SESSION['auth'] = [
        'type' => $type,
        'id' => $id,
    ];
    session_regenerate_id(true);

    // Mantém compatibilidade para páginas legadas que ainda leem estes cookies
    setcookie('user_type', $type, 0, '/');
    setcookie('user_id', (string) $id, 0, '/');

    // Remove legado inseguro
    setcookie('user_password', '', time() - 3600, '/');
}

function logoutUser(): void {
    $_SESSION = [];

    if (ini_get('session.use_cookies')) {
        $params = session_get_cookie_params();
        setcookie(session_name(), '', time() - 42000, $params['path'], $params['domain'], $params['secure'], $params['httponly']);
    }

    session_destroy();

    setcookie('user_type', 'guest', time() + 3600, '/');
    setcookie('user_id', '', time() - 3600, '/');
    setcookie('user_password', '', time() - 3600, '/');
}

function requireAuth(string $type): int {
    if (empty($_SESSION['auth']) || $_SESSION['auth']['type'] !== $type || empty($_SESSION['auth']['id'])) {
        header('Location: hub.php?msg=acessoproibido');
        exit();
    }

    return (int) $_SESSION['auth']['id'];
}
