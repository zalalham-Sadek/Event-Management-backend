<?php
namespace App\Middleware;

use App\Core\loggers\LoggerFactory;
use App\Core\builder\QueryBuilder;
use App\Models\User;
class AdminMiddleware {
    public static function handle(): void {
        // Build the query with QueryBuilder
        $query = QueryBuilder::table("users")
            ->select("users.id")
            ->join("user_roles", "users.id", "=", "user_roles.user_id")
            ->join("roles", "roles.id", "=", "user_roles.role_id")
            ->where("roles.name", "=", "admin")
            ->limit(1);

        // Prepare & execute
        $admin = User::isAdmin($query);

        // Handle redirect if no admin
        if (!$admin && $_SERVER['REQUEST_URI'] !== '/setup-admin') {
            LoggerFactory::create('file') // أو 'db' حسب اختيارك
                ->warning("No admin found, redirecting to setup.");

            header("Location: /setup-admin");
            exit;
        }
    }
}
