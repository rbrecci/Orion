<?php
defined('ORION') || exit('Acesso negado.');

function e($value)
{
    return htmlspecialchars((string) $value, ENT_QUOTES, 'UTF-8');
}

function base_path()
{
    $dir = str_replace('\\', '/', dirname($_SERVER['SCRIPT_NAME']));
    return rtrim($dir, '/');
}

function url($route = '')
{
    $route = ltrim($route, '/');
    $base  = base_path() . '/index.php';
    return $route === '' ? $base : $base . '?url=' . $route;
}

function asset($path)
{
    return base_path() . '/assets/' . ltrim($path, '/');
}

function media($path)
{
    $path = (string) $path;
    if ($path === '') {
        return '';
    }
    return preg_match('#^https?://#i', $path) ? $path : asset($path);
}

function redirect($route = '')
{
    header('Location: ' . url($route));
    exit;
}

function is_post()
{
    return ($_SERVER['REQUEST_METHOD'] ?? 'GET') === 'POST';
}

function flash($type, $message)
{
    $_SESSION['_flash'][] = ['type' => $type, 'message' => $message];
}

function get_flashes()
{
    $f = $_SESSION['_flash'] ?? [];
    unset($_SESSION['_flash']);
    return $f;
}

function flash_old(array $data)
{
    unset($data['password'], $data['password_confirm']);
    $_SESSION['_old'] = $data;
}

function old($key, $default = '')
{
    return $_SESSION['_old'][$key] ?? $default;
}

function clear_old()
{
    unset($_SESSION['_old']);
}

function old_checked($key, $default = false)
{
    if (isset($_SESSION['_old'])) {
        return isset($_SESSION['_old'][$key]);
    }
    return (bool) $default;
}

function old_array($key, array $default = [])
{
    if (isset($_SESSION['_old'][$key]) && is_array($_SESSION['_old'][$key])) {
        return array_map('intval', $_SESSION['_old'][$key]);
    }
    return $default;
}

function csrf_token()
{
    if (empty($_SESSION['_csrf'])) {
        $_SESSION['_csrf'] = bin2hex(random_bytes(32));
    }
    return $_SESSION['_csrf'];
}

function csrf_field()
{
    return '<input type="hidden" name="_csrf" value="' . e(csrf_token()) . '">';
}

function verify_csrf()
{
    $sent = $_POST['_csrf'] ?? '';
    return is_string($sent) && hash_equals($_SESSION['_csrf'] ?? '', $sent);
}

function money($value)
{
    return 'R$ ' . number_format((float) $value, 2, ',', '.');
}

function dt($datetime)
{
    if (empty($datetime)) {
        return '';
    }
    $ts = strtotime($datetime);
    return $ts ? date('d/m/Y H:i', $ts) : '';
}

function slugify($text)
{
    $text = (string) $text;
    if (function_exists('iconv')) {
        $conv = @iconv('UTF-8', 'ASCII//TRANSLIT//IGNORE', $text);
        if ($conv !== false) {
            $text = $conv;
        }
    }
    $text = strtolower($text);
    $text = preg_replace('/[^a-z0-9]+/', '-', $text);
    return trim($text, '-');
}
