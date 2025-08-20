<?php
namespace App\Core;
use App\Middleware\AuthMiddleware;

class Router {
    private static array $routes = [
        'GET'    => [],
        'POST'   => [],
        'PUT'    => [],
        'PATCH'  => [],
        'DELETE' => [],
    ];

    public static function get(string $path, $callback, array $middleware = [])    { 
        self::$routes['GET'][] = [$path, $callback, $middleware]; 
    }
    public static function post(string $path, $callback, array $middleware = [])   { 
        self::$routes['POST'][] = [$path, $callback, $middleware]; 
    }
    public static function put(string $path, $callback, array $middleware = [])    { 
        self::$routes['PUT'][] = [$path, $callback, $middleware]; 
    }
    public static function patch(string $path, $callback, array $middleware = [])  { 
        self::$routes['PATCH'][] = [$path, $callback, $middleware]; 
    }
    public static function delete(string $path, $callback, array $middleware = []) { 
        self::$routes['DELETE'][] = [$path, $callback, $middleware]; 
    }

    public static function dispatch(string $method, string $uri): void {
        $path = parse_url($uri, PHP_URL_PATH) ?? '/';

        $base = rtrim(dirname($_SERVER['SCRIPT_NAME']), '/');
        if ($base !== '' && $base !== '/' && str_starts_with($path, $base)) {
            $path = substr($path, strlen($base)) ?: '/';
        }
        if ($path !== '/' && str_ends_with($path, '/')) $path = rtrim($path, '/');

        foreach (self::$routes[$method] ?? [] as [$route, $callback, $middleware]) {
            $regex = preg_replace_callback('/\{([a-zA-Z_][a-zA-Z0-9_]*)(?::([^}]+))?\}/', function($m){
                return '(' . ($m[2] ?? '[^/]+') . ')';
            }, $route);

            $pattern = '#^' . rtrim($regex, '/') . '/?$#';

            if (preg_match($pattern, $path, $matches)) {
                array_shift($matches);

                // ✨ Execute middleware
                foreach ($middleware as $mw) {
                    $mw::handle(); // كل middleware عنده static handle
                }

                // ✨ Execute controller
                if (is_array($callback)) {
                    [$class, $methodName] = $callback;
                    $controller = is_string($class) ? new $class() : $class;
                    call_user_func_array([$controller, $methodName], $matches);
                } else {
                    call_user_func_array($callback, $matches);
                }

                return;
            }
        }

        http_response_code(404);
        echo json_encode(['error' => 'Not Found']);
    }

    public static function auth(callable $callback) {
    AuthMiddleware::handle();  // ينفذ مرة واحدة
    $callback();                // ينفذ كل الـ routes داخل المجموعة
}

// استخدامه


}

