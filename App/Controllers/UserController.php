<?php
namespace App\Controllers;

use App\Core\Controller;
use App\Models\User;

class UserController extends Controller {
    // Web routes
    public function index() {
        $users = User::all();
        $this->view('user_list', compact('users'));
    }

    public function create() {
        // Implementation for web route
    }

    public function edit($id) {
        $user = User::find($id);
        $this->view('edit_user', compact('user'));
    }

    // API routes
    public function getAllUsers() {
        $users = User::all();
        return $this->json(['users' => $users]);
    }

    public function getUser($id) {
        $user = User::find($id);
        if (!$user) {
            return $this->json(['error' => 'User not found'], 404);
        }
        return $this->json(['user' => $user]);
    }

    public function createUser() {
        // Get JSON data from request
        $data = json_decode(file_get_contents('php://input'), true);
        
        if (!$data || !isset($data['name']) || !isset($data['email']) || !isset($data['password'])) {
            return $this->json(['error' => 'Missing required fields'], 400);
        }

        // Check if email already exists
        if (User::findByEmail($data['email'])) {
            return $this->json(['error' => 'Email already exists'], 409);
        }

        try {
            $userId = User::create([
                'name' => $data['name'],
                'email' => $data['email'],
                'password' => password_hash($data['password'], PASSWORD_BCRYPT)
            ]);

            $user = User::find($userId);
            return $this->json(['message' => 'User created successfully', 'user' => $user], 201);
        } catch (\Exception $e) {
            return $this->json(['error' => 'Failed to create user: ' . $e->getMessage()], 500);
        }
    }

    // public function updateUser($id) {
    //     $user = User::find($id);
    //     if (!$user) {
    //         return $this->json(['error' => 'User not found'], 404);
    //     }

    //     $data = json_decode(file_get_contents('php://input'), true);
    //     if (!$data) {
    //         return $this->json(['error' => 'Invalid JSON data'], 400);
    //     }

    //     $updateData = [];
    //     if (isset($data['name'])) $updateData['name'] = $data['name'];
    //     if (isset($data['email'])) {
    //         // Check if new email already exists for another user
    //         $existingUser = User::findByEmail($data['email']);
    //         if ($existingUser && $existingUser['id'] != $id) {
    //             return $this->json(['error' => 'Email already in use'], 409);
    //         }
    //         $updateData['email'] = $data['email'];
    //     }
    //     if (isset($data['password'])) {
    //         $updateData['password'] = password_hash($data['password'], PASSWORD_BCRYPT);
    //     }

    //     if (empty($updateData)) {
    //         return $this->json(['error' => 'No fields to update'], 400);
    //     }

    //     try {
    //         User::update($id, $updateData);
    //         $updatedUser = User::find($id);
    //         return $this->json(['message' => 'User updated successfully', 'user' => $updatedUser]);
    //     } catch (\Exception $e) {
    //         return $this->json(['error' => 'Failed to update user: ' . $e->getMessage()], 500);
    //     }
    // }

    // public function deleteUser($id) {
    //     $user = User::find($id);
    //     if (!$user) {
    //         return $this->json(['error' => 'User not found'], 404);
    //     }

    //     try {
    //         User::delete($id);
    //         return $this->json(['message' => 'User deleted successfully']);
    //     } catch (\Exception $e) {
    //         return $this->json(['error' => 'Failed to delete user: ' . $e->getMessage()], 500);
    //     }
    // }
}
