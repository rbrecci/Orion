<?php
defined('ORION') || exit('Acesso negado.');

class Auth
{
    public static function check()
    {
        return !empty($_SESSION['user']);
    }

    public static function user()
    {
        return $_SESSION['user'] ?? null;
    }

    public static function id()
    {
        return $_SESSION['user']['id'] ?? null;
    }

    public static function isAdmin()
    {
        return self::check() && ($_SESSION['user']['role'] ?? '') === 'admin';
    }

    public static function login(array $user)
    {
        session_regenerate_id(true);
        $_SESSION['user'] = [
            'id'       => (int) $user['id'],
            'username' => $user['username'],
            'email'    => $user['email'],
            'role'     => $user['role'],
        ];
    }

    public static function logout()
    {
        $_SESSION = [];
        if (ini_get('session.use_cookies')) {
            $p = session_get_cookie_params();
            setcookie(session_name(), '', time() - 42000, $p['path'], $p['domain'], $p['secure'], $p['httponly']);
        }
        session_destroy();
    }

    public static function requireAdmin()
    {
        if (!self::isAdmin()) {
            flash('danger', 'Faça login como administrador para acessar o painel.');
            redirect('login');
        }
    }

    public static function requireAuth()
    {
        if (!self::check()) {
            flash('danger', 'Faça login para continuar.');
            redirect('login');
        }
    }

    public static function home()
    {
        return self::isAdmin() ? 'admin' : 'browse';
    }
}
