<?php
use App\Core\Router;
use App\Controllers\UserController;


Router::get('/users', [ UserController::class, 'index']);
Router::get('/users/edit/{id}', [ UserController::class, 'edit']);


Router::dispatch($_SERVER['REQUEST_METHOD'], $_SERVER['REQUEST_URI']);
