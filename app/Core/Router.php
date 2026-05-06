<?php

namespace App\Core;

class Router
{
    protected $routes = [];

    public function add($route, $controller, $method)
    {
        $this->routes[$route] = [
            'controller' => $controller,
            'method' => $method
        ];
    }

    public function dispatch($url)
    {
        $url = trim($url, '/');
        if (empty($url)) $url = 'home';

        if (isset($this->routes[$url])) {
            $controllerName = $this->routes[$url]['controller'];
            $method = $this->routes[$url]['method'];

            $controller = new $controllerName();
            $controller->$method();
        } else {
            header("HTTP/1.0 404 Not Found");
            echo "404 - Page not found";
        }
    }
}
