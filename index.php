<?php
require __DIR__ . '/vendor/autoload.php';
$dotenv = Dotenv\Dotenv::createImmutable(__DIR__ );
$dotenv->load();

\App\Middleware\AdminMiddleware::handle();


require __DIR__ . '/routes/web.php';



