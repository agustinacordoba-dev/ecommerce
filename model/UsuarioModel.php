<?php
class UsuarioModel
{
    private $database;

    public function __construct($database)
    {
        $this->database = $database;
    }

    public function registrarUsuario($nombre, $apellido, $email, $password, $telefono)
    {
        $sql = "INSERT INTO cliente (codigocliente, nombre, apellido, email, password, telefono) VALUES (?, ?, ?, ?, ?, ?)";
        return $this->database->execute($sql, [$this->generarCodigo(), $nombre, $apellido, $email, $password, $telefono]) > 0;
    }

    public function emailExiste($email)
    {
        $sql = "SELECT COUNT(*) AS conteo FROM cliente WHERE email = ?";
        return $this->database->query($sql, [$email])[0]['conteo'] > 0;
    }

    public function buscarUsuarioPorEmail($email)
    {
        $sql = "SELECT * FROM cliente WHERE email = ?";
        $resultado = $this->database->query($sql, [$email]);
        return !empty($resultado) ? $resultado[0] : null;
    }

    private function generarCodigo()
    {
        do {
            $codigo = sprintf('%06d', random_int(0, 999999));
            $resultado = $this->database->query("SELECT COUNT(*) AS conteo FROM cliente WHERE codigoCliente = ?", [$codigo]);
        } while ($resultado[0]['conteo'] > 0);

        return $codigo;
    }

}