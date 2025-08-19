<?php
namespace App\Controllers;

class HomeController
{
    public function index()
    {
        session_start(); 

        if (isset($_SESSION['user_id'])) {
            header("Location: /users");
            exit;
        } else {
            header("Location: /login");
            exit;
        }
    }
}
