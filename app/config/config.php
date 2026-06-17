<?php
defined('ORION') || exit('Acesso negado.');

$host = strtolower($_SERVER['HTTP_HOST'] ?? $_SERVER['SERVER_NAME'] ?? '');
$host = explode(':', $host)[0];
$isLocal = ($host === '' || $host === 'localhost' || $host === '127.0.0.1' || $host === '::1');
define('APP_ENV', $isLocal ? 'local' : 'prod');

if (APP_ENV === 'local') {
    define('DB_HOST', '127.0.0.1');
    define('DB_NAME', 'orion');
    define('DB_USER', 'root');
    define('DB_PASS', 'Senai@118');
    define('DB_CHARSET', 'utf8mb4');
} else {
    define('DB_HOST', 'sql101.infinityfree.com');
    define('DB_NAME', 'if0_42207499_orion');
    define('DB_USER', 'if0_42207499');
    define('DB_PASS', 'Orion2026');
    define('DB_CHARSET', 'utf8mb4');
}

define('APP_NAME', 'Orion');
define('DAILY_RATE', 0.99);
define('LOYALTY_DISCOUNT', 0.30);
define('PLAYER_PLACEHOLDER_URL', 'https://www.youtube.com/embed/dQw4w9WgXcQ');

if (APP_ENV === 'local') {
    error_reporting(E_ALL);
    ini_set('display_errors', '1');
} else {
    error_reporting(0);
    ini_set('display_errors', '0');
}

date_default_timezone_set('America/Sao_Paulo');
