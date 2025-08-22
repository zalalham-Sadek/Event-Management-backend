<?php
namespace App\Controllers;

use App\Core\Controller;
use App\Models\User;
use App\Models\Role;
use App\Models\UserRole;
use App\Core\loggers\LoggerFactory;

class AuthController extends Controller {
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
    
    // API Authentication Methods
    public function apiLogin() {
        // Get JSON data from request
        $data = json_decode(file_get_contents('php://input'), true);
        $logger = LoggerFactory::create('file');
        
        if (!$data || !isset($data['email']) || !isset($data['password'])) {
            return $this->json(['error' => 'Email and password are required'], 400);
        }
        
        $email = $data['email'];
        $password = $data['password'];
        
        $user = User::findByEmail($email);
        
        if (!$user || !password_verify($password, $user['password'])) {
            $logger->warning("API login failed for email: $email");
            return $this->json(['error' => 'Invalid credentials'], 401);
        }
        
        // Create a session for API users too
        if (session_status() === PHP_SESSION_NONE) {
            session_start();
        }
        
        $_SESSION['user'] = [
            'id' => $user['id'],
            'name' => $user['name'],
            'role' => User::getRoles($user['id'])
        ];
        
        $logger->info("API login successful for user ID: {$user['id']}, Email: $email");
        
        return $this->json([
            'message' => 'Login successful',
            'user' => [
                'id' => $user['id'],
                'name' => $user['name'],
                'email' => $user['email'],
                'roles' => User::getRoles($user['id'])
            ]
        ]);
    }
    
    public function apiRegister() {
        // Get JSON data from request
        $data = json_decode(file_get_contents('php://input'), true);
        $logger = LoggerFactory::create('file');
        
        // Debug: Log the incoming data
        $logger->info("API Registration attempt with data: " . json_encode(array_merge($data ?? [], ['password' => '***'])));
        
        if (!$data || !isset($data['name']) || !isset($data['email']) || !isset($data['password'])) {
            $logger->error("API Registration failed: Missing required fields");
            return $this->json(['error' => 'Name, email and password are required'], 400);
        }
        
        $name = $data['name'];
        $email = $data['email'];
        $password = $data['password'];
        
        // Check if email already exists
        if (User::findByEmail($email)) {
            $logger->error("API registration failed: Email $email already exists");
            return $this->json(['error' => 'Email already exists'], 409);
        }
        
        try {
            $logger->info("Creating user with email: $email");
            
            $userId = User::create([
                'name' => $name,
                'email' => $email,
                'password' => password_hash($password, PASSWORD_BCRYPT)
            ]);
            
            $logger->info("User created with ID: $userId");
            
            $role = Role::findByName('user');
            
            if ($role) {
                UserRole::create([
                    'user_id' => $userId,
                    'role_id' => $role['id']
                ]);
                $logger->info("User role assigned: user_id=$userId, role_id={$role['id']}");
            } else {
                $logger->warning("Default 'user' role not found");
            }
            
            $user = User::find($userId);
            
            // Create a session for the new user
            if (session_status() === PHP_SESSION_NONE) {
                session_start();
            }
            
            $_SESSION['user'] = [
                'id' => $user['id'],
                'name' => $user['name'],
                'role' => User::getRoles($user['id'])
            ];
            
            $logger->info("API registration successful for user ID: $userId, Email: $email");
            
            return $this->json([
                'message' => 'Registration successful',
                'user' => [
                    'id' => $user['id'],
                    'name' => $user['name'],
                    'email' => $user['email'],
                    'roles' => User::getRoles($user['id'])
                ]
            ], 201);
        } catch (\Exception $e) {
            $logger->error("API registration failed for $email: " . $e->getMessage());
            return $this->json(['error' => 'Registration failed: ' . $e->getMessage()], 500);
        }
    }
    
    public function apiMe() {
        if (session_status() === PHP_SESSION_NONE) {
            session_start();
        }
        
        if (!isset($_SESSION['user'])) {
            return $this->json(['error' => 'Not authenticated'], 401);
        }
        
        $user = User::find($_SESSION['user']['id']);
        if (!$user) {
            return $this->json(['error' => 'User not found'], 404);
        }
        
        return $this->json([
            'user' => [
                'id' => $user['id'],
                'name' => $user['name'],
                'email' => $user['email'],
                'roles' => User::getRoles($user['id'])
            ]
        ]);
    }
    
    public function apiLogout() {
        if (session_status() === PHP_SESSION_NONE) {
            session_start();
        }
        
        session_destroy();
        
        return $this->json(['message' => 'Logged out successfully']);
    }
    
    public function apiUpdateProfile() {
        if (session_status() === PHP_SESSION_NONE) {
            session_start();
        }
        
        if (!isset($_SESSION['user'])) {
            return $this->json(['error' => 'Not authenticated'], 401);
        }
        
        $data = json_decode(file_get_contents('php://input'), true);
        $logger = LoggerFactory::create('file');
        
        if (!$data) {
            return $this->json(['error' => 'Invalid JSON data'], 400);
        }
        
        $userId = $_SESSION['user']['id'];
        $updateData = [];
        
        if (isset($data['name'])) {
            $updateData['name'] = $data['name'];
        }
        
        if (isset($data['email'])) {
            // Check if new email already exists for another user
            $existingUser = User::findByEmail($data['email']);
            if ($existingUser && $existingUser['id'] != $userId) {
                return $this->json(['error' => 'Email already in use'], 409);
            }
            $updateData['email'] = $data['email'];
        }
        
        if (empty($updateData)) {
            return $this->json(['error' => 'No fields to update'], 400);
        }
        
        try {
            User::update($userId, $updateData);
            $updatedUser = User::find($userId);
            
            // Update session
            $_SESSION['user']['name'] = $updatedUser['name'];
            
            $logger->info("User updated profile: $userId");
            
            return $this->json([
                'message' => 'Profile updated successfully',
                'user' => [
                    'id' => $updatedUser['id'],
                    'name' => $updatedUser['name'],
                    'email' => $updatedUser['email'],
                    'roles' => User::getRoles($updatedUser['id'])
                ]
            ]);
        } catch (\Exception $e) {
            $logger->error("Failed to update profile for user $userId: " . $e->getMessage());
            return $this->json(['error' => 'Failed to update profile'], 500);
        }
    }
    
    public function apiUpdatePassword() {
        if (session_status() === PHP_SESSION_NONE) {
            session_start();
        }
        
        if (!isset($_SESSION['user'])) {
            return $this->json(['error' => 'Not authenticated'], 401);
        }
        
        $data = json_decode(file_get_contents('php://input'), true);
        $logger = LoggerFactory::create('file');
        
        if (!$data || !isset($data['current_password']) || !isset($data['new_password'])) {
            return $this->json(['error' => 'Current password and new password are required'], 400);
        }
        
        $userId = $_SESSION['user']['id'];
        $user = User::find($userId);
        
        if (!password_verify($data['current_password'], $user['password'])) {
            return $this->json(['error' => 'Current password is incorrect'], 401);
        }
        
        try {
            User::update($userId, [
                'password' => password_hash($data['new_password'], PASSWORD_BCRYPT)
            ]);
            
            $logger->info("User changed password: $userId");
            
            return $this->json(['message' => 'Password updated successfully']);
        } catch (\Exception $e) {
            $logger->error("Failed to update password for user $userId: " . $e->getMessage());
            return $this->json(['error' => 'Failed to update password'], 500);
        }
    }
}
