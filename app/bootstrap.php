<?php
defined('ORION') || exit('Acesso negado.');

define('APP_PATH',  __DIR__);
define('VIEW_PATH', APP_PATH . '/views');

require APP_PATH . '/config/config.php';
require APP_PATH . '/core/helpers.php';

if (session_status() !== PHP_SESSION_ACTIVE) {
    $secure = (!empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off');
    session_set_cookie_params([
        'lifetime' => 0,
        'path'     => '/',
        'httponly' => true,
        'secure'   => $secure,
        'samesite' => 'Lax',
    ]);
    session_name('orion_session');
    session_start();
}

spl_autoload_register(function ($class) {
    foreach (['core', 'config', 'controllers', 'models'] as $dir) {
        $file = APP_PATH . '/' . $dir . '/' . $class . '.php';
        if (is_file($file)) {
            require $file;
            return;
        }
    }
});
