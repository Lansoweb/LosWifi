<?php
namespace LosWifi\Options;

use Zend\Stdlib\AbstractOptions;

final class ModuleOptions extends AbstractOptions
{

    private $debug = false;

    private $controllers = [];

    public function getDebug()
    {
        return $this->debug;
    }

    public function setDebug($debug)
    {
        $this->debug = (bool) $debug;

        return $this;
    }

    public function getControllers()
    {
        return $this->controllers;
    }

    public function setControllers($controllers)
    {
        $this->controllers = $controllers;
        return $this;
    }

    public function getController($slug)
    {
        foreach ($this->controllers as $controller) {
            if (array_key_exists('slug', $controller) && $controller['slug'] == $slug) {
                return $controller;
            }
        }
        return false;
    }
}
