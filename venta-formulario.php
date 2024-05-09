<?php
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
                    <label for="lstDia">Fecha y hora:</label>
                    <div class="row">
                        <div class="col-3">
                            <select class="form-control" name="lstDia" id="lstDia">
                                <option value="" selected disabled>DD</option>
                                <?php for ($i=1; $i <= 31; $i++): ?>                        
                                    <option value="<?= $i ?>"><?= $i ?></option>
                                <?php endfor; ?>
                            </select>
                        </div>
                        <div class="col-3">
                            <select class="form-control" name="lstMes" id="lstMes">
                                <?php for ($i=1; $i <= 12; $i++): ?>                        
                                    <option value="<?= $i ?>"><?= $i ?></option>
                                <?php endfor; ?>
                            </select>
                        </div>
                        <div class="col-3">
                            <select class="form-control" name="lstAnio" id="lstAnio">
                                
                            </select>
                        </div>
                        <div class="col-3">
                            <select class="form-control" name="lstHora" id="lstHora">
                                
                            </select>
                        </div>
                    </div>
                </div>
            </div>
            <div class="row">
                <div class="col-6 form-group">
                    <label for="lstCliente">Cliente:</label>
                    <select required name="lstCliente" id="lstCliente" class="form-control">
                        <option value="" selected disabled>Seleccionar</option>
                    </select>
                </div>
                <div class="col-6 form-group">
                    <label for="lstProducto">Producto:</label>
                    <select required name="lstProducto" id="lstProducto" class="form-control">
                        <option value="" selected disabled>Seleccionar</option>
                    </select>
                </div>
                <div class="col-6 form-group">
                    <label for="txtPrecioUnitario">Precio unitario:</label>
                    <input type="number" disabled class="form-control" name="txtPrecioUnitario" id="txtPrecioUnitario" value="">
                </div>
                <div class="col-6 form-group">
                    <label for="txtCantidad">Cantidad:</label>
                    <input type="number" required class="form-control" name="txtCantidad" id="txtCantidad" value="0">
                </div>
                <div class="col-6 form-group">
                    <label for="txtTotal">Total:</label>
                    <input type="number" required class="form-control" name="txtTotal" id="txtTotal" value="0">
                </div>
            </div>

        </div>
        <!-- /.container-fluid -->

      </div>
      <!-- End of Main Content -->

<?php include_once "footer.php";?>