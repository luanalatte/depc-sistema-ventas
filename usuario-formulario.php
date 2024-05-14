<?php

include_once "config.php";
include_once "entidades/usuario.php";

$pg = "Edición de usuario";

$usuario = new Usuario();
$usuario->cargarFormulario($_REQUEST);

if ($_POST) {
    if (isset($_POST["btnGuardar"])) {
        if (isset($_GET["id"]) && $_GET["id"] > 0) {
            $usuario->actualizar();
            $msg["texto"] = "Guardado correctamente";
            $msg["codigo"] = "alert-success";
        } else {
            if (isset($_POST["txtClave"]) && $_POST["txtClave"]) {
                $usuario->insertar();
                $msg["texto"] = "Guardado correctamente";
                $msg["codigo"] = "alert-success";
            } else {
                $msg["texto"] = "Algo salió mal. Por favor verifica los datos.";
                $msg["codigo"] = "alert-danger";
            }
        }
    } else if (isset($_POST["btnBorrar"])) {
        $usuario->eliminar();
        header("Location: usuario-listado.php");
    }
}

if (isset($_GET["id"]) && $_GET["id"] > 0) {
    $usuario->obtenerPorId();
}

include_once "header.php";
?>
        <!-- Begin Page Content -->
        <div class="container-fluid">

          <!-- Page Heading -->
          <h1 class="h3 mb-4 text-gray-800">Usuario</h1>
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
                    <a href="usuario-listado.php" class="btn btn-primary mr-2">Listado</a>
                    <a href="usuario-formulario.php" class="btn btn-primary mr-2">Nuevo</a>
                    <button type="submit" class="btn btn-success mr-2" id="btnGuardar" name="btnGuardar">Guardar</button>
                    <button type="submit" class="btn btn-danger" id="btnBorrar" name="btnBorrar">Borrar</button>
                </div>
            </div>
            <div class="row">
                <div class="col-6 form-group">
                    <label for="txtUsuario">Usuario:</label>
                    <input type="text" required class="form-control" name="txtUsuario" id="txtUsuario" value="<?= $usuario->usuario ?>">
                </div>
                <div class="col-6 form-group">
                    <label for="txtNombre">Nombre:</label>
                    <input type="text" required class="form-control" name="txtNombre" id="txtNombre" value="<?= $usuario->nombre ?>">
                </div>
                <div class="col-6 form-group">
                    <label for="txtApellido">Apellido:</label>
                    <input type="text" required class="form-control" name="txtApellido" id="txtApellido" value="<?= $usuario->apellido ?>">
                </div>
                <div class="col-6 form-group">
                    <label for="txtCorreo">Correo:</label>
                    <input type="mail" required class="form-control" name="txtCorreo" id="txtCorreo" value="<?= $usuario->correo ?>">
                </div>
                <div class="col-6 form-group">
                    <label for="txtClave">Clave:</label>
                    <?php if(isset($_GET["id"]) && $_GET["id"] > 0): ?>
                        <input type="password" class="form-control" name="txtClave" id="txtClave" value="" placeholder="Ingrese un valor para modificar la clave existente">
                    <?php else: ?>
                        <input type="password" required class="form-control" name="txtClave" id="txtClave" value="">
                    <?php endif; ?>
                </div>
            </div>

        </div>
        <!-- /.container-fluid -->

      </div>
      <!-- End of Main Content -->

<?php include_once "footer.php";?>