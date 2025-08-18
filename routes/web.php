<?php
use App\Core\Router;
use App\Controllers\UserController;
use App\Controllers\AuthController;


Router::get('/users', [ UserController::class, 'index']);
Router::get('/users/edit/{id}', [ UserController::class, 'edit']);
Router::get('/register', [ AuthController::class, 'registerForm']);
Router::post('/register', [ AuthController::class, 'register']);
Router::get('/login', [ AuthController::class, 'loginForm']);
Router::post('/login', [ AuthController::class, 'login']);
Router::get('/logout', [ AuthController::class, 'logout']);


Router::dispatch($_SERVER['REQUEST_METHOD'], $_SERVER['REQUEST_URI']);
