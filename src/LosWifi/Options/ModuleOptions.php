<?php
namespace LosWifi\Options;

use Zend\Stdlib\AbstractOptions;

final class ModuleOptions extends AbstractOptions
{

    private $backend = 'unifi';

    private $username = '';

    private $password = '';

    private $baseUrl = 'https://127.0.0.1:8443';

    private $site = 'default';

    private $controller = '4.0.0';

    private $debug = false;

    public function getBackend()
    {
        return $this->backend;
    }

    public function setBackend($backend)
    {
        if ($backend !== 'unifi') {
            throw new \InvalidArgumentException("Invalid type '$backend' specified. Must be 'unifi'.");
        }
        $this->backend = $backend;
    }

    public function getUsername()
    {
        return $this->username;
    }

    public function setUsername($username)
    {
        $this->username = (string) $username;

        return $this;
    }

    public function getPassword()
    {
        return $this->password;
    }

    public function setPassword($password)
    {
        $this->password = (string) $password;

        return $this;
    }

    public function getBaseUrl()
    {
        return $this->baseUrl;
    }

    public function setBaseUrl($baseUrl)
    {
        $this->baseUrl = (string) $baseUrl;

        return $this;
    }

    public function getSite()
    {
        return $this->site;
    }

    public function setSite($site)
    {
        $this->site = (string) $site;

        return $this;
    }

    public function getController()
    {
        return $this->controller;
    }

    public function setController($controller)
    {
        $this->controller = (string) $controller;

        return $this;
    }

    public function getDebug()
    {
        return $this->debug;
    }

    public function setDebug($debug)
    {
        $this->debug = (bool) $debug;

        return $this;
    }
}
