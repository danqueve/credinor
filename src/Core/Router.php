<?php
// src/Core/Router.php
namespace App\Core;

use FastRoute\Dispatcher;
use FastRoute\RouteCollector;
use function FastRoute\simpleDispatcher;

class Router
{
    private array $routes = [];
    private array $middlewares = [];

    public function get(string $pattern, array $handler, array $middlewares = []): void
    {
        $this->addRoute('GET', $pattern, $handler, $middlewares);
    }

    public function post(string $pattern, array $handler, array $middlewares = []): void
    {
        $this->addRoute('POST', $pattern, $handler, $middlewares);
    }

    private function addRoute(string $method, string $pattern, array $handler, array $middlewares): void
    {
        $this->routes[] = compact('method', 'pattern', 'handler', 'middlewares');
    }

    public function dispatch(string $method, string $uri): void
    {
        $dispatcher = simpleDispatcher(function (RouteCollector $r) {
            foreach ($this->routes as $route) {
                $r->addRoute($route['method'], $route['pattern'], [
                    'handler'     => $route['handler'],
                    'middlewares' => $route['middlewares'],
                ]);
            }
        });

        // Strip query string
        $uri = strtok($uri, '?');
        // Strip base path if running in subdirectory
        $basePath = parse_url(config('url'), PHP_URL_PATH) ?? '';
        if ($basePath && str_starts_with($uri, $basePath)) {
            $uri = substr($uri, strlen($basePath));
        }
        $uri = '/' . ltrim($uri, '/');

        $routeInfo = $dispatcher->dispatch($method, $uri);

        switch ($routeInfo[0]) {
            case Dispatcher::NOT_FOUND:
                http_response_code(404);
                require ROOT_PATH . '/views/errors/404.php';
                break;

            case Dispatcher::METHOD_NOT_ALLOWED:
                http_response_code(405);
                echo 'Método no permitido';
                break;

            case Dispatcher::FOUND:
                $route  = $routeInfo[1];
                $params = $routeInfo[2];

                // Run middlewares
                foreach ($route['middlewares'] as $middlewareClass) {
                    $middleware = new $middlewareClass();
                    $middleware->handle();
                }

                [$controllerClass, $action] = $route['handler'];
                $controller = new $controllerClass();
                $controller->$action($params);
                break;
        }
    }
}
