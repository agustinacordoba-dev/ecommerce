<?php
class Router
{
    private $config;
    private $defaultController;
    private $defaultMethod;
    public function __construct($config, $defaultController, $defaultMethod) {
    $this->config = $config;
    $this->defaultController = $defaultController;
    $this->defaultMethod = $defaultMethod;
    }

    public function dispatch($controllerName, $methodName)
    {
        $controller = $this->getController($controllerName);
        $method     = $this->getMethod($controller, $methodName);
        $controller->{$method}();
    }

    private function getController($controller)
    {
        return $this->config->getOrDefault($controller, $this->defaultController);
    }

    private function getMethod($controller, $method)
        {
        return method_exists($controller, $method) ? $method : $this->defaultMethod;
    }
}