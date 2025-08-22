<?php
namespace App\Middleware;

class CorsMiddleware {
    public static function handle() {
        // Debug CORS requests
        error_log('CORS Middleware: ' . $_SERVER['REQUEST_METHOD'] . ' ' . $_SERVER['REQUEST_URI']);
        error_log('Origin: ' . ($_SERVER['HTTP_ORIGIN'] ?? 'none'));
        error_log('All headers: ' . print_r(getallheaders(), true));
        
        // Always set CORS headers regardless of origin
        // You can change this in production or make it configurable via .env
        $allowedOrigins = [
            'http://localhost:5173',  // Vue CLI default
            'http://localhost:3000',  // Another common development port
            'http://localhost:8080',  // Another common development port
            'http://127.0.0.1:5173',  // Using IP instead of localhost
            'http://127.0.0.1:3000',
            'http://127.0.0.1:8080'
        ];
        
        $origin = $_SERVER['HTTP_ORIGIN'] ?? '';
        
        // Always allow the origin for development
        if (!empty($origin)) {
            header("Access-Control-Allow-Origin: $origin");
            error_log("Setting Access-Control-Allow-Origin: $origin");
        } else {
            // Default fallback
            header("Access-Control-Allow-Origin: http://localhost:5173");
            error_log("Setting default Access-Control-Allow-Origin: http://localhost:5173");
        }
        
        // Allow cookies and session
        header("Access-Control-Allow-Credentials: true");
        
        // Allow these HTTP methods
        header("Access-Control-Allow-Methods: GET, POST, PUT, PATCH, DELETE, OPTIONS");
        
        // Allow these headers
        header("Access-Control-Allow-Headers: Content-Type, Authorization, X-Requested-With, X-CSRF-TOKEN");
        
        // Cache preflight response for 1 hour (3600 seconds)
        header("Access-Control-Max-Age: 3600");
        
        // Handle preflight OPTIONS request
        if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') {
            error_log('Handling OPTIONS preflight request');
            http_response_code(204); // No content needed for OPTIONS
            exit;
        }
        
        // Debug: Log that CORS headers have been set
        error_log('CORS headers set successfully');
    }
}
