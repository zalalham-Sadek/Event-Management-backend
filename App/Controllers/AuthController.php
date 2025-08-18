<?php
namespace App\Controllers;

use App\Core\Controller;
use App\Models\User;
use App\Models\Role;
use App\Models\UserRole;

class AuthController extends Controller{
    public function registerForm(){
        require __DIR__ . "/../Views/auth/register.php";
    }

public static function register() {
    $name = $_POST['name'] ?? null;
    $email = $_POST['email'] ?? null;
    $password = $_POST['password'] ?? null;
    $confirm_password = $_POST['confirm_password'] ?? null;

    if (!$name || !$email || !$password || !$confirm_password) {
        return ['error' => 'All fields are required'];
    }

    if ($password !== $confirm_password) {
        return ['error' => 'Passwords do not match'];
    }

    if (User::findByEmail($email)) {
        return ['error' => 'Email already exists'];
    }

    $userId = User::create([
        'name' => $name,
        'email' => $email,
        'password' => password_hash($password, PASSWORD_BCRYPT)
    ]);

    $role = Role::findByName('user');

    if ($role) {
       $user_roleID = UserRole::create([
            'user_id'=> $userId,
            'role_id'=> $role['id']
        ]); 
        
        
    }
    session_start();
    $_SESSION['user_id'] = $userId;
    $_SESSION['roles'] = User::getRoles($userId);
    header("Location: /users");
    exit;
}

    public function  loginForm(){
                require __DIR__ . "/../Views/auth/login.php";
    }

    public static function login() {
    $email = $_POST['email'] ?? null;
    $password = $_POST['password'] ?? null;

        if ( !$email || !$password ) {
        return ['error' => 'All fields are required'];
    }
        $user = User::findByEmail($email);
        if (!$user || !password_verify($password, $user['password'])) {
            return ['error' => 'Invalid credentials'];
        }

        session_start();
        $_SESSION['user_id'] = $user['id'];
        $_SESSION['roles'] = User::getRoles($user['id']);

    header("Location: /users");
    exit;    }

    public static function logout() {
        session_start();
        session_destroy();
  header("Location: /login");
    exit;
    }

    public static function checkRole($role) {
        session_start();
        return in_array($role, $_SESSION['roles'] ?? []);
    }
}
