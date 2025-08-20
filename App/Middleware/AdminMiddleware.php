<?php
namespace App\Middleware;

use App\Core\loggers\LoggerFactory;
use App\Core\builder\QueryBuilder;
use App\Models\User;
class AdminMiddleware {
    public static function handle(): void {
        // Prepare & execute
        $admin = User::isAdmin();

        // Handle redirect if no admin
        if (!$admin && $_SERVER['REQUEST_URI'] !== '/setup-admin') {
            LoggerFactory::create('file') // أو 'db' حسب اختيارك
                ->warning("No admin found, redirecting to setup.");

            header("Location: /setup-admin");
            exit;
        }
    }
}
