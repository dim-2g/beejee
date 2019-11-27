<?php

namespace Core;

class Utils
{

    public static function redirect($url)
    {
        header("Location: {$url}");
        exit();
    }

    public static function debug($arr)
    {
        echo '<pre>';
        var_dump($arr);
        echo '</pre>';
    }

    public static function isAdmin()
    {
        return empty($_SESSION['isAdmin']) ? false : $_SESSION['isAdmin'];
    }

}