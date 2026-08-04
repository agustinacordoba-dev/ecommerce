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
        $categorias = $this->categoriaModel->obtenerCategorias();
        $productos = $this->eliminarProductoEnOfertasDeGeneral($this->productoModel->obtenerProductos(), $this->productoModel->ofertas());

        $this->renderer->render("home", ["productos" => $productos, "categorias" => $categorias]);
    }

    public function buscadorAjax()
    {
        $busqueda  = isset($_GET["buscar"]) ? trim($_GET["buscar"]) : "";
        $productos = [];
        $estaEnOff = false;

        if (strlen($busqueda) >= 3)
            $productos = $this->obtenerProductosModificadosParaSaberSiEstaEnOferta($this->productoModel->buscarProducto($busqueda));

        header('Content-type: application/json');
        echo json_encode($productos);
        exit();
    }

// funciones privadas
    private function eliminarProductoEnOfertasDeGeneral($productos, $ofertas)
    {
        $idOfertas = array_column($ofertas, "producto_id");

        foreach ($productos as $i => $producto)
            if (in_array($producto["id"], $idOfertas))
                unset($productos[$i]);

        return array_values($productos);
    }

    private function obtenerProductosModificadosParaSaberSiEstaEnOferta($productos)
    {
        $ofertas = $this->productoModel->ofertas();
        $idsOfertas = array_column($ofertas, 'producto_id');
        $productosModificados = [];

        foreach ($productos as $producto) {
            if (in_array($producto['id'], $idsOfertas)) {
                $producto['en_oferta'] = true;
                foreach ($ofertas as $oferta)
                    if ($producto['id'] == $oferta['producto_id']) {
                        $producto['precio_nuevo'] = $oferta['precio_nuevo'];
                        $producto['descuento'] = $oferta['descuento'];
                    }
            }
            $productosModificados[] = $producto;
        }
        return $productosModificados;
    }
}