<?php

class Usuario {
    private $idusuario;
    private $usuario;
    private $clave;
    private $nombre;
    private $apellido;
    private $correo;

    // public function __construct() {}

    public function __get($atributo) {
        return $this->$atributo;
    }

    public function __set($atributo, $valor) {
        $this->$atributo = $valor;
    }

    public function cargarFormulario($request) {
        $this->idusuario = $request["id"] ?? "";
        $this->usuario = $request["txtUsuario"] ?? "";
        $this->clave = $request["txtClave"] ?? "";
        $this->nombre = $request["txtNombre"] ?? "";
        $this->apellido = $request["txtApellido"] ?? "";
        $this->correo = $request["txtCorreo"] ?? "";
    }

    public function insertar() {
        $mysqli = new mysqli(Config::BBDD_HOST, Config::BBDD_USUARIO, Config::BBDD_CLAVE, Config::BBDD_NOMBRE, Config::BBDD_PORT);
        $sql = "INSERT INTO usuarios (
                    usuario,
                    clave,
                    nombre,
                    apellido,
                    correo
                ) VALUES (
                    '$this->usuario',
                    '$this->clave',
                    '$this->nombre',
                    '$this->apellido',
                    '$this->correo'
                );";        
        // print_r($sql);exit;

        if (!$mysqli->query($sql)) {
            printf("Error en query: %s\n", $mysqli->error . " " . $sql);
        }

        $this->idusuario = $mysqli->insert_id; //Obtiene el ID generado
        $mysqli->close();
    }

    public function actualizar() {
        $mysqli = new mysqli(Config::BBDD_HOST, Config::BBDD_USUARIO, Config::BBDD_CLAVE, Config::BBDD_NOMBRE, Config::BBDD_PORT);
        $sql = "UPDATE usuarios SET
                usuario = '" . $this->usuario . "',
                clave = '" . $this->clave . "',
                nombre = '" . $this->nombre . "',
                apellido = '" . $this->apellido . "',
                correo =  '" . $this->correo . "'
                WHERE idusuario = " . $this->idusuario;

        if (!$mysqli->query($sql)) {
            printf("Error en query: %s\n", $mysqli->error . " " . $sql);
        }

        $mysqli->close();
    }

    public function eliminar() {
        $mysqli = new mysqli(Config::BBDD_HOST, Config::BBDD_USUARIO, Config::BBDD_CLAVE, Config::BBDD_NOMBRE, Config::BBDD_PORT);
        $sql = "DELETE FROM usuarios WHERE idusuario = " . $this->idusuario;

        if (!$mysqli->query($sql)) {
            printf("Error en query: %s\n", $mysqli->error . " " . $sql);
        }

        $mysqli->close();
    }

    //Convierte el resultado en un array asociativo
    public function construirDesdeFila($fila) {
        $this->idusuario = $fila["idusuario"];
        $this->usuario = $fila["usuario"];
        $this->clave = $fila["clave"];
        $this->nombre = $fila["nombre"];
        $this->apellido = $fila["apellido"];
        $this->correo = $fila["correo"];
    }

    public function obtenerPorId() {
        $mysqli = new mysqli(Config::BBDD_HOST, Config::BBDD_USUARIO, Config::BBDD_CLAVE, Config::BBDD_NOMBRE, Config::BBDD_PORT);
        $sql = "SELECT idusuario,
                        usuario,
                        clave,
                        nombre,
                        apellido,
                        correo
                FROM usuarios
                WHERE idusuario = $this->idusuario";
        if (!$resultado = $mysqli->query($sql)) {
            printf("Error en query: %s\n", $mysqli->error . " " . $sql);
        }

        if ($fila = $resultado->fetch_assoc()) {
            $this->construirDesdeFila($fila);
        }

        $mysqli->close();
    }

    public function obtenerTodos() {
        $mysqli = new mysqli(Config::BBDD_HOST, Config::BBDD_USUARIO, Config::BBDD_CLAVE, Config::BBDD_NOMBRE, Config::BBDD_PORT);
        $sql = "SELECT 
                    idusuario,
                    usuario,
                    clave,
                    nombre,
                    apellido,
                    correo
                FROM usuarios";

        if (!$resultado = $mysqli->query($sql)) {
            printf("Error en query: %s\n", $mysqli->error . " " . $sql);
        }

        $aResultado = array();
        if($resultado){

            while($fila = $resultado->fetch_assoc()){
                $entidadAux = new Usuario();
                $entidadAux->construirDesdeFila($fila);
                $aResultado[] = $entidadAux;
            }
        }

        $mysqli->close();
        return $aResultado;
    }

}

?>