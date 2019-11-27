<?php

ini_set("error_reporting", E_ALL);
ini_set("display_errors", 1);

require_once __DIR__ . '/vendor/autoload.php';

spl_autoload_register(function ($class) {
    $root = __DIR__;

    $class_path = str_replace('\\', '/', $class);
    $file = "{$root}/{$class_path}.php";
    if (is_readable($file)) {
        require $file;
    }
});

session_start();
$url = $_SERVER['REQUEST_URI'];
$router = new Core\Router();
echo $router->dispatch($url);