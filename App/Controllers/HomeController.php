<?php
namespace App\Controllers;

class HomeController
{
    public function index()
    {

        if (isset($_SESSION['user'])) {
            header("Location: /users");
            exit;
        } 
            header("Location: /login");
            exit;
        
    }
}
