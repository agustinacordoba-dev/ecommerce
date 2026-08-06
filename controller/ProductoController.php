<?php

class ProductoController
{
    private $request;
    private $renderer;
    private $productoModel;
    private $categoriaModel;

    public function __construct($request, $renderer, $productoModel, $categoriaModel) {
        $this->request        = $request;
        $this->renderer       = $renderer;
        $this->productoModel  = $productoModel;
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

        if($this->productoModel->agregarProducto([$codigo, $nombre, $descripcion, $precio, $stock, $foto, $categoria_id]))
            $this->renderer->render("agregarProducto", ["mensaje" => "Producto Agregado"]);
        else {
            Log::error("ProductoController::agregar - Error el producto no pudo ser agregado");
            $this->renderer->render("agregarProducto", ["error" => "El producto no pudo ser agregado"]);
        }

    }

    public function detalle()
    {
        if (session_status() == PHP_SESSION_NONE)
            session_start();

        $id         = $this->request->get("id");
        $resultado  = $this->productoModel->buscarPorId($id);
        $producto   = !empty($resultado) ? $resultado[0] : null;

        $categorias = $this->categoriaModel->obtenerCategorias();

        $nombre   = $_SESSION['nombre'] ?? "";
        $apellido = $_SESSION['apellido'] ?? "";
        $logueado = isset($_SESSION['id']);

        $estaEnOff  = $this->verificarSiEstaEnOfertas($id);
        $productoOff = $this->buscarProductoEnOferta($id);

        $this->renderer->render("detalle", ["producto" => $producto,  "categorias" => $categorias, "enOferta" => $estaEnOff,
            "productoOff" => $productoOff, "nombre" => $nombre, "apellido" => $apellido, "logueado" => $logueado]);
    }

    public function filtrarProduPorCategorias()
    {
        if (session_status() == PHP_SESSION_NONE)
            session_start();

        $idCategoria     = $this->request->get("id");
        $productos       = $this->obtenerProductosModificadosParaSaberSiEstaEnOferta($this->productoModel->filtrarPorCategoria($idCategoria));
        $categoriaActual = $this->categoriaModel->buscarCategoriaPorId($idCategoria);
        $categorias      = $this->categoriaModel->obtenerCategorias();

        $nombre   = $_SESSION['nombre'] ?? "";
        $apellido = $_SESSION['apellido'] ?? "";
        $logueado = isset($_SESSION['id']);

        $this->renderer->render("prodCategoria", ["productos" => $productos, "categoriaActual" => $categoriaActual[0],
            "totalProductos" => count($productos), "hasProductos" => !empty($productos), "categorias" => $categorias,
            "nombre" => $nombre, "apellido" => $apellido, "logueado" => $logueado]);
    }

    public function ofertas()
    {
        if (session_status() == PHP_SESSION_NONE)
            session_start();

        $productos  = $this->productoModel->ofertas();
        $categorias = $this->categoriaModel->obtenerCategorias();

        $nombre   = $_SESSION['nombre'] ?? "";
        $apellido = $_SESSION['apellido'] ?? "";
        $logueado = isset($_SESSION['id']);

        $this->renderer->render("ofertas", ["productos" => $productos, "categorias" => $categorias,
            "nombre" => $nombre, "apellido" => $apellido, "logueado" => $logueado]);
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

    private function verificarSiEstaEnOfertas($id)
    {
        $idsOfertas = array_column($this->productoModel->ofertas(), 'producto_id');
        return in_array($id, $idsOfertas);
    }

    private function buscarProductoEnOferta($id)
    {
        $idsOfertas = array_column($this->productoModel->ofertas(), 'producto_id');

        foreach ($idsOfertas as $i => $idOferta)
            if ($id == $idOferta) {
                $resultado = $this->productoModel->buscarProductoEnOferta($id);
                return !empty($resultado) ? $resultado[0] : null;
            }

        return null;
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