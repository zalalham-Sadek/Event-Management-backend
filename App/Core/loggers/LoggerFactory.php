<?php
namespace App\Core\loggers;
use InvalidArgumentException;
class LoggerFactory {
    public static function create(string $type): Logger {
        return match(strtolower($type)) {
            'file' => new FileLogger(),
            'db' => new DatabaseLogger(),
            default => throw new InvalidArgumentException("Unknown logger type: $type"),
        };
    }
}
