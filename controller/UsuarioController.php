<?php
class UsuarioController
{
    private $request;
    private $renderer;
    private $usuarioModel;
    private $categoriaModel;


    public function __construct($request, $renderer, $usuarioModel, $categoriaModel)
    {
        $this->request = $request;
        $this->renderer = $renderer;
        $this->usuarioModel = $usuarioModel;
        $this->categoriaModel = $categoriaModel;
    }

    public function registro()
    {
        Log::info("UsuarioController::registro - formulario");

        $categorias = $this->categoriaModel->obtenerCategorias();
        $this->renderer->render("registro", ["categorias" => $categorias]);
    }

    public function procesarRegistro()
    {
        $nombre              = trim($this->request->post('nombre'));
        $apellido            = trim($this->request->post('apellido'));
        $email               = trim($this->request->post('email'));
        $contrasenia         = trim($this->request->post('password'));
        $contraseniaRepetida = trim($this->request->post('passwordRepetida'));
        $telefono            = trim($this->request->post('telefono'));

        if (empty($nombre) || empty($apellido) || empty($email) || empty($contrasenia) || empty($telefono))
            return $this->renderer->render("registro", ["mensaje" => "Todos los campos son obligatorios"]);
        if ($contrasenia !== $contraseniaRepetida)
            return $this->renderer->render("registro", ["mensaje" => "Las contraseñas no coinciden"]);

        $contraseniaHash = password_hash($contrasenia, PASSWORD_DEFAULT);

        if ($this->usuarioModel->emailExiste($email)) {
            Log::error("UsuarioController::procesarRegistro - El correo ya esta registrado");
            return $this->renderer->render("registro", ["mensaje" => "Ese correo ya está registrado"]);
        }

        $registroExitoso = $this->usuarioModel->registrarUsuario($nombre, $apellido, $email, $contraseniaHash, $telefono);
        if ($registroExitoso) {
            Log::info("UsuarioController::procesarRegistro - Se registro un nuevo usuario");
            header("location: ?controller=usuario&method=iniciarSesion");
            exit();
        } else {
            Log::error("UsuarioController::procesarRegistro - No se pudo registrar un nuevo usuario");
            return $this->renderer->render("registro", ["mensaje" => "No se pudo registrar"]);
        }
    }

    public function iniciarSesion()
    {
        Log::info("UsuarioController::iniciarSesion - formulario");
        $categorias = $this->categoriaModel->obtenerCategorias();
        $this->renderer->render("login", ["categorias" => $categorias]);
    }

    public function autenticarUsuario()
    {
        $email       = trim($this->request->post('email'));
        $contrasenia = trim($this->request->post('password'));

        if (empty($email) || empty($contrasenia))
            return $this->renderer->render("login", ["mensaje" => "Todos los campos son obligatorios"]);

        $usuario = $this->usuarioModel->buscarUsuarioPorEmail($email);

        if (!$usuario)
            return $this->renderer->render("login", ["mensaje" => "El usuario no existe"]);

        if (!password_verify($contrasenia, $usuario["password"]))
            return $this->renderer->render("login", ["mensaje" => "Contraseña incorrecta"]);

        if (session_status() === PHP_SESSION_NONE)
            session_start();

        $_SESSION["id"]       = $usuario["id"];
        $_SESSION["nombre"]   = $usuario["nombre"];
        $_SESSION["apellido"] = $usuario["apellido"];

        Log::info("UsuarioController::autenticarUsuario() - Inicio de sesion exitoso");
        header("location: ?controller=home&method=index");
        exit();
    }

    public function cerrarSesion()
    {
        if (session_status() === PHP_SESSION_NONE)
            session_start();


        // Limpiar todas las variables de la sesión activa
        $_SESSION = [];

        // Borrar la cookie de sesión del navegador del cliente
        if (ini_get("session.use_cookies")) {
            $params = session_get_cookie_params();
            setcookie(
                session_name(), '',
                time() - 42000,
                $params["path"],
                $params["domain"],
                $params["secure"],
                $params["httponly"]
            );
        }

        // Destruir los datos guardados en el servidor
        session_destroy();

        Log::info("UsuarioController::cerrarSesion - Sesión cerrada correctamente");

        header("location: ?controller=home&method=index");
        exit();
    }

}