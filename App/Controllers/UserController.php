<?php
namespace App\Controllers;

use App\Core\Controller;
use App\Models\User;

class UserController extends Controller {
    public function index() {
        // header('Content-Type: application/json');
        $users = User::all();
        
        $this->view('user_list',$users);
    }

    public function edit($id) {
        header('Content-Type: application/json');
        $user = User::find($id);
        echo json_encode($user);
    }
}
