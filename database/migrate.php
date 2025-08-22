<?php
require __DIR__ . '/../vendor/autoload.php';
use App\Core\Database;

$dotenv = Dotenv\Dotenv::createImmutable(__DIR__ . '/../');
$dotenv->load();

$db = null;
if (!$db) {
    $config = [
        'host' => $_ENV['DB_HOST'],
        'name' => $_ENV['DB_DATABASE'],
        'user' => $_ENV['DB_USERNAME'],
        'pass' => $_ENV['DB_PASSWORD'],
        'charset' => $_ENV['DB_CHARSET']
    ];
    $db = Database::getInstance($config);
}

$migrationsDir = __DIR__ . '/migrations';
$files = scandir($migrationsDir);
$executed = 0;

$rollback = $argv[1] ?? null;

foreach ($files as $file) {
    if (pathinfo($file, PATHINFO_EXTENSION) === 'php') {
        require_once $migrationsDir . '/' . $file;

       $filename = pathinfo($file, PATHINFO_FILENAME); // 001_CreateUsersTable
        $className = preg_replace('/^\d+_/', '', $filename); // يزيل 001_
        $fullClassName = "App\\Migrations\\$className";

        if (class_exists($fullClassName)) {
            $migration = new $fullClassName();

            if ($rollback === 'rollback') {
                $migration->down($db);
                echo "Rolled back: $className\n";
            } else {
                $migration->up($db);
                echo "Migrated: $className\n";
            }

            $executed++;
        }
    }
}

if ($executed === 0) {
    echo $rollback === 'rollback' ? "No migrations to rollback.\n" : "No migrations found.\n";
} else {
    echo $rollback === 'rollback' ? "All rollbacks executed successfully.\n" : "All migrations executed successfully.\n";
}
