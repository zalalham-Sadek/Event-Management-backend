<?php
namespace App\Core;

class Controller {

protected function view(string $view, array $data = [], string $layout = 'app') {
    $logger = \App\Core\loggers\LoggerFactory::create('file');
    // $viewPath = __DIR__ . "/../Views/{$view}.php";
    
    if (!file_exists(__DIR__ . "/../Views/{$view}.php")) {
        $logger->error("View {$view} not found!");
        return ['error' => "View {$view} not found!"];
    }

    // extract data for use in the view
    extract($data);

    // capture view content
    ob_start();
    require __DIR__ . "/../Views/{$view}.php";
    $content = ob_get_clean();

    // load layout
    // $layoutPath = __DIR__ . "/../Views/layouts/{$layout}.php";
    if (!file_exists(__DIR__ . "/../Views/layouts/{$layout}.php")) {
        throw new \Exception("Layout {$layout} not found!");
    }

    require __DIR__ . "/../Views/layouts/{$layout}.php";
}


    protected function redirect(string $to) {
        header("Location: {$to}");
        exit;
    }


        protected function json($data, int $statusCode = 200) {
        // Don't remove headers - preserve CORS headers set by middleware
        header("Content-Type: application/json; charset=UTF-8");
        http_response_code($statusCode);
        echo json_encode($data, JSON_UNESCAPED_UNICODE | JSON_PRETTY_PRINT);
        exit;
    }
}
