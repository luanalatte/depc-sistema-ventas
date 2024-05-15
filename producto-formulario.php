<?php

include_once "config.php";
include_once "entidades/tipoproducto.php";
include_once "entidades/producto.php";
include_once "entidades/venta.php";

$pg = "Edición de producto";

$producto = new Producto();
$producto->cargarFormulario($_REQUEST);

if ($_POST) {
    if (isset($_POST["btnGuardar"])) {
        if (isset($_FILES["fileImagen"]) && $_FILES["fileImagen"]["error"] == UPLOAD_ERR_OK) {

            $mime = mime_content_type($_FILES["fileImagen"]["tmp_name"]);
            $ext = strtolower(pathinfo($_FILES["fileImagen"]["name"], PATHINFO_EXTENSION));
            if (str_starts_with($mime, "image/") && in_array($ext, ["jpg", "jpeg", "png", "webp", "gif"])) {
                $nombreImagen = date("Ymdhmsi") . rand(1000, 9999) . "." . $ext;
                if (!move_uploaded_file($_FILES["fileImagen"]["tmp_name"], "img/productos/$nombreImagen")) {
                    unset($nombreImagen);
                }
            }
        }

        if (isset($_GET["id"]) && $_GET["id"] > 0) {
            // NOTE: cargando desde DB solo para eliminar la imagen?
            $productoOld = new Producto();
            $productoOld->idproducto = $producto->idproducto;
            $productoOld->obtenerPorId();
            if (isset($nombreImagen)) {
                $productoOld->eliminarImagen();
                $producto->imagen = $nombreImagen;
            } else {
                $producto->imagen = $productoOld->imagen;
            }

            $producto->actualizar();
        } else {
            if (isset($nombreImagen)) {
                $producto->imagen = $nombreImagen;
            }

            $producto->insertar();
        }

        $msg["texto"] = "Guardado correctamente";
        $msg["codigo"] = "alert-success";
    } else if (isset($_POST["btnBorrar"])) {
        if (Venta::obtenerVentasPorProducto($producto->idproducto)) {
            $msg["texto"] = "No se puede eliminar un producto con ventas asociadas.";
            $msg["codigo"] = "alert-danger";
        } else {
            // NOTE: cargando desde DB solo para eliminar la imagen?
            $producto->obtenerPorId();
            $producto->eliminarImagen();
            $producto->eliminar();
            header("Location: producto-listado.php");
        }
    }
}

if (isset($_GET["id"]) && $_GET["id"] > 0) {
    $producto->obtenerPorId();
}

$aTipoProductos = TipoProducto::obtenerTodos();

include_once "header.php";
?>
        <!-- Begin Page Content -->
        <div class="container-fluid">

          <!-- Page Heading -->
          <h1 class="h3 mb-4 text-gray-800">Producto</h1>
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
                    <a href="producto-listado.php" class="btn btn-primary mr-2">Listado</a>
                    <a href="producto-formulario.php" class="btn btn-primary mr-2">Nuevo</a>
                    <button type="submit" class="btn btn-success mr-2" id="btnGuardar" name="btnGuardar">Guardar</button>
                    <button type="submit" class="btn btn-danger" id="btnBorrar" name="btnBorrar">Borrar</button>
                </div>
            </div>
            <div class="row">
                <div class="col-6 form-group">
                    <label for="txtNombre">Nombre:</label>
                    <input type="text" required class="form-control" name="txtNombre" id="txtNombre" value="<?= $producto->nombre ?>">
                </div>
                <div class="col-6 form-group">
                    <label for="lstTipoProducto">Tipo de producto:</label>
                    <select class="form-control" name="lstTipoProducto" id="lstTipoProducto">
                        <option value="" selected disabled>Seleccionar</option>
                        <?php foreach($aTipoProductos as $tipo): ?>
                            <option value="<?= $tipo->idtipoproducto ?>"<?= $tipo->idtipoproducto == $producto->fk_idtipoproducto ? " selected" : "" ?>><?= $tipo->nombre ?></option>
                        <?php endforeach; ?>
                    </select>
                </div>
                <div class="col-6 form-group">
                    <label for="txtCantidad">Cantidad:</label>
                    <input type="number" required class="form-control" name="txtCantidad" id="txtCantidad" value="<?= $producto->cantidad ?>">
                </div>
                <div class="col-6 form-group">
                    <label for="txtPrecio">Precio:</label>
                    <input type="number" required class="form-control" name="txtPrecio" id="txtPrecio" value="<?= $producto->precio ?>">
                </div>
                <div class="col-12 form-group">
                    <label for="txtDescripcion">Descripción:</label>
                    <textarea name="txtDescripcion" id="txtDescripcion" class="form-control"><?= $producto->descripcion ?></textarea>
                    <script>
                    ClassicEditor
                        .create( document.querySelector( '#txtDescripcion' ) )
                        .catch( error => {
                            console.error( error );
                        } );
                    </script>
                </div>
                <div class="col-6 form-group">
                    <label for="fileImagen">Imagen:</label>
                    <input class="form-control mb-2" type="file" name="fileImagen" id="fileImagen" accept=".jpg, .jpeg, .png, .webp, .gif">
                    <?php if($producto->imagen): ?>
                        <img class="img-thumbnail" src="img/productos/<?= $producto->imagen ?>">
                    <?php endif; ?>
                </div>
            </div>

        </div>
        <!-- /.container-fluid -->

      </div>
      <!-- End of Main Content -->

<?php include_once "footer.php";?>