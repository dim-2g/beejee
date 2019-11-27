<?php

namespace Core;

class View
{

    public static function render($view, $args = [])
    {
        extract($args, EXTR_SKIP);
        $file = 'App/Views/'.$view;

        if (is_readable($file)) {
            include $file;
        } else {
            echo "file $file not found";
        }
    }

    public static function renderTemplate($template, $args = [])
    {
        static $twig = null;

        if ($twig === null) {
            $loader = new \Twig_Loader_Filesystem('App/Views');
            $twig = new \Twig_Environment($loader);
        }

        echo $twig->render($template, $args);
    }

    public static function getTemplate($template, $args = [])
    {
        static $twig = null;

        if ($twig === null) {
            $loader = new \Twig_Loader_Filesystem('App/Views');
            $twig = new \Twig_Environment($loader);
        }

        return $twig->render($template, $args);
    }
}