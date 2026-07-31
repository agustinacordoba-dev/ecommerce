<?php

class CategoriaModel
{
    private $database;

    public function __construct($database) {
        $this->database = $database;
    }

    public function obtenerCategorias() {
        $sql = "SELECT * FROM categoria";
         return $this->database->query($sql);
    }

    public function buscarCategoriaPorId($id) {
        $sql = "SELECT * FROM categoria WHERE id = $id";
        return $this->database->query($sql);
    }

    public function buscarCategoriaPorNombre($nombre) {
        $sql = "SELECT * FROM categoria WHERE nombre = '$nombre'";
        return $this->database->query($sql);
    }

    public function agregarCategoria($nombre) {
        $sql = "INSERT INTO categoria (nombre) VALUES (?)";
        return $this->database->execute([$nombre]) == 1;
    }

    public function eliminarCategoriaPorId($id) {
        $sql = "DELETE FROM categoria WHERE id = ?";
        return $this->database->execute($sql, $id);
    }

    public function modificarCategoriaPorId($nombre, $id) {
        $sql = "UPDATE categoria SET nombre = ? WHERE id = ?";
        $datos = [$nombre, $id];
        return $this->database->execute($sql, $datos);
    }
}