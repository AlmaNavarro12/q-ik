<div class="col-md-12">
        <div class="titulo-lista" id="contenedor-titulo-form-factura">Reporte sobre corte de caja </div>
</div>
<div id="form-factura" class="mb-3 px-3">
    <div id="div-space">
    </div>
    <div class="div-form px-5 py-4 border border-secondary-subtle">
        <div class="row" id="not-timbre">
        </div>
        <div class="row">
            <div class="col-md-3 py-1" id="div-usuario">
                <label class="label-form text-right" for="usuario-corte">Usuario</label> <label class="mark-required text-danger fw-bold">*</label>
                <div class="form-group">
                    <select class="form-select text-center input-form" id="usuario-corte" name="usuario-corte" onchange="buscarCorte();">
                        <option value="" id="option-default-usuario">- - - -</option>
                        <optgroup id="usuarios" class="contenedor-usuarios text-start"> </optgroup>
                    </select>
                    <div id="usuario-corte-errors" class="small"></div>
                </div>
            </div>
            <div class="col-md-3 py-1" id="div-fecha">
                <label class="label-form text-right" for="fecha-creacion">Fecha</label> <label class="mark-required text-danger fw-bold">*</label>
                <div class="form-group">
                    <input class="form-control input-form text-center" id="fecha-creacion" name="fecha-creacion" type="date" onchange="buscarCorte();"/>
                    <div id="fecha-creacion-errors" class="small"></div>
                </div>
            </div>
            <div class="col-md-3 py-1" id="div-fecha">
                <label class="label-form text-right" for="horainicio">Hora inicio</label> <label class="mark-required text-danger fw-bold">*</label>
                <div class="form-group">
                    <input class="form-control input-form text-center" id="horainicio" name="horainicio" type="time" onchange="buscarCorte();"/>
                    <div id="horainicio-errors" class="small"></div>
                </div>
            </div><div class="col-md-3 py-1" id="div-fecha">
                <label class="label-form text-right" for="horafin">Hora fin</label> <label class="mark-required text-danger fw-bold">*</label>
                <div class="form-group">
                    <input class="form-control input-form text-center" id="horafin" name="horafin" type="time" onchange="buscarCorte();"/>
                    <div id="horafin-errors" class="small"></div>
                </div>
            </div>
        </div>
        <div class="row mt-4">
            <div class="col-6 text-center">
                <label class="label-sub-tit fw-semibold fs-5 mt-4">VENTAS TOTALES: </label> <label class="label-sub-tit fw-semibold fs-5 mt-4" id="lbl-ventas">$0.00</label>
                <input type="hidden" name="ventas_totales" id="ventas_totales">
            </div>
            <div class="col-6 text-center">
                <label class="label-sub-tit fw-semibold fs-5 mt-4">
                            <div class="new-tooltip icon tip">
                                <span class="fas fa-question-circle small text-primary-emphasis"></span>
                                <span class="tiptext">El total de ganancias solo hace referencia a las ventas realizadas en Punto de Venta.</span>
                            </div>
                    GANANCIAS: </label> <label class="label-sub-tit fw-semibold fs-5 mt-4" id="lbl-ganancia">$0.00</label>
                <input type="hidden" name="ganancias_totales" id="ganancias_totales">
            </div>
        </div>
        <div class="row row-cols-1 row-cols-sm-2 row-cols-md-3 g-3 mh-100">
            <div class="col">
                <div class="card shadow border border-dark-subtle" style="height: 100%;">
                    <div class="card-body text-center">
                        <label class="label-sub-tit fw-semibold fs-5 text-uppercase text-center">Entradas de efectivo </label>
                        <div id="tab-entradas">
                            <ul class='list-group mb-3'>
                                <li class='list-group-item d-flex justify-content-between'>
                                    <span class='fw-bold text-muted'>Total ($)</span>
                                    <strong>$ 0.00</strong>
                                </li>
                            </ul>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col">
                <div class="card shadow border border-dark-subtle" style="height: 100%;">
                    <div class="card-body text-center">
                        <label class="label-sub-tit fw-semibold fs-5 text-uppercase text-center">Salidas de efectivo </label>
                        <div id="tab-salidas">
                            <ul class='list-group mb-3'>
                                <li class='list-group-item d-flex justify-content-between'>
                                    <span class='fw-bold text-muted'>Total ($)</span>
                                    <strong>$ 0.00</strong>
                                </li>
                            </ul>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col">
                <div class="card shadow border border-dark-subtle" style="height: 100%;">
                    <div class="card-body text-center">
                        <label class="label-sub-tit fw-semibold fs-5 text-uppercase text-center">Dinero en caja </label>
                        <div id="tab-caja">
                            <ul class='list-group mb-3'>
                                <li class='list-group-item d-flex justify-content-between'>
                                    <span class='fw-bold text-muted'>Total ($)</span>
                                    <strong>$ 0.00</strong>
                                </li>
                            </ul>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="row mt-5">
            <div class="col-md-8 py-1 mt-2">
               <div id="pago_factura" style="display: none;">
               <div class="form-group d-flex aling-items-center">
                    <input class="input-check me-2" type="checkbox" id="pago" checked name="pago" onchange="buscarCorte();">
                    <label class="ms-3 text-uppercase label-sub-tit fw-semibold fs-6" for="pago">¿Incluir pagos de facturas?</label>
                    <div id="pago-errors">
                    </div>
                </div>
               </div>
            </div>
            <div class="col-md-4 text-end" id="btns">
                <button class="button-modal col-12" onclick="generarPDFCorte();">
                    <i class="fas fa-save"></i>
                    Imprimir
                </button>
            </div>
        </div>
    </div>
</div>
<script src="js/scriptreportes.js"></script>