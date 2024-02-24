<!--ACTIVAR INVENTARIO-->
<div class="modal fade shadow-lg rounded rounded-5" id="cambiarestado" tabindex="-1" aria-labelledby="exampleModalLabel" data-bs-backdrop="static" aria-hidden="true">
    <div class="modal-dialog modal-sm">
        <div class="modal-content">
            <div class="modal-header">
                <h4 class="modal-title fs-5 text-uppercase" id="exampleModalLabel">Activar inventario</h4>
                <button type="button" id="btn-close-modal" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <form onsubmit="return false;" >
                    <input type="hidden" class="form-control" id="idproducto" placeholder="Buscar productos">
                    <div class="row">
                        <div class="col-md-12 form-group">
                            <label class="label-form col-md-2 mb-2" for="cantidad-inventario">Cantidad</label>
                            <input class="form-control text-center input-form" id="cantidad-inventario" name="cantidad-inventario" placeholder="Producto" type="number" value="0" />
                            <div id="cantidad-inventario-errors">
                            </div>
                        </div>
                    </div>
                    <div class="row mt-3">
                        <div class="col-md-12 text-end" id="btn">
                            <button class="button-file text-uppercase" onclick="estadoInventario()" id="btn-pago">Activar Inventario <span class="fas fa-pencil-alt"></span></button>
                        </div>	
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>

<!--CAMBIAR CANTIDAD-->
<div class="modal fade shadow-lg rounded rounded-5" id="cambiarcantidad" tabindex="-1" aria-labelledby="exampleModalLabel" data-bs-backdrop="static" aria-hidden="true">
    <div class="modal-dialog modal-sm">
        <div class="modal-content">
            <div class="modal-header">
                <h4 class="modal-title fs-5 text-uppercase" id="exampleModalLabel">Modificar cantidad</h4>
                <button type="button" id="btn-close-modal" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <form onsubmit="return false;" >
                    <input type="hidden" class="form-control" id="idproducto-inventario" placeholder="Buscar productos">
                    <div class="row">
                        <div class="col-md-12 form-group">
                            <label class="label-form mb-2" for="tipo">Cantidad</label>
                            <input class="form-control text-center input-form" id="cantidad-nueva" name="producto" placeholder="Cantidad de producto" type="number" value="0" />
                            <div id="cantidad-nueva-errors">
                            </div>
                        </div>
                    </div>
                    <div class="row mt-3">
                        <div class="col-md-12 text-end" id="btn">
                            <button class="button-file text-uppercase" onclick="cambiarCantidad()" id="btn-pago">Cambiar Cantidad <span class="fas fa-pencil-alt"></span></button>
                        </div>	
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>