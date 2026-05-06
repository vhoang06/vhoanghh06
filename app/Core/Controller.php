<?php

namespace App\Core;

abstract class Controller
{
    /**
     * Render a view file from a module
     * 
     * @param string $module The module name (e.g. 'Order')
     * @param string $view The view file name (e.g. 'index')
     * @param array $data Data to extract for the view
     */
    protected function view($module, $view, $data = [])
    {
        extract($data);
        
        $viewPath = dirname(__DIR__) . "/Modules/{$module}/Views/{$view}.php";
        
        if (file_exists($viewPath)) {
            require_once $viewPath;
        } else {
            die("View {$view} not found in module {$module}");
        }
    }

    /**
     * Redirect to a specific route
     */
    protected function redirect($url)
    {
        header("Location: " . APP_URL . "/" . $url);
        exit;
    }
}
