<?php
class CarritoController
{
    private $request;
    private $renderer;
    private $carritoModel;
    private $clienteModel;
    private $carritoProductModel;
    private $productModel;
    private $categoriaModel;

    public function __construct($request, $renderer, $carritoModel, $carritoProductModel, $clienteModel, $productModel, $categoriaModel)
    {
        $this->request = $request;
        $this->renderer = $renderer;
        $this->carritoModel = $carritoModel;
        $this->carritoProductModel = $carritoProductModel;
        $this->clienteModel = $clienteModel;
        $this->productModel = $productModel;
        $this->categoriaModel = $categoriaModel;
    }

    public function agregarAlCarrito()
    {
        if (session_status() == PHP_SESSION_NONE) {
            session_start();
        }

        $cliente_id  = $_SESSION['id'] ?? null;
        $producto_id = $this->request->get('id');
        $cantidad    = $this->request->post('cantidad') ?? 1;

        // 1. Control de Sesión
        if (!$cliente_id) {
            $_SESSION['mensajeError'] = 'Debes iniciar sesión para agregar productos al carrito.';
            Log::info('CarritoController::agregarAlCarrito - Debe iniciar sesión');
            header('Location: ?controller=usuario&method=iniciarSesion');
            exit;
        }

        // 2. Obtener Precio del Producto
        $producto = $this->productModel->buscarPorId($producto_id);
        if (empty($producto)) {
            $_SESSION['mensajeError'] = 'El producto solicitado no existe.';
            header('Location: ?controller=home&method=index');
            exit;
        }
        $precio = $producto[0]['precio'];

        // 3. Buscar o Crear Carrito Activo
        $carritoActivo = $this->carritoModel->buscarCarritosActivos($cliente_id);

        if (empty($carritoActivo)) {
            $this->carritoModel->crearCarrito($cliente_id);
            $carritoActivo = $this->carritoModel->buscarCarritosActivos($cliente_id);
        }

        if (empty($carritoActivo)) {
            $_SESSION['mensajeError'] = 'Error al recuperar tu carrito de compras.';
            header('Location: ?controller=home&method=index');
            exit;
        }

        $carrito_id = $carritoActivo[0]['id'];

        // 4. Agregar o Actualizar Producto
        $exito = $this->carritoProductModel->agregarProducto($carrito_id, $producto_id, $cantidad, $precio);

        if ($exito) {
            $_SESSION['mensajeExito'] = '¡Producto agregado al carrito correctamente!';
            Log::info('CarritoController::agregarAlCarrito - Producto agregado');
        } else {
            $_SESSION['mensajeError'] = 'Hubo un problema, el producto no pudo ser agregado.';
            Log::error('CarritoController::agregarAlCarrito - Error al agregar producto');
        }

        header('Location: ?controller=home&method=index');
        exit;
    }

    public function verCarrito()
    {
        if (session_status() == PHP_SESSION_NONE)
            session_start();

        $nombre     = $_SESSION['nombre'] ?? "";
        $apellido   = $_SESSION['apellido'] ?? "";
        $logueado   = isset($_SESSION['id']);
        $cliente_id = $_SESSION['id'] ?? "";

        $carritoActivo = $this->carritoModel->buscarCarritosActivos($cliente_id);
        $categorias    = $this->categoriaModel->obtenerCategorias();

        $productos = [];
        $totalCarrito = 0;

        if (!empty($carritoActivo)) {
            $productos = $this->carritoProductModel->obtenerProductos($carritoActivo[0]['id']);

            // Calcular el total acumulado
            foreach ($productos as $p)
                $totalCarrito += $p['subtotal'];
        }

        // Pasamos tanto los productos como el total general a la vista
        $this->renderer->render('verCarrito', [
            'productos' => $productos, 'nombre' => $nombre, 'apellido' => $apellido,
            'categorias' => $categorias, 'logueado' => $logueado,
            'totalCarrito' => number_format($totalCarrito, 2, '.', '')
        ]);
    }

    public function eliminarProducto()
    {
      /*  if (session_status() == PHP_SESSION_NONE)
            session_start();

        $cliente_id = $_SESSION['id'] ?? null;

        $carrito_id  = $this->carritoModel->buscarCarritosActivos($cliente_id)[0]['id'];
        $producto_id = $this->request->get('id');

        $eliminado = $this->carritoProductModel->eliminarProductoDelCarrito($carrito_id, $producto_id);

        if ($eliminado) {
            echo json_encode(['status' => 'success', 'mensaje' => 'Producto eliminado del carrito']);
        } else {
            http_response_code(400);
            echo json_encode(['status' => 'error', 'mensaje' => 'No se pudo eliminar el producto']);
        }
        exit; */
    }
}