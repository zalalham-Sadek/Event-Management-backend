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

// تحقق من الوسيط في سطر الأوامر
$rollback = $argv[1] ?? null;

foreach ($files as $file) {
    if (pathinfo($file, PATHINFO_EXTENSION) === 'php') {
        require_once $migrationsDir . '/' . $file;
        $className = str_replace('_', '', ucwords(pathinfo($file, PATHINFO_FILENAME), '_'));
        
        if (class_exists($className)) {
            $migration = new $className();

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
