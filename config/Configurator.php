<?php

class Configurator
{
    private $config;

    public function __construct()
    {
        $this->config = parse_ini_file("config/config.ini");
    }

    public function getConfig()
    {
        return $this->config;
    }

    public function getHomeController()
    {
        return new HomeController(new Request(), $this->getRenderer(), $this->getProductoModel(), $this->getCategoriaModel());
    }

    public function getProductoController()
    {
        return new ProductoController(new Request(), $this->getRenderer(), $this->getProductoModel(), $this->getCategoriaModel());
    }

    public function getUsuarioController()
    {
        return new UsuarioController(new Request(), $this->getRenderer(), $this->getUsuarioModel(), $this->getCategoriaModel());
    }

    public function getCarritoController()
    {
        return new CarritoController(new Request(), $this->getRenderer(), $this->getCarritoModel(), $this->getCarritoProductoModel(), $this->getUsuarioModel(), $this->getProductoModel(), $this->getCategoriaModel());
    }

    public function getRouter()
    {
        return new Router($this, 'home', 'index');
    }

    public function getOrDefault($controllerName, $defaultControllerName)
    {
        $getter = 'get' . ucfirst($controllerName) . 'Controller';
        if (method_exists($this, $getter)) {
            return $this->{$getter}();
        }

        $defaultGetter = 'get' . ucfirst($defaultControllerName) . 'Controller';
        return $this->{$defaultGetter}();
    }

// METODOS PRIVADOS
    private function getDatabase()
    {
        return new MyDatabase(
            $this->config['hostname'],
            $this->config['username'],
            $this->config['password'],
            $this->config['database']
        );
    }

    private function getRenderer()
    {
        return new MustacheRenderer(__DIR__ . '/../view', $this->getDatabase());
    }

    private function getCategoriaModel()
    {
        return new CategoriaModel($this->getDatabase());
    }

    private function getProductoModel()
    {
        return new ProductoModel($this->getDatabase());
    }

    private function getUsuarioModel()
    {
        return new UsuarioModel($this->getDatabase());
    }

    private function getCarritoModel()
    {
        return new CarritoModel($this->getDatabase());
    }

    private function getCarritoProductoModel()
    {
        return new CarritoProductoModel($this->getDatabase());
    }
}