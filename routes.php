<?php
use App\Controllers\UserController;

$router->get('PHP-Project/users', [new UserController(), 'index']);
$router->get('PHP-Project/users/edit', function() {
    $id = $_GET['id'] ?? null;
    (new UserController())->edit($id);
});
