<?php

// Start session globally once
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
require __DIR__ . '/vendor/autoload.php';
$dotenv = Dotenv\Dotenv::createImmutable(__DIR__ );
$dotenv->load();

// Determine if this is an API request
$requestUri = $_SERVER['REQUEST_URI'] ?? '';
$isApiRequest = strpos($requestUri, '/api/') === 0;

// Always apply CORS middleware first for API requests
if ($isApiRequest) {
    // Apply CORS middleware for API requests
    \App\Middleware\CorsMiddleware::handle();
    
    // Handle API routes
    require __DIR__ . '/routes/api.php';
} else {
    // Handle web routes with admin middleware
    \App\Middleware\AdminMiddleware::handle();
    require __DIR__ . '/routes/web.php';
}



