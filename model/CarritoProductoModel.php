<?php
class CarritoProductoModel
{
    private $database;

    public function __construct($database)
    {
        $this->database = $database;
    }

    public function agregarProducto($carrito_id, $producto_id, $cantidad, $precio)
    {
        $sql = "INSERT INTO carrito_productos (carrito_id, producto_id, precio_unitario, cantidad) VALUES(?, ?, ?, ?)";

        $existeProducto = $this->verificarExistenciaProducto($carrito_id, $producto_id);

        // Si el resultado de query() devuelve un array con registros
        if (!empty($existeProducto) && isset($existeProducto[0])) {
            $cantidadNueva = $existeProducto[0]['cantidad'] + $cantidad; // Si existe - actualizar la cantidad
            return $this->actualizarCantidadProducto($carrito_id, $producto_id, $cantidadNueva);
        } else
            return $this->database->execute($sql, [$carrito_id, $producto_id, $precio, $cantidad]);
    }

    public function obtenerProductos($carrito_id)
    {
        $sql = "SELECT cp.id, cp.carrito_id, cp.producto_id, cp.cantidad, cp.precio_unitario, 
                       p.nombre AS producto_nombre, p.foto AS producto_imagen,
                       (cp.cantidad * cp.precio_unitario) AS subtotal
                FROM carrito_productos cp
                INNER JOIN producto p ON cp.producto_id = p.id
                WHERE cp.carrito_id = ?";

        return $this->database->query($sql, [$carrito_id]);
    }

    public function actualizarCantidadProducto($carrito_id, $producto_id, $cantidad)
    {
        $sql = "UPDATE carrito_productos SET cantidad = ? WHERE carrito_id = ? AND producto_id = ?";
        return $this->database->execute($sql, [$cantidad, $carrito_id, $producto_id]);
    }

    public function eliminarProductoDelCarrito($carrito_id, $producto_id)
    {
        $sql = "DELETE FROM carrito_productos WHERE carrito_id = ? AND producto_id = ?";
        return $this->database->execute($sql, [$carrito_id, $producto_id]);
    }

    public function vaciarCarrito($carrito_id)
    {
        $sql = "DELETE FROM carrito_productos WHERE carrito_id = ?";
        return $this->database->execute($sql, [$carrito_id]);
    }

    private function verificarExistenciaProducto($carrito_id, $producto_id)
    {
        $sql = "SELECT * FROM carrito_productos WHERE carrito_id = ? AND producto_id = ?";
        return $this->database->query($sql, [$carrito_id, $producto_id]) ?? null;
    }
}