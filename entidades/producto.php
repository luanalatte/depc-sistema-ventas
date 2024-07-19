<?php

class Producto {
    private $idproducto;
    private $nombre;
    private $cantidad;
    private $precio;
    private $descripcion;
    private $imagen;
    private $fk_idtipoproducto;

    // public function __construct() {}

    public function __get($atributo) {
        return $this->$atributo;
    }

    public function __set($atributo, $valor) {
        $this->$atributo = $valor;
    }

    public function eliminarImagen() {
        if ($this->imagen && file_exists("img/productos/$this->imagen")) {
            unlink("img/productos/$this->imagen");
        }
    }

    public function cargarFormulario($request) {
        $this->idproducto = $request["id"] ?? "";
        $this->nombre = $request["txtNombre"] ?? "";
        $this->cantidad = $request["txtCantidad"] ?? "";
        $this->precio = $request["txtPrecio"] ?? "";
        $this->descripcion = $request["txtDescripcion"] ?? "";
        $this->fk_idtipoproducto = $request["lstTipoProducto"] ?? "";
    }

    public function insertar() {
        $mysqli = new mysqli(Config::BBDD_HOST, Config::BBDD_USUARIO, Config::BBDD_CLAVE, Config::BBDD_NOMBRE, Config::BBDD_PORT);
        $sql = "INSERT INTO productos (
                    nombre,
                    cantidad,
                    precio,
                    descripcion,
                    imagen,
                    fk_idtipoproducto
                ) VALUES (
                    '$this->nombre',
                    '$this->cantidad',
                    '$this->precio',
                    '$this->descripcion',
                    '$this->imagen',
                    '$this->fk_idtipoproducto'
                );";        
        // print_r($sql);exit;

        if (!$mysqli->query($sql)) {
            printf("Error en query: %s\n", $mysqli->error . " " . $sql);
        }

        $this->idproducto = $mysqli->insert_id; //Obtiene el ID generado
        $mysqli->close();
    }

    public function actualizar() {
        $mysqli = new mysqli(Config::BBDD_HOST, Config::BBDD_USUARIO, Config::BBDD_CLAVE, Config::BBDD_NOMBRE, Config::BBDD_PORT);
        $sql = "UPDATE productos SET
                nombre = '" . $this->nombre . "',
                cantidad = '" . $this->cantidad . "',
                precio = '" . $this->precio . "',
                descripcion = '" . $this->descripcion . "',
                imagen =  '" . $this->imagen . "',
                fk_idtipoproducto =  '" . $this->fk_idtipoproducto . "'
                WHERE idproducto = " . $this->idproducto;

        if (!$mysqli->query($sql)) {
            printf("Error en query: %s\n", $mysqli->error . " " . $sql);
        }

        $mysqli->close();
    }

    public function eliminar() {
        $this->obtenerImagen();
        $this->eliminarImagen();

        $mysqli = new mysqli(Config::BBDD_HOST, Config::BBDD_USUARIO, Config::BBDD_CLAVE, Config::BBDD_NOMBRE, Config::BBDD_PORT);
        $sql = "DELETE FROM productos WHERE idproducto = " . $this->idproducto;

        if (!$mysqli->query($sql)) {
            printf("Error en query: %s\n", $mysqli->error . " " . $sql);
        }

        $mysqli->close();
    }

    public function obtenerImagen() {
        $mysqli = new mysqli(Config::BBDD_HOST, Config::BBDD_USUARIO, Config::BBDD_CLAVE, Config::BBDD_NOMBRE, Config::BBDD_PORT);
        $sql = "SELECT idproducto,
                        imagen
                FROM productos
                WHERE idproducto = $this->idproducto";
        if (!$resultado = $mysqli->query($sql)) {
            printf("Error en query: %s\n", $mysqli->error . " " . $sql);
        }

        if ($fila = $resultado->fetch_assoc()) {
            $this->imagen = $fila["imagen"];
        }

        $mysqli->close();
    }

    //Convierte el resultado en un array asociativo
    public function construirDesdeFila($fila) {
        $this->idproducto = $fila["idproducto"];
        $this->nombre = $fila["nombre"];
        $this->cantidad = $fila["cantidad"];
        $this->precio = $fila["precio"];
        $this->descripcion = $fila["descripcion"];
        $this->imagen = $fila["imagen"];
        $this->fk_idtipoproducto = $fila["fk_idtipoproducto"];
    }

    public function obtenerPorId() {
        $mysqli = new mysqli(Config::BBDD_HOST, Config::BBDD_USUARIO, Config::BBDD_CLAVE, Config::BBDD_NOMBRE, Config::BBDD_PORT);
        $sql = "SELECT idproducto,
                        nombre,
                        cantidad,
                        precio,
                        descripcion,
                        imagen,
                        fk_idtipoproducto
                FROM productos
                WHERE idproducto = $this->idproducto";
        if (!$resultado = $mysqli->query($sql)) {
            printf("Error en query: %s\n", $mysqli->error . " " . $sql);
        }

        if ($fila = $resultado->fetch_assoc()) {
            $this->construirDesdeFila($fila);
        }

        $mysqli->close();
    }

    public static function obtenerPrecio($idproducto) {
        $mysqli = new mysqli(Config::BBDD_HOST, Config::BBDD_USUARIO, Config::BBDD_CLAVE, Config::BBDD_NOMBRE, Config::BBDD_PORT);
        $sql = "SELECT precio FROM productos WHERE idproducto = '$idproducto'";
        if (!$resultado = $mysqli->query($sql)) {
            printf("Error en query: %s\n", $mysqli->error . " " . $sql);
        }

        if ($fila = $resultado->fetch_assoc()) {
            return floatval($fila["precio"]);
        }

        $mysqli->close();
    }

    public static function obtenerTodos() {
        $mysqli = new mysqli(Config::BBDD_HOST, Config::BBDD_USUARIO, Config::BBDD_CLAVE, Config::BBDD_NOMBRE, Config::BBDD_PORT);
        $sql = "SELECT 
                    idproducto,
                    nombre,
                    cantidad,
                    precio,
                    descripcion,
                    imagen,
                    fk_idtipoproducto
                FROM productos";

        if (!$resultado = $mysqli->query($sql)) {
            printf("Error en query: %s\n", $mysqli->error . " " . $sql);
        }

        $aResultado = array();
        if($resultado){

            while($fila = $resultado->fetch_assoc()){
                $entidadAux = new Producto();
                $entidadAux->construirDesdeFila($fila);
                $aResultado[] = $entidadAux;
            }
        }

        $mysqli->close();
        return $aResultado;
    }

    public static function obtenerProductosPorTipo($idtipoproducto) {
        $mysqli = new mysqli(Config::BBDD_HOST, Config::BBDD_USUARIO, Config::BBDD_CLAVE, Config::BBDD_NOMBRE, Config::BBDD_PORT);

        $sql = "SELECT 
                    idproducto
                FROM productos
                WHERE fk_idtipoproducto = $idtipoproducto";

        if (!$resultado = $mysqli->query($sql)) {
            printf("Error en query: %s\n", $mysqli->error . " " . $sql);
        }

        $aResultado = array();
        if($resultado){

            while($fila = $resultado->fetch_assoc()){
                $entidadAux = new Producto();
                $entidadAux->idproducto = $fila["idproducto"];
                $aResultado[] = $entidadAux;
            }
        }

        $mysqli->close();
        return $aResultado;
    }

}

?>