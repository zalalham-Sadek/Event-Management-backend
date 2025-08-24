<?php
use App\Core\RouterAPI;
use App\Controllers\UserController;
use App\Controllers\AuthController;
use App\Controllers\AdminController;
use App\Middleware\CorsMiddleware;

// User API endpoints
RouterAPI::get('/api/users', [UserController::class, 'getAllUsers']);
RouterAPI::get('/api/users/{id}', [UserController::class, 'getUser']);
RouterAPI::post('/api/users/create', [UserController::class, 'createUser']);
RouterAPI::put('/api/users/{id}', [UserController::class, 'updateUser']);
RouterAPI::delete('/api/users/{id}', [UserController::class, 'deleteUser']);

RouterAPI::get('/api/setup-admin', [AdminController::class, 'apiSetupAdmin']);
RouterAPI::post('/api/setup-admin', [AdminController::class, 'apiCreateAdmin']);
// Auth API endpoints
RouterAPI::post('/api/login', [AuthController::class, 'apiLogin']);
RouterAPI::post('/api/register', [AuthController::class, 'apiRegister']);
RouterAPI::get('/api/me', [AuthController::class, 'apiMe']);
RouterAPI::post('/api/logout', [AuthController::class, 'apiLogout']);

// Dispatch API routes
RouterAPI::dispatch($_SERVER['REQUEST_METHOD'], $_SERVER['REQUEST_URI']);


