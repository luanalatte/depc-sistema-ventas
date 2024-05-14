<?php

class Venta {
    private $idventa;
    private $fk_idcliente;
    private $fk_idproducto;
    private $fecha_hora;
    private $cantidad;
    private $preciounitario;
    private $total;

    private $nombre_cliente;
    private $nombre_producto;

    // public function __construct() {}

    public function __get($atributo) {
        return $this->$atributo;
    }

    public function __set($atributo, $valor) {
        $this->$atributo = $valor;
    }

    public function cargarFormulario($request) {
        $this->idventa = $request["id"] ?? "";
        $this->fk_idcliente = $request["lstCliente"] ?? "";
        $this->fk_idproducto = $request["lstProducto"] ?? "";
        if (isset($request["txtFechaDia"], $request["txtFechaMes"], $request["txtFechaAnio"])) {
            $this->fecha_hora = $request["txtFechaAnio"] . "-" . $request["txtFechaMes"] . "-" . $request["txtFechaDia"];
            if (isset($request["txtHora"])) {
                $this->fecha_hora .= " " . date_format(date_create($request["txtHora"]), "H:i");
            }
        }
        $this->cantidad = $request["txtCantidad"] ?? "";
        $this->preciounitario = $request["txtPrecioUnitario"] ?? "";
        $this->total = $request["txtTotal"] ?? "";
    }

    public function insertar() {
        $mysqli = new mysqli(Config::BBDD_HOST, Config::BBDD_USUARIO, Config::BBDD_CLAVE, Config::BBDD_NOMBRE, Config::BBDD_PORT);
        $sql = "INSERT INTO ventas (
                    fk_idcliente,
                    fk_idproducto,
                    fecha_hora,
                    cantidad,
                    preciounitario,
                    total
                ) VALUES (
                    '$this->fk_idcliente',
                    '$this->fk_idproducto',
                    '$this->fecha_hora',
                    '$this->cantidad',
                    '$this->preciounitario',
                    '$this->total'
                );";        
        // print_r($sql);exit;

        if (!$mysqli->query($sql)) {
            printf("Error en query: %s\n", $mysqli->error . " " . $sql);
        }

