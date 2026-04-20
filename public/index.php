<?php

require __DIR__ . '/../bootstrap/app.php';

$router = new \App\Helpers\Router();

require BASE_PATH . '/routes/web.php';
require BASE_PATH . '/routes/admin.php';

$method = $_SERVER['REQUEST_METHOD'];
$uri = $_SERVER['REQUEST_URI'];

$router->dispatch($method, $uri);
