<?php
namespace App\Controllers;

use App\Core\Controller;
use App\Models\User;
use App\Models\Role;
use App\Models\UserRole;
use App\Core\loggers\LoggerFactory;
class AuthController extends Controller{
    public function registerForm(){
        require __DIR__ . "/../Views/auth/register.php";
    }

    public static function register() {
        $logger = \App\Core\loggers\LoggerFactory::create('file');

        $name = $_POST['name'] ?? null;
        $email = $_POST['email'] ?? null;
        $password = $_POST['password'] ?? null;
        $confirm_password = $_POST['confirm_password'] ?? null;

        if (!$name || !$email || !$password || !$confirm_password) {
            $logger->error("Registration failed: Missing fields");
            return ['error' => 'All fields are required'];
        }

        if ($password !== $confirm_password) {
            $logger->error("Registration failed: Passwords do not match for email $email");
            return ['error' => 'Passwords do not match'];
        }

        if (User::findByEmail($email)) {
            $logger->error("Registration failed: Email $email already exists");
            return ['error' => 'Email already exists'];
        }

        try {
            $userId = User::create([
                'name' => $name,
                'email' => $email,
                'password' => password_hash($password, PASSWORD_BCRYPT)
            ]);

            $role = Role::findByName('user');

            if ($role) {
                UserRole::create([
                    'user_id'=> $userId,
                    'role_id'=> $role['id']
                ]); 
            }

            $user = User::find($userId);

            $_SESSION['user'] = [
                'id' => $user['id'],
                'name' => $user['name'],
                'role' => User::getRoles($user['id'])
            ];
            $logger->log("User registered successfully: $email with ID $userId");
            header("Location: /");
            exit;

        } catch (\Exception $e) {
            $logger->error("Registration failed for $email: " . $e->getMessage());
            return ['error' => 'Registration failed. Please try again.'];
        }
    }


    public function  loginForm(){

        if (isset($_SESSION['user'])) {
            // لو مسجل دخول بالفعل → ما يروح لتسجيل الدخول
            header("Location: /users");
            exit;
        }
            require __DIR__ . "/../Views/auth/login.php";
    }

    public static function login() {
        $email = $_POST['email'] ?? null;
        $password = $_POST['password'] ?? null;

        // إنشاء Logger مرة واحدة
        $logger = LoggerFactory::create('file');

        if (!$email || !$password) {
            $logger->warning("Login attempt with missing fields. Email: " . ($email ?? 'NULL'));
            return ['error' => 'All fields are required'];
        }

        $user = User::findByEmail($email);

        if (!$user || !password_verify($password, $user['password'])) {
            $logger->warning("Failed login attempt for email: $email");
            return ['error' => 'Invalid credentials'];
        }

        session_start();
        $_SESSION['user'] = [
                'id' => $user['id'],
                'name' => $user['name'],
                'role' => User::getRoles($user['id'])
            ];

        $logger->info("User logged in successfully. User ID: {$user['id']}, Email: $email");

        header("Location: /");
        exit;
    }
    
    public static function logout() {
        session_destroy();
        
        header("Location: /login");
        exit;
    }

    public static function checkRole($role) {
        session_start();
        return in_array($role, $_SESSION['user']['role'] ?? []);
    }
}
