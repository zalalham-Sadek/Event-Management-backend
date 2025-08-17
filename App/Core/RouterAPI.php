<?php
namespace App\Core;

class Router {
    private static array $routes = [
        'GET'    => [],
        'POST'   => [],
        'PUT'    => [],
        'PATCH'  => [],
        'DELETE' => [],
    ];

    public static function get(string $path, $callback)    { self::$routes['GET'][]    = [$path, $callback]; }
    public static function post(string $path, $callback)   { self::$routes['POST'][]   = [$path, $callback]; }
    public static function put(string $path, $callback)    { self::$routes['PUT'][]    = [$path, $callback]; }
    public static function patch(string $path, $callback)  { self::$routes['PATCH'][]  = [$path, $callback]; }
    public static function delete(string $path, $callback) { self::$routes['DELETE'][] = [$path, $callback]; }

    public static function dispatch(string $method, string $uri): void {
        header('Content-Type: application/json; charset=utf-8');

        // 1) Extract path (no query string)
        $path = parse_url($uri, PHP_URL_PATH) ?? '/';

        // 2) Auto-strip base directory like /PHP-Project
        $base = rtrim(dirname($_SERVER['SCRIPT_NAME']), '/');   // e.g. /PHP-Project or /public
        if ($base !== '' && $base !== '/' && str_starts_with($path, $base)) {
            $path = substr($path, strlen($base));
            if ($path === false || $path === '') $path = '/';
        }

        // 3) Normalize (no trailing slash except root)
        if ($path !== '/' && str_ends_with($path, '/')) {
            $path = rtrim($path, '/');
        }

        // 4) Find a matching route for this HTTP method
    foreach (self::$routes[$method] ?? [] as [$route, $callback]) {
        $regex = preg_replace_callback('/\{([a-zA-Z_][a-zA-Z0-9_]*)(?::([^}]+))?\}/', function($m){
            $pattern = $m[2] ?? '[^/]+';
            return '(' . $pattern . ')';
        }, $route);

        $pattern = '#^' . rtrim($regex, '/') . '/?$#';

        if (preg_match($pattern, $path, $matches)) {
            array_shift($matches);

            if (is_array($callback)) {
                [$class, $methodName] = $callback;
                $controller = is_string($class) ? new $class() : $class;
                call_user_func_array([$controller, $methodName], $matches);
            } else {
                call_user_func_array($callback, $matches);
            }

            return; // <--- stop execution after a successful match
        }
    }

        // Only send 404 if no route matched
        http_response_code(404);
        echo json_encode(['error' => 'Not Found']);

    }
}
