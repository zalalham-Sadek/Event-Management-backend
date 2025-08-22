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

    private static string $groupPrefix = '';   // لتخزين الـ prefix مؤقتاً
    private static array $groupMiddleware = []; // لتخزين الـ middleware الخاص بالمجموعة

    public static function get(string $path, $callback, array $middleware = [])    { 
        self::$routes['GET'][] = [self::$groupPrefix . $path, $callback, array_merge(self::$groupMiddleware, $middleware)];
    }
    public static function post(string $path, $callback, array $middleware = [])   { 
        self::$routes['POST'][] = [self::$groupPrefix . $path, $callback, array_merge(self::$groupMiddleware, $middleware)];
    }
    public static function put(string $path, $callback, array $middleware = [])    { 
        self::$routes['PUT'][] = [self::$groupPrefix . $path, $callback, array_merge(self::$groupMiddleware, $middleware)];
    }
    public static function patch(string $path, $callback, array $middleware = [])  { 
        self::$routes['PATCH'][] = [self::$groupPrefix . $path, $callback, array_merge(self::$groupMiddleware, $middleware)];
    }
    public static function delete(string $path, $callback, array $middleware = []) { 
        self::$routes['DELETE'][] = [self::$groupPrefix . $path, $callback, array_merge(self::$groupMiddleware, $middleware)];
    }

    // ✅ group function
    public static function group(string $prefix, callable $callback, array $middleware = []): void {
        $oldPrefix = self::$groupPrefix;
        $oldMiddleware = self::$groupMiddleware;

        // أضف الـ prefix الحالي + الجديد
        self::$groupPrefix .= $prefix;
        self::$groupMiddleware = array_merge(self::$groupMiddleware, $middleware);

        // نفذ كل الـ routes داخل المجموعة
        $callback();

        // رجّع القيم القديمة عشان ما تأثر على باقي الـ routes
        self::$groupPrefix = $oldPrefix;
        self::$groupMiddleware = $oldMiddleware;
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
                    $mw::handle();
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
}
