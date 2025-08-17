<?php
namespace App\Core;

class Controller {
    protected $db; // الاتصال بقاعدة البيانات

    // public function __construct() {
    //     $config = require __DIR__ . '/../../config.php'; // تحميل الإعدادات مرة واحدة
    //     $this->db = Database::getInstance($config); // اتصال Singleton
    // }

    protected function view($view, $data = []) {
        extract($data);
        ob_start();
        require __DIR__ . "/../Views/{$view}.php";
            $content = ob_get_clean();
        require __DIR__ . "/../Views/layouts/app.php";

    }
}
