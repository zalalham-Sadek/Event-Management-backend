<?php
namespace App\Controllers;

use App\Core\Controller;
use App\Models\User;
use App\Models\Role;
use App\Models\UserRole;
use App\Core\loggers\FileLogger;
use App\Core\loggers\LoggerFactory;

class AdminController extends Controller {
    public function setupAdmin() {
        // Prepare & execute
        $admin = User::isAdmin();
        if ($admin) {
            // إذا كان هناك أدمن، إعادة التوجيه إلى الصفحة الرئيسية
            header("Location: /login");
            exit;
        }
        return $this->view('setup/setup');
    }
    public function createAdmin(){
            $logger = \App\Core\loggers\LoggerFactory::create('file');

        $name = $_POST['name'] ?? '';
        $email = $_POST['email'] ?? '';
        $password = $_POST['password'] ?? '';
        $confirm_password = $_POST['confirm_password'] ?? '';

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

        // انشاء المستخدم
        $hashed = password_hash($password, PASSWORD_DEFAULT);
        $userId = User::create([
            'name' => $name,
            'email' => $email,
            'password' => $hashed
        ]);

        // اعطاءه صلاحية أدمن
        $role = Role::findByName('admin'); // تأكد من أن لديك Role::findByName
        if (!$role) {
            $logger->error("Role 'admin' not found");
            return ['error' => 'Admin role not found'];
        }
        // ربط المستخدم بالدور
        $userRole = UserRole::create([
            'user_id' => $userId,
            'role_id' => $role['id']
        ]);
        // بعد الإنشاء، تحويله لتسجيل الدخول
        header("Location: /login");
        exit;
    }
    
    // API Methods for Admin Operations
    
    public function apiSetupAdmin() {
        $admin = User::isAdmin();
        if ($admin) {
            return $this->json(['error' => 'Admin already exists','hasAdmin'=>true], 409);
        }
        return $this->json(['message' => 'You can setup admin now','hasAdmin'=>false]);
    }
    
    public function apiCreateAdmin() {

        $data = json_decode(file_get_contents('php://input'), true);

        $logger = LoggerFactory::create('file');
        
        // Check if user is admin
        $logger->info("API admin setup attempt with data: " . json_encode(array_merge($data ?? [], ['password' => '***'])));

        if (!$data || !isset($data['name']) || !isset($data['email']) || !isset($data['password'])) {
            $logger->error("API Registration failed: Missing required fields");
            return $this->json(['error' => 'Name, email and password are required'], 400);
        }
        
        $name = $data['name'];
        $email = $data['email'];
        $password = $data['password'];
        $role = $data['role'] ?? 'admin';
        
        // Check if email already exists
        if (User::findByEmail($email)) {
            return $this->json(['error' => 'Email already exists'], 409);
        }
        
        try {
            $userId = User::create([
                'name' => $name,
                'email' => $email,
                'password' => password_hash($password, PASSWORD_BCRYPT)
            ]);
            
            $role = Role::findByName($role);
            if ($role) {
                UserRole::create([
                    'user_id' => $userId,
                    'role_id' => $role['id']
                ]);
            }
            
            $user = User::find($userId);
            $logger->info("Admin created user: $email with ID $userId");
            
            return $this->json([
                'message' => 'User created successfully',
                'user' => $user
            ], 201);
        } catch (\Exception $e) {
            $logger->error("Admin failed to create user: " . $e->getMessage());
            return $this->json(['error' => 'Failed to create user'], 500);
        }
    }
     
}