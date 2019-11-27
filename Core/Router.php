<?php

namespace Core;

use App\Config;

class Router
{
    protected $routes = [
        '^/$' => [
            'controller' => 'Tasks',
            'action' => 'index'
        ],
        '^/tasks/index$' => [
            'controller' => 'Tasks',
            'action' => 'index'
        ],
        '^/admin/login$' => [
            'controller' => 'Admins',
            'action' => 'login'
        ],
        '^/admin/logout$' => [
            'controller' => 'Admins',
            'action' => 'logout'
        ],
        '^/tasks/add$' => [
            'controller' => 'Tasks',
            'action' => 'add'
        ],
        '^/tasks/edit/(?P<id>\d+)$' => [
            'controller' => 'Tasks',
            'action' => 'edit'
        ],
    ];

    protected $params = [];

    public function match($url)
    {
        foreach ($this->routes as $route => $params) {
            $reg_exp = '#' . $route . '#siU';
            if (preg_match($reg_exp, $url, $matches)) {
                foreach ($matches as $key => $match) {
                    if (is_string($key)) {
                        $params[$key] = $match;
                    }
                }
                $this->params = $params;
                return true;
            }
        }
    }

    public function dispatch($url)
    {
        $url = $this->removeQueryString($url);
        if ($this->match($url)) {
            $controller = $this->params['controller'];
            $controller = $this->convertToStudlyCaps($controller);
            $controller = "App\Controllers\\".$controller;
            if (class_exists($controller)) {
                $controller_object = new $controller($this->params);
                $action = $this->params['action'];
                $action = $this->convertToCamelCase($action);
                if (is_callable([$controller_object, $action])) {
                    $controller_object->$action();
                } else {
                    echo "Method '{$action}'' in class '{$controller}' not found";
                }
            } else {
                echo "Class '{$controller}' not found";
            }
        } else {
            echo "No route found";
        }
        return false;
    }

    protected function convertToStudlyCaps($str)
    {
        return str_replace(' ', '-', ucwords(str_replace('-', ' ', $str)));
    }

    protected function convertToCamelCase($str)
    {
        return str_replace('-', '', lcfirst($this->convertToStudlyCaps($str)));
    }

    protected function removeQueryString($url)
    {
        return preg_replace('#\?.*$#siU', '', $url);
    }

}