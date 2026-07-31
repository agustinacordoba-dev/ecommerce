<?php

class ProductoController
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

    public function mostrarFormAgregarProducto()
    {
        $categorias = $this->categoriaModel->obtenerCategorias();
        $this->renderer->render("agregarProducto", ["categorias" => $categorias]);
    }
    public function procesarAgregarProducto() {

        $codigo       = trim($this->request->post("codigo"));
        $nombre       = trim($this->request->post("nombre"));
        $descripcion  = trim($this->request->post("descripcion"));
        $precio       = trim($this->request->post("precio"));
        $stock        = trim($this->request->post("stock"));
        $foto         = $this->procesarImagen();
        $categoria_id = $this->categoriaModel->buscarCategoriaPorNombre(trim($this->request->post("categoria")));

        if (empty($codigo) || empty($nombre) || empty($descripcion) || empty($precio) || empty($stock) || empty($categoria_id)) {
            Log::error("ProductoController::agregar - Error campos incompletos");
            $this->renderer->render("agregarProducto", ["error" => "Todos los campos son obligatorios"]);
        }

        $datos = [$codigo, $nombre, $descripcion, $precio, $stock, $foto, $categoria_id];

        if($this->productoModel->agregarProducto($datos)) {
            $this->renderer->render("agregarProducto", ["mensaje" => "Producto Agregado"]);
        } else {
            Log::error("ProductoController::agregar - Error el producto no pudo ser agregado");
            $this->renderer->render("agregarProducto", ["error" => "El producto no pudo ser agregado"]);
        }

    }

    public function detalle()
    {
        $id = $this->request->get("id");
        $resultado = $this->productoModel->buscarPorId($id);
        $categorias = $this->categoriaModel->obtenerCategorias();
        $producto = !empty($resultado) ? $resultado[0] : null;

        $this->renderer->render("detalle", ["producto" => $producto, "categorias" => $categorias]);
    }

// funciones privadas
    private function procesarImagen()
    {
        $nombre   = $_FILES['foto']['name'];
        $ruta_tmp = $_FILES['foto']['tmp_name'];
        $tamanio  = $_FILES['foto']['size'];
        $error    = $_FILES['foto']['error'];

        if ($error !== UPLOAD_ERR_OK) {
            Log::error("ProductoController::agregar - Error subiendo imagen");
            $this->renderer->render("agregarProducto", ["error" => "No se pudo subir el archivo"]);
        }

        $extensionesPermitidas = ['jpg', 'jpeg', 'png'];

        $extension = strtolower(pathinfo($nombre, PATHINFO_EXTENSION));

        if (!in_array($extension, $extensionesPermitidas)) {
            Log::error("ProductoController::agregar - Error formato invalido");
            $this->renderer->render("agregarProducto", ["error" => "Formato inválido. Solo JPG, JPEG o PNG."]);
        }

        $maxTamanio = 2 * 1024 * 1024;

        if ($tamanio > $maxTamanio) {
            Log::error("ProductoController::agregar - Error la imagen supera los 2MB");
            $this->renderer->render("agregarProducto", ["error" => "La imagen supera los 2MB."]);
        }

        $rutaFinal = 'public/img/' . uniqid() . "." . $extension;

        if (!move_uploaded_file($ruta_tmp, $rutaFinal)) {
            Log::error("ProductoController::agregar - Error la imagen no se pudo guardar");
            $this->renderer->render("agregarProducto", ["error" => "No se pudo guardar la imagen."]);
        }

        return $rutaFinal;
    }
}