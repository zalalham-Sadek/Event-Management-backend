<?php
use App\Core\Router;
use App\Controllers\UserController;
use App\Controllers\AuthController;
use App\Controllers\HomeController;
use App\Controllers\AdminController;

Router::get('/',[HomeController::class,'index']);
Router::get('/setup-admin', [AdminController::class, 'setupAdmin']);
Router::post('/setup-admin', [AdminController::class, 'createAdmin']);
// Router::get('/users', [ UserController::class, 'index']);
// Router::get('/users/edit/{id}', [ UserController::class, 'edit']);
Router::get('/register', [ AuthController::class, 'registerForm']);
Router::post('/register', [ AuthController::class, 'register']);
Router::get('/login', [ AuthController::class, 'loginForm']);
Router::post('/login', [ AuthController::class, 'login']);
Router::get('/logout', [ AuthController::class, 'logout']);
Router::auth(function() {
    Router::get('/users', [UserController::class, 'index']);
    Router::get('/users/edit/{id}', [UserController::class, 'edit']);
});

Router::dispatch($_SERVER['REQUEST_METHOD'], $_SERVER['REQUEST_URI']);
