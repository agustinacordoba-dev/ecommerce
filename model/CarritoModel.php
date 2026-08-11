<?php
class CarritoModel
{
    private $database;

    public function __construct($database)
    {
        $this->database = $database;
    }

    public function crearCarrito($cliente_id)
    {
        $sql = "INSERT INTO carritos (cliente_id) VALUES (?)";
        return $this->database->execute($sql, [$cliente_id]);
    }

    public function eliminarCarritoAuto($cliente_id)
    {
        $sql = "DELETE FROM carritos WHERE cliente_id = ? AND estado = 'activo' AND actualizado_en < NOW() - INTERVAL 7 DAY;";
        return $this->database->execute($sql, [$cliente_id]);
    }

    public function obtenerCarrito($cliente_id)
    {
        $sql = "SELECT * FROM carritos WHERE cliente_id = ?";
        return $this->database->query($sql, [$cliente_id]);
    }
    public function eliminarCarritoAbandonado($cliente_id)
    {
        $sql = "DELETE FROM carritos WHERE cliente_id = ? AND estado = 'abandonado';";
        return $this->database->execute($sql, [$cliente_id]);
    }

    public function buscarCarritosActivos($cliente_id)
    {
        $sql = "SELECT * FROM carritos WHERE cliente_id = ? AND estado = 'activo' ORDER BY id DESC LIMIT 1";
        return $this->database->query($sql, [$cliente_id]) ?? null;
    }

    public function cambiarEstado($id, $estado)
    {
        $sql = "UPDATE carritos SET estado = ? WHERE id = ?";
        return $this->database->execute($sql, [$estado, $id]);
    }
}