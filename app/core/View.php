<?php
// FILE: /app/core/View.php

class View {
    private $viewsPath;
    private $layoutsPath;

    public function __construct() {
        $this->viewsPath = __DIR__ . '/../views/';
        $this->layoutsPath = __DIR__ . '/../views/layouts/';
    }

    public function render($viewPath, $data = [], $layout = 'main') {
        extract($data);

        ob_start();
        $viewFile = $this->viewsPath . $viewPath . '.php';

        if (!file_exists($viewFile)) {
            throw new Exception("View file not found: {$viewPath}");
        }

        require $viewFile;
        $content = ob_get_clean();

        if ($layout) {
            $layoutFile = $this->layoutsPath . $layout . '.php';
            if (file_exists($layoutFile)) {
                require $layoutFile;
            } else {
                echo $content;
            }
        } else {
            echo $content;
        }
    }

    public function renderPartial($viewPath, $data = []) {
        extract($data);
        $viewFile = $this->viewsPath . $viewPath . '.php';

        if (!file_exists($viewFile)) {
            throw new Exception("View file not found: {$viewPath}");
        }

        require $viewFile;
    }
}