        $this->idventa = $mysqli->insert_id; //Obtiene el ID generado
        $mysqli->close();
    }

    public function actualizar() {
        $mysqli = new mysqli(Config::BBDD_HOST, Config::BBDD_USUARIO, Config::BBDD_CLAVE, Config::BBDD_NOMBRE, Config::BBDD_PORT);
        $sql = "UPDATE ventas SET
                fk_idcliente = '" . $this->fk_idcliente . "',
                fk_idproducto = '" . $this->fk_idproducto . "',
                fecha_hora = '" . $this->fecha_hora . "',
                cantidad = '" . $this->cantidad . "',
                preciounitario =  '" . $this->preciounitario . "',
                total =  '" . $this->total . "'
                WHERE idventa = " . $this->idventa;

        if (!$mysqli->query($sql)) {
            printf("Error en query: %s\n", $mysqli->error . " " . $sql);
        }

        $mysqli->close();
    }

    public function eliminar() {
        $mysqli = new mysqli(Config::BBDD_HOST, Config::BBDD_USUARIO, Config::BBDD_CLAVE, Config::BBDD_NOMBRE, Config::BBDD_PORT);
        $sql = "DELETE FROM ventas WHERE idventa = " . $this->idventa;

        if (!$mysqli->query($sql)) {
            printf("Error en query: %s\n", $mysqli->error . " " . $sql);
        }

        $mysqli->close();
    }

    //Convierte el resultado en un array asociativo
    public function construirDesdeFila($fila) {
        $this->idventa = $fila["idventa"];
        $this->fk_idcliente = $fila["fk_idcliente"];
        $this->fk_idproducto = $fila["fk_idproducto"];
        $this->fecha_hora = $fila["fecha_hora"];
        $this->cantidad = $fila["cantidad"];
        $this->preciounitario = $fila["preciounitario"];
        $this->total = $fila["total"];
    }

    public function obtenerPorId() {
        $mysqli = new mysqli(Config::BBDD_HOST, Config::BBDD_USUARIO, Config::BBDD_CLAVE, Config::BBDD_NOMBRE, Config::BBDD_PORT);
        $sql = "SELECT idventa,
                        fk_idcliente,
                        fk_idproducto,
                        fecha_hora,
                        cantidad,
                        preciounitario,
                        total
                FROM ventas
                WHERE idventa = $this->idventa";
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
                    idventa,
                    fk_idcliente,
                    fk_idproducto,
                    fecha_hora,
                    cantidad,
                    preciounitario,
                    total
                FROM ventas";

        if (!$resultado = $mysqli->query($sql)) {
            printf("Error en query: %s\n", $mysqli->error . " " . $sql);
        }

        $aResultado = array();
        if($resultado){

            while($fila = $resultado->fetch_assoc()){
                $entidadAux = new Venta();
                $entidadAux->construirDesdeFila($fila);
                $aResultado[] = $entidadAux;
            }
        }

        $mysqli->close();
        return $aResultado;
    }

    public function obtenerGrilla() {
        $mysqli = new mysqli(Config::BBDD_HOST, Config::BBDD_USUARIO, Config::BBDD_CLAVE, Config::BBDD_NOMBRE, Config::BBDD_PORT);
        $sql = "SELECT 
                    ventas.idventa,
                    ventas.fk_idcliente,
                    clientes.nombre AS nombre_cliente,
                    ventas.fk_idproducto,
                    productos.nombre AS nombre_producto,
                    ventas.fecha_hora,
                    ventas.cantidad,
                    ventas.preciounitario,
                    ventas.total
                FROM ventas
                INNER JOIN clientes ON ventas.fk_idcliente = clientes.idcliente
                INNER JOIN productos ON ventas.fk_idproducto = productos.idproducto
                ORDER BY ventas.fecha_hora DESC";

        if (!$resultado = $mysqli->query($sql)) {
            printf("Error en query: %s\n", $mysqli->error . " " . $sql);
        }

        $aResultado = array();
        if($resultado){

            while($fila = $resultado->fetch_assoc()){
                $entidadAux = new Venta();
                $entidadAux->construirDesdeFila($fila);
                $entidadAux->nombre_cliente = $fila["nombre_cliente"];
                $entidadAux->nombre_producto = $fila["nombre_producto"];
                $aResultado[] = $entidadAux;
            }
        }

        $mysqli->close();
        return $aResultado;
    }

    public static function obtenerFacturacionMensual($mes, $anio) {
        $mysqli = new mysqli(Config::BBDD_HOST, Config::BBDD_USUARIO, Config::BBDD_CLAVE, Config::BBDD_NOMBRE, Config::BBDD_PORT);
        $sql = "SELECT SUM(total) AS sum
                FROM ventas
                WHERE MONTH(fecha_hora) = '$mes' AND YEAR(fecha_hora) = '$anio';";

        if (!$resultado = $mysqli->query($sql)) {
            printf("Error en query: %s\n", $mysqli->error . " " . $sql);
        }

        $facturacion = 0;
        if ($fila = $resultado->fetch_assoc()) {
            $facturacion = $fila["sum"] > 0 ? $fila["sum"] : 0;
        }

        $mysqli->close();
        return $facturacion;
    }

    public static function obtenerFacturacionPorPeriodo($fechaDesde, $fechaHasta) {
        $mysqli = new mysqli(Config::BBDD_HOST, Config::BBDD_USUARIO, Config::BBDD_CLAVE, Config::BBDD_NOMBRE, Config::BBDD_PORT);
        $sql = "SELECT SUM(total) AS sum
                FROM ventas
                WHERE fecha_hora >= '$fechaDesde' AND fecha_hora <= '$fechaHasta 23:59:59';";

        if (!$resultado = $mysqli->query($sql)) {
            printf("Error en query: %s\n", $mysqli->error . " " . $sql);
        }

        $facturacion = 0;
        if ($fila = $resultado->fetch_assoc()) {
            $facturacion = $fila["sum"] > 0 ? $fila["sum"] : 0;
        }

        $mysqli->close();
        return $facturacion;
    }

    public function obtenerVentasPorCliente($idcliente) {
        $mysqli = new mysqli(Config::BBDD_HOST, Config::BBDD_USUARIO, Config::BBDD_CLAVE, Config::BBDD_NOMBRE, Config::BBDD_PORT);

        $sql = "SELECT 
                    idventa
                FROM ventas
                WHERE fk_idcliente = $idcliente";

        if (!$resultado = $mysqli->query($sql)) {
            printf("Error en query: %s\n", $mysqli->error . " " . $sql);
        }

        $aResultado = array();
        if($resultado){

            while($fila = $resultado->fetch_assoc()){
                $entidadAux = new Venta();
                $entidadAux->idventa = $fila["idventa"];
                $aResultado[] = $entidadAux;
            }
        }

        $mysqli->close();
        return $aResultado;
    }

    public function obtenerVentasPorProducto($idproducto) {
        $mysqli = new mysqli(Config::BBDD_HOST, Config::BBDD_USUARIO, Config::BBDD_CLAVE, Config::BBDD_NOMBRE, Config::BBDD_PORT);

        $sql = "SELECT 
                    idventa
                FROM ventas
                WHERE fk_idproducto = $idproducto";

        if (!$resultado = $mysqli->query($sql)) {
            printf("Error en query: %s\n", $mysqli->error . " " . $sql);
        }

        $aResultado = array();
        if($resultado){

            while($fila = $resultado->fetch_assoc()){
                $entidadAux = new Venta();
                $entidadAux->idventa = $fila["idventa"];
                $aResultado[] = $entidadAux;
            }
        }

        $mysqli->close();
        return $aResultado;
    }

}

?>