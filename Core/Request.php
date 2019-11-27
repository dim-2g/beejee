<?php

namespace Core;


class Request
{

    public static function get()
    {
        return $_GET;
    }

    public static function post()
    {
        return $_POST;
    }

    public static function getPage()
    {
        $page = self::getValue('page');
        return (!empty($page)) ? (int)$page : 1;
    }

    public static function getValue($name)
    {
        $get = self::get();
        return (!empty($get[$name])) ? $get[$name] : null;
    }

}