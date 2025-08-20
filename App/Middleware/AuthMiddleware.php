<?php
namespace App\Middleware;

class AuthMiddleware {
    public static function handle() {
        $publicRoutes = [
            '/login',
            '/register',
            '/setup-admin'
        ];

        $currentUri = parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH);

        // لو المستخدم غير مسجل وما هو في مسار عام → وجهه
        if (!isset($_SESSION['user']) && !in_array($currentUri, $publicRoutes)) {
            header("Location: /login");
            exit;
        }
    }
}
