<?php declare(strict_types=1);

// Load the PHP / composer autloader
require_once __DIR__.'/../vendor/autoload.php';

// Bootstrap the framework (set app path (PATH) and initialize session)
require_once __DIR__.'/../config/bootstrap.php';

require_once PATH . 'Core/Request.php';
require_once PATH . 'Core/Router.php';

$routes = require_once __DIR__.'/../config/routes.php';

$router = new Router($routes);
$response = $router->handle(new Request());

echo $response;
