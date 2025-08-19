<?php
namespace App\Core\loggers;

interface Logger {
    public function log(string $message): void;
}
