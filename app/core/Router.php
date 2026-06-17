<?php
defined('ORION') || exit('Acesso negado.');

class Router
{
    private $routes = ['GET' => [], 'POST' => []];

    public function get($pattern, $handler)
    {
        $this->routes['GET'][$pattern] = $handler;
    }

    public function post($pattern, $handler)
    {
        $this->routes['POST'][$pattern] = $handler;
    }

    public function dispatch()
    {
        $method = $_SERVER['REQUEST_METHOD'] ?? 'GET';
        if (!isset($this->routes[$method])) {
            $method = 'GET';
        }

        $uri = trim($_GET['url'] ?? '', '/');

        foreach ($this->routes[$method] as $pattern => $handler) {
            $regex = '#^' . preg_replace('#\{[a-zA-Z_]+\}#', '([^/]+)', $pattern) . '$#';
            if (preg_match($regex, $uri, $matches)) {
                array_shift($matches);
                return $this->run($handler, $matches);
            }
        }

        http_response_code(404);
        $this->run('AuthController@notFound', []);
    }

    private function run($handler, array $params)
    {
        if ($handler instanceof Closure) {
            return call_user_func_array($handler, $params);
        }

        list($controllerName, $action) = explode('@', $handler);

        if (!class_exists($controllerName)) {
            http_response_code(500);
            exit('Controller não encontrado: ' . e($controllerName));
        }

        $controller = new $controllerName();

        if (!method_exists($controller, $action)) {
            http_response_code(500);
            exit('Ação não encontrada: ' . e($action));
        }

        return call_user_func_array([$controller, $action], $params);
    }
}
