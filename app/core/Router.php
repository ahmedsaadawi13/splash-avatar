<?php
// FILE: /app/core/Router.php

class Router {
    private $routes = [];
    private $middlewares = [];

    public function get($uri, $action, $middlewares = []) {
        $this->addRoute('GET', $uri, $action, $middlewares);
        return $this;
    }

    public function post($uri, $action, $middlewares = []) {
        $this->addRoute('POST', $uri, $action, $middlewares);
        return $this;
    }

    public function put($uri, $action, $middlewares = []) {
        $this->addRoute('PUT', $uri, $action, $middlewares);
        return $this;
    }

    public function delete($uri, $action, $middlewares = []) {
        $this->addRoute('DELETE', $uri, $action, $middlewares);
        return $this;
    }

    private function addRoute($method, $uri, $action, $middlewares) {
        $this->routes[] = [
            'method' => $method,
            'uri' => $uri,
            'action' => $action,
            'middlewares' => $middlewares,
        ];
    }

    public function dispatch() {
        $requestUri = Request::uri();
        $requestMethod = Request::method();

        foreach ($this->routes as $route) {
            $pattern = $this->convertToPattern($route['uri']);

            if ($route['method'] === $requestMethod && preg_match($pattern, $requestUri, $matches)) {
                array_shift($matches);

                // Run middlewares
                foreach ($route['middlewares'] as $middleware) {
                    $middlewareInstance = new $middleware();
                    $middlewareInstance->handle();
                }

                // Execute action
                if (is_callable($route['action'])) {
                    return call_user_func_array($route['action'], $matches);
                }

                if (is_string($route['action'])) {
                    return $this->callControllerAction($route['action'], $matches);
                }
            }
        }

        Response::notFound();
    }

    private function convertToPattern($uri) {
        $pattern = preg_replace('/\{([a-zA-Z0-9_]+)\}/', '([a-zA-Z0-9_-]+)', $uri);
        return '#^' . $pattern . '$#';
    }

    private function callControllerAction($action, $params) {
        list($controller, $method) = explode('@', $action);

        $controllerFile = __DIR__ . '/../controllers/' . $controller . '.php';
        if (!file_exists($controllerFile)) {
            throw new Exception("Controller {$controller} not found");
        }

        require_once $controllerFile;

        if (!class_exists($controller)) {
            throw new Exception("Controller class {$controller} not found");
        }

        $controllerInstance = new $controller();

        if (!method_exists($controllerInstance, $method)) {
            throw new Exception("Method {$method} not found in controller {$controller}");
        }

        return call_user_func_array([$controllerInstance, $method], $params);
    }
}
