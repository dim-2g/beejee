<?php

namespace Core;

use Core\Request;

abstract class Controller
{
    /**
     * Parameters from the matched route
     * @var array
     */
    protected $params = [];

    public function __construct($route_params)
    {
        $this->params = $route_params;
        $this->request = new Request();
    }

    public function getParam($name)
    {
        if (array_key_exists($name, $this->params)) {
            return $this->params[$name];
        } else {
            return null;
        }
    }
}