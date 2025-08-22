<?php
namespace App\Core\loggers;

interface Logger {
    public function log(string $message, string $level = 'INFO'): void;
    public function info(string $message);
    public function warning(string $message);
    public function error(string $message);
}
