<?php
class ProductoModel
{
    private $database;

    public function __construct($database) {
        $this->database = $database;
    }

    public function agregarProducto($datos) {
        $codigo = $this->generarCodigo();
        array_unshift($datos, $codigo); // agrega codigo al array de datos colocando lo 1ro

        $sql = "INSERT INTO producto (codigo, nombre, descripcion, precio, stock, foto, categoria_id) 
                VALUES (?, ?, ?, ?, ?, ?, ?)";
        return $this->database->execute($sql, $datos) == 1;
    }

    public function eliminarPorCodigo($codigo)
    {
        $sql = "DELETE FROM producto WHERE codigo = ?";
        return $this->database->execute($sql, $codigo) == 1;
    }

    public function eliminarPorNombre($datos, $nombre){
        $sql = "DELETE FROM producto WHERE nombre = ?";
        return $this->database->execute($sql, $nombre) == 1;
    }

    public function modificarProducto($codigo) {
        $sql = "UPDATE producto SET nombre = ?, descripcion = ?, precio = ?, stock = ?, categoria_id = ? WHERE codigo = ?";
        return $this->database->execute($sql, $codigo) == 1;
    }

    public function buscarProducto($texto) {
        $txt = trim($texto);
        $busqueda = "%" . $txt . "%";

        $sql = "SELECT p.*, c.nombre AS categoria_nombre 
            FROM producto p
            LEFT JOIN categoria c ON p.categoria_id = c.id
            WHERE p.codigo LIKE ? 
               OR p.nombre LIKE ? 
               OR c.nombre LIKE ?";

        return $this->database->query($sql, [$busqueda, $busqueda, $busqueda]);
    }

    public function filtrarPorCategoria($id)
    {
        $sql = "SELECT p.*
                FROM producto p 
                INNER JOIN categoria c ON p.categoria_id = c.id 
                WHERE p.categoria_id = ?";
        return $this->database->query($sql, [$id]);
    }

    public function buscarPorId($id)
    {
        $sql = "SELECT * FROM producto WHERE id = ?";
        return $this->database->query($sql, [$id]);
    }

    public function obtenerProductos() {
        $sql = "SELECT * FROM producto";
        return $this->database->query($sql);
    }

    private function generarCodigo()
    {
        return sprintf('%06d', random_int(0, 999999));
    }
}