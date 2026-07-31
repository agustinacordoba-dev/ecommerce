<?php

class HomeController
{
    private $request;
    private $renderer;
    private $productoModel;
    private $categoriaModel;

    public function __construct($request, $renderer, $productoModel, $categoriaModel) {
        $this->request = $request;
        $this->renderer = $renderer;
        $this->productoModel = $productoModel;
        $this->categoriaModel = $categoriaModel;
    }

    public function index() {
        $productos  = $this->productoModel->obtenerProductos();
        $categorias = $this->categoriaModel->obtenerCategorias();

        $this->renderer->render("home", ["productos" => $productos, "categorias" => $categorias]);
    }

    public function buscadorAjax()
    {
        $busqueda = isset($_GET["buscar"]) ? trim($_GET["buscar"]) : "";
        $productos = [];

        if (strlen($busqueda) >= 3)
            $productos = $this->productoModel->buscarProducto($busqueda);

        header('Content-type: application/json');
        echo json_encode($productos);
        exit();
    }
}