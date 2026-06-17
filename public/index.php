<?php
define('ORION', true);

require __DIR__ . '/../app/bootstrap.php';

$router = new Router();
require APP_PATH . '/routes.php';
$router->dispatch();
