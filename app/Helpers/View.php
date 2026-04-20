<?php

namespace App\Helpers;

class View
{
    public static function render(string $template, array $data = [], string $layout = 'main'): void
    {
        extract($data);

        ob_start();
        $templatePath = VIEWS_PATH . '/' . str_replace('.', '/', $template) . '.php';
        if (file_exists($templatePath)) {
            include $templatePath;
        } else {
            echo "View not found: $template";
        }
        $content = ob_get_clean();

        $layoutPath = VIEWS_PATH . '/layouts/' . $layout . '.php';
        if (file_exists($layoutPath)) {
            include $layoutPath;
        } else {
            echo $content;
        }
    }

    public static function partial(string $name, array $data = []): void
    {
        extract($data);
        $path = VIEWS_PATH . '/partials/' . $name . '.php';
        if (file_exists($path)) {
            include $path;
        }
    }
}
