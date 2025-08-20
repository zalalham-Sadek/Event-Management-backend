<?php
namespace App\Core;

class Controller {

    protected function view(string $view, array $data = [], string $layout = 'app') {
        $logger = \App\Core\loggers\LoggerFactory::create('file');
        // تحميل محتوى الـ view
        $viewPath = __DIR__ . "/../Views/{$view}.php";
        if (!file_exists($viewPath)) {
            $logger->error("View {$view} not found!");
            return ['error' => "View {$view} not found!"];
            // throw new \Exception("View {$view} not found!");
        }

        // اجعل المتغيرات متاحة في الـ view
        // مثال: compact('users', 'posts') => ['users'=>..., 'posts'=>...]
        extract($data);

        // تخزين محتوى الـ view
        ob_start();
        require $viewPath;
        $content = ob_get_clean();

        // تحميل الـ layout مع محتوى الـ view
        $layoutPath = __DIR__ . "/../Views/layouts/{$layout}.php";
        if (!file_exists($layoutPath)) {
            throw new \Exception("Layout {$layout} not found!");
        }

        require $layoutPath;
    }

    protected function redirect(string $to) {
        header("Location: {$to}");
        exit;
    }
}
