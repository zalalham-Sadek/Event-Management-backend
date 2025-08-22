<?php
namespace App\Core\loggers;

class FileLogger implements Logger {
    private $file;
    private $ignore = ['Connected to database successfully!'];

    public function __construct(string $file = 'logs/app.log') {
        $this->file = $file;
        if (!file_exists(dirname($this->file))) mkdir(dirname($this->file), 0777, true);
    }

    public function log(string $message, string $level = 'INFO'): void {
        foreach ($this->ignore as $ignoreMessage) {
            if (str_contains($message, $ignoreMessage)) return;
        }

        $date = date('Y-m-d H:i:s');
        file_put_contents($this->file, "[$date][$level] $message" . PHP_EOL, FILE_APPEND);
    }

    public function info(string $message) {
        $this->log($message, 'INFO');
    }

    public function warning(string $message) {
        $this->log($message, 'WARNING');
    }

    public function error(string $message) {
        $this->log($message, 'ERROR');
    }
}
