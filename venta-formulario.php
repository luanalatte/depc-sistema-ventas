<?php

include_once "config.php";
include_once "entidades/cliente.php";
include_once "entidades/producto.php";
include_once "entidades/venta.php";

$pg = "Edición de venta";

$venta = new Venta();
$venta->cargarFormulario($_REQUEST);

if ($_POST) {
    if (isset($_POST["btnGuardar"])) {
        if (isset($_GET["id"]) && $_GET["id"] > 0) {
            $venta->actualizar();
        } else {
            $venta->insertar();
        }

        $msg["texto"] = "Guardado correctamente";
        $msg["codigo"] = "alert-success";
    } else if (isset($_POST["btnBorrar"])) {
        $venta->eliminar();
        header("Location: venta-listado.php");
    }
}

if (isset($_GET["do"]) && $_GET["do"] == "obtenerProducto" && $_GET["id"] && $_GET["id"] > 0) {
    $aProducto = [
        "precio" => Producto::obtenerPrecio($_GET["id"])
    ];
    echo json_encode($aProducto);
    exit;
}
if (isset($_GET["id"]) && $_GET["id"] > 0) {
    $venta->obtenerPorId();
}

$aClientes = Cliente::obtenerTodos();
$aProductos = Producto::obtenerTodos();

include_once "header.php";

?>
        <!-- Begin Page Content -->
        <div class="container-fluid">

          <!-- Page Heading -->
          <h1 class="h3 mb-4 text-gray-800">Venta</h1>
          <?php if (isset($msg)): ?>
            <div class="row">
                <div class="col-12">
                    <div class="alert <?php echo $msg["codigo"]; ?>" role="alert">
                        <?php echo $msg["texto"]; ?>
                    </div>
                </div>
            </div>
            <?php endif;?>
            <div class="row">
                <div class="col-12 mb-3">
                    <a href="venta-listado.php" class="btn btn-primary mr-2">Listado</a>
                    <a href="venta-formulario.php" class="btn btn-primary mr-2">Nuevo</a>
                    <button type="submit" class="btn btn-success mr-2" id="btnGuardar" name="btnGuardar">Guardar</button>
                    <button type="submit" class="btn btn-danger" id="btnBorrar" name="btnBorrar">Borrar</button>
                </div>
            </div>
            <div class="row">
                <div class="col-6 form-group">
                    <label for="txtFechaDia">Fecha y hora:</label>
                    <div class="row">
                        <div class="col-3">
                            <select class="form-control" name="txtFechaDia" id="txtFechaDia">
                                <option value="" disabled <?= $venta->fecha_hora ? "" : " selected" ?>>DD</option>
                                <?php for ($i=1; $i <= 31; $i++): ?>
                                    <option value="<?= $i ?>"
                                    <?php
                                    if ($venta->fecha_hora && $i == date_format(date_create($venta->fecha_hora), "j")) {
                                        echo " selected";
                                    }
                                    ?>
                                    ><?= $i ?></option>
                                <?php endfor; ?>
                            </select>
                        </div>
                        <div class="col-3">
                            <select class="form-control" name="txtFechaMes" id="txtFechaMes">
                                <?php for ($i=1; $i <= 12; $i++): ?>
                                    <option value="<?= $i ?>"
                                    <?php
                                    if ($venta->fecha_hora && $i == date_format(date_create($venta->fecha_hora), "n")) {
                                        echo " selected";
                                    }
                                    ?>
                                    ><?= $i ?></option>
                                <?php endfor; ?>
                            </select>
                        </div>
                        <div class="col-3">
                            <select class="form-control" name="txtFechaAnio" id="txtFechaAnio">
                                <?php for ($i=date('Y'); $i >= 1900; $i--): ?>
                                    <option value="<?= $i ?>"
                                    <?php
                                    if ($venta->fecha_hora && $i == date_format(date_create($venta->fecha_hora), "Y")) {
                                        echo " selected";
                                    }
                                    ?>
                                    ><?= $i ?></option>
                                <?php endfor; ?>
                            </select>
                        </div>
                        <div class="col-3">
                            <input type="time" class="form-control" name="txtHora" id="txtHora" value="<?= $venta->fecha_hora ? date_format(date_create($venta->fecha_hora), "H:i") : "" ?>">
                        </div>
                    </div>
                </div>
            </div>
            <div class="row">
                <div class="col-6 form-group">
                    <label for="lstCliente">Cliente:</label>
                    <select required name="lstCliente" id="lstCliente" class="form-control">
                        <option value="" selected disabled>Seleccionar</option>
                        <?php foreach($aClientes as $cliente): ?>
                            <option value="<?= $cliente->idcliente ?>"<?= $venta->fk_idcliente == $cliente->idcliente ? " selected" : "" ?>><?= $cliente->nombre ?></option>
                        <?php endforeach; ?>
                    </select>
                </div>
                <div class="col-6 form-group">
                    <label for="lstProducto">Producto:</label>
                    <select required name="lstProducto" id="lstProducto" class="form-control" onchange="fObtenerProducto()">
                        <option value="" selected disabled>Seleccionar</option>
                        <?php foreach($aProductos as $producto): ?>
                            <option value="<?= $producto->idproducto ?>"<?= $venta->fk_idproducto == $producto->idproducto ? " selected" : "" ?>><?= $producto->nombre ?></option>
                        <?php endforeach; ?>
                    </select>
                </div>
                <div class="col-6 form-group">
                    <label for="txtPrecioUnitario">Precio unitario:</label>
                    <input onchange="fCalcularTotal()" type="number" readonly class="form-control" name="txtPrecioUnitario" id="txtPrecioUnitario" value="<?= $venta->preciounitario ?>">
                </div>
                <div class="col-6 form-group">
                    <label for="txtCantidad">Cantidad:</label>
                    <input onchange="fCalcularTotal()" type="number" required class="form-control" name="txtCantidad" id="txtCantidad" value="<?= $venta->cantidad ?>">
                </div>
                <div class="col-6 form-group">
                    <label for="txtTotal">Total:</label>
                    <input type="number" required class="form-control" readonly name="txtTotal" id="txtTotal" value="<?= $venta->total ?>">
                </div>
            </div>

        </div>
        <!-- /.container-fluid -->

      </div>
      <!-- End of Main Content -->
<script>
    function fCalcularTotal() {
        let cantidad = $('#txtCantidad').val();
        let precioUnitario = $('#txtPrecioUnitario').val();

        if (cantidad && precioUnitario) {
            $('#txtTotal').val((Number(cantidad) * Number(precioUnitario)).toFixed(2));
        } else {
            $('#txtTotal').val('');
        }
    }

    function fObtenerProducto() {
        idProducto = $("#lstProducto option:selected").val();
        $.ajax({
            type: "GET",
            url: "venta-formulario.php?do=obtenerProducto",
            data: { id:idProducto },
            async: true,
            dataType: "json",
            success: function (respuesta) {
                $('#txtPrecioUnitario').val((Number(respuesta.precio ?? 0)).toFixed(2));
                fCalcularTotal();
            }
        });
    }
</script>
<?php include_once "footer.php";?>