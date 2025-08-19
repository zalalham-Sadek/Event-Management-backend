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
}