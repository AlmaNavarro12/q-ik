<!--F2 RGEISTRAR CANTIDAD DE ENTRADAS Y SALIDAS-->
<div class="modal fade shadow-lg rounded rounded-5" id="modal-entradas" tabindex="-1" aria-labelledby="exampleModalLabel" data-bs-backdrop="static" aria-hidden="true">
    <div class="modal-dialog modal-sm">
        <div class="modal-content">
            <div class="modal-header">
                <h4 class="modal-title fs-5" id="label-ingresos">Registrar entrada de efectivo</h4>
                <button type="button" id="btn-close-modal" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <form id="form-cobrar" onsubmit="return false;">
                    <input type="hidden" id="type-movimiento" />
                    <div class="row py-2">
                        <div class="form-group">
                            <label class="label-form text-start" for="monto-entrada">Cantidad</label> <label class="mark-required text-danger fw-bold">*</label>
                            <input class="input-form text-center form-control" type="text" placeholder="Monto" id="monto-entrada" oninput="this.value = this.value.replace(/[^0-9.]/g, '').replace(/(\..*)\./g, '$1');" />
                            <div id="monto-entrada-errors"></div>
                        </div>
                    </div>
                    <div class="row py-2">
                        <div class="form-group">
                            <label class="label-form text-start" for="concepto-entrada">Concepto</label> <label class="mark-required text-danger fw-bold">*</label>
                            <input class="input-form text-center form-control" type="text" placeholder="Concepto" id="concepto-entrada" />
                            <div id="concepto-entrada-errors"></div>
                        </div>
                    </div>
                    <div class="row py-2">
                        <div class="text-end" id="btns">
                            <button class="button-file text-uppercase fw-semibold" onclick="registrarEntrada()" id="btn-form-entrada">Registrar <span class="fas fa-dollar-sign"></span></button>
                        </div>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>

<!--CAMBIAR LA CANTIDAD DE PRODUCTO EN EL TICKET-->
<div class="modal fade shadow-lg rounded rounded-5" id="modal-cantidad" tabindex="-1" aria-labelledby="exampleModalLabel">
    <div class="modal-dialog modal-sm">
        <div class="modal-content">
            <div class="modal-header">
                <h4 class="modal-title fs-5" id="exampleModalLabel">Editar cantidad</h4>
                <button type="button" id="btn-close-modal" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <input type="hidden" class="form-control" id="idcant">
                <div class="row">
                    <input id="precio-orig" name="precio-orig" type="hidden" />
                    <div class="form-group col-md-12">
                        <label class="label-form text-start mb-1" for="cantidad-producto">Cantidad</label>
                        <div class="input-group">
                            <input class="form-control text-center input-form" id="cantidad-producto" name="cantidad-producto" placeholder="Cantidad" type="number" oninput="calcularPrecio();" />
                        </div>

                        <label class="label-form text-start mb-1" for="precio-prod">Precio</label>
                        <div class="input-group">
                            <input class="form-control text-center input-form" id="precio-prod" name="precio-prod" placeholder="Cantidad" type="number" oninput="calcularCantidad();" />
                        </div>
                    </div>
                    <div id="precio-prod-errors"></div>
                </div>

                <div class="row mt-3">
                    <div class="text-end">
                        <button class="button-modal" onclick="actualizarCantidad()" id="btn-cantidad">Actualizar <span class="fas fa-dollar-sign"></span></button>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!--MODAL PARA COBRAR TICKET-->
<div class="modal fade shadow-lg rounded rounded-5" id="modal-cobrar" role="dialog" aria-labelledby="myModalLabel" data-bs-backdrop="static" aria-hidden="true">
    <div class="modal-dialog modal-lg" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h4 class="modal-title fs-5 fw-bold" id="label-nuevo-producto">COBRAR</h4>
                <button type="button" id="btn-close-modal" class="btn-close" data-bs-dismiss="modal" onclick="cerrarValores();" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <div class="row">
                    <table class="table table-hover table-condensed table-responsive row-venta">
                        <tbody>
                            <tr class="text-center border border-light">
                                <td>
                                    <label class="titulo-lista fs-4 fw-medium">TOTAL A COBRAR:</label>
                                </td>
                                <td class='text-end'>
                                    <button class="button-modal col-md-12" onclick="validarProductosVenta(1);" id="btn-print"><span class="fas fa-print"></span> Cobrar e imprimir ticket </button>
                                </td>
                            </tr>
                            <tr class="text-center border border-light">
                                <td>
                                    <label class="titulo-lista fs-1 fw-semibold" id="label-total">$ 0.00</label>
                                    <input id="total-cobrar" name="total-cobrar" type="hidden" />
                                </td>
                                <td class='text-end'>
                                    <button class="button-modal col-md-12" onclick="validarProductosVenta(0);" id="btn-form-reg"><span class="fas fa-dollar-sign"></span> Cobrar sin imprimir ticket </button>
                                </td>
                            </tr>
                            <tr class="text-center border border-light">
                                <td>
                                </td>
                                <td class='text-end'>
                                    <button class="button-modal col-md-12" onclick="cerrarTicket();" id="btn-form-cancelar"><span class="fas fa-times"></span> Cancelar Ticket </button>
                                </td>
                            </tr>
                            <tr class="border border-light">
                                <td>
                                    <label class="text-center fw-bold text-muted mb-2">Forma de Pago:</label>
                                    <div id="btn-fmpago" class="row d-flex justify-content-center">
                                        <button pago-tab="cash" class="button-venta col-md-3 me-3 button-venta-active">Efectivo <span id="cash-icon"></span></button>
                                        <button pago-tab="card" class="button-venta col-md-3 me-3">Tarjeta <span id="card-icon"></span></button>
                                        <button pago-tab="val" class="button-venta col-md-3 me-3">Vales <span id="vales-icon"></span></button>
                                    </div>
                                </td>
                                <td class="text-center">
                                    <p><label class="label-sub text-center">Total de articulos:</label></p>
                                    <label id="label-art" class="titulo-lista fs-2 fw-medium"></label>
                                </td>
                            </tr>
                            <tr class="border border-light">
                                <td class="text-start">
                                    <div class="div-forma" id="cash-div">
                                        <label class="text-start fw-bold text-muted mb-2">Pago con:</label>
                                        <input class="input-form text-center form-control mt-0" id="monto-pagado" name="monto-pagado" placeholder="Cantidad pagada" type="text" oninput="this.value = this.value.replace(/[^0-9.]/g, '').replace(/(\..*)\./g, '$1'); calcularCambio();" />
                                    </div>
                                    <div class="div-forma" id="ref-div" style="display: none;">
                                        <label class="text-start fw-bold text-muted mb-2">Referencia:</label>
                                        <input class="input-form text-center form-control" id="referencia-pago" name="referencia-pago" placeholder="No. Referencia de la transacción" type="text" />
                                        <div class="col-md-12 mt-3" id="tipo-tarjeta">
                                            <fieldset>
                                                <div class="form-group ps-5">
                                                    <div class="row d-flex justify-content-center">
                                                        <div class="col-sm-6">
                                                            <div class="form-group">
                                                                <label class="label-radio d-flex align-items-center">
                                                                    <input class="input-radio me-2" type="radio" id="tarjetacredito" name="tarjeta" value="credito" checked=""> <label class="text-start fw-bold text-muted" for="tarjetacredito">Crédito</label>
                                                                </label>
                                                            </div>
                                                        </div>
                                                        <div class="col-sm-6">
                                                            <div class="form-group">
                                                                <label class="label-radio d-flex align-items-center">
                                                                    <input class="input-radio me-2" type="radio" id="tarjetadebito" name="tarjeta" value="debito"> <label class="text-start fw-bold text-muted" for="tarjetadebito">Débito</label>
                                                                </label>
                                                            </div>
                                                        </div>
                                                    </div>
                                                </div>
                                            </fieldset>
                                        </div>
                                        <div id="tarjeta-errors"></div>
                                    </div>
                                </td>
                                <td class="text-center">
                                    <label for="ChkDescuento" class="fw-bold text-center mb-2 text-uppercase" style="color: #17177C;">
                                        Descuento: <span id="Spndescuento" class="far fa-square fs-6"></span>
                                        <input id="ChkDescuento" type="checkbox" value="1" onclick="habilitarDescuento()" style="display: none;">
                                    </label>
                                    <div id="groupDesc" style="display: none;">
                                        <div class="input-group">
                                            <input type="number" class="input-form text-center form-control" id="PercentDescuento" min="0" max="100" value="5">
                                            <div class="input-group-text">%</div>
                                        </div>
                                        <div id="PercentDescuento-errors" class='text-start'></div>
                                    </div>
                                </td>
                            </tr>
                            <tr class="border border-light">
                                <td class="text-center mt-2">
                                    <div class="div-forma" id="cambio-label">
                                        <label class="label-form me-3">Cambio:</label>
                                        <label id="label-cambio" class="label-cambio text-center fw-bold">$0.00</label>
                                    </div>
                                </td>
                                <td class="text-center">
                                    <div class="div-forma" id="cambio-descuento">
                                        <label class="label-form text-center">Descuento:</label>
                                        <label id="label-descuento" class="titulo-lista fs-4 fw-semibold text-center"></label>
                                    </div>
                                    <input type="hidden" id="input-descuento" value="0" />
                                    <input type="hidden" id="input-descuento-original" value="0" />
                                    <input type="hidden" id="total-original" value="0" />
                                </td>
                            </tr>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>

<!--REGISTRAR DINERO INCIAL EN CAJA-->
<div class="modal fade shadow-lg rounded rounded-5" id="modal-dincaja" data-bs-backdrop="static" data-bs-keyboard="false" tabindex="-1" aria-labelledby="staticBackdropLabel" aria-hidden="true">
    <div class="modal-dialog modal-sm">
        <div class="modal-content">
            <div class="modal-header">
                <h4 class="modal-title fs-5" id="label-ingresos">Registrar cantidad inicial</h4>
            </div>
            <div class="modal-body">
                <form id="form-cobrar" onsubmit="return false;">
                    <div class="row">
                        <label class="label-form text-start mb-1" for="monto-inicial">Dinero en caja <label class="mark-required text-danger fw-bold">*</label></label>
                        <div class="form-group">
                            <input class="input-form text-center form-control" type="text" placeholder="Monto" id="monto-inicial" name="monto-inicial" oninput="this.value = this.value.replace(/[^0-9.]/g, '').replace(/(\..*)\./g, '$1');" />
                            <div id="monto-inicial-errors"></div>
                        </div>
                    </div>
                    <div class="row mt-3">
                        <div class="text-end" id="btns">
                            <button class="button-modal" onclick="registrarDineroInicial()" id="btn-form-inicial">Registrar <span class="fas fa-dollar-sign"></span></button>
                        </div>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>

<!-- Modal para consultar precios -->
<div class="modal fade shadow-lg rounded rounded-5" id="modal-consulta-precios" tabindex="-1" aria-labelledby="exampleModalLabel" data-bs-backdrop="static" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h4 class="modal-title fs-5" id="label-ingresos">Consulta precio</h4>
                <button type="button" id="btn-close-modal" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body pb-5">
                <label class="label-sub">Código del producto</label>
                <div class="col-12">
                    <div class="row">
                        <div class="col-md-8 py-1 text-start">
                            <input type="text" class="form-control text-center input-form col-12" id="buscar-producto-precio" placeholder="Buscar codigo" oninput="aucompletarBuscarProducto();" />
                        </div>
                        <div class="col-md-4 py-1 text-end">
                            <div class="space-div"></div>
                            <button id="btn-nuevo-producto" type="button" class="button-file text-uppercase" onclick="buscarPrecioProducto();">
                                <i class="fas fa-search"></i> Buscar Prod
                            </button>
                        </div>
                    </div>
                </div>
                <div id="CollapsePrecio" class="collapse">
                    <div class="row text-center" style="margin-top: 2rem;">
                        <div class="col-md-6">
                            <font style="color: #17177C; font-weight: bold;">CÓDIGO:</font>
                            <h4 class="text-primary-emphasis" style="margin: 0;"><span id="SpnCodigo">086</span></h4>
                        </div>
                        <div class="col-md-6">
                            <font style="color: #17177C; font-weight: bold;">PRODUCTO:</font>
                            <h4 class="text-primary-emphasis" style="margin: 0;"><span id="SpnProd">XX0X0X0X</span></h4>
                        </div>
                    </div>
                    <div class="row text-center col-12 mx-0 mt-3" id="impuestos_modal">
                        <div class="col-md-4">
                            <font style="color: #17177C; font-weight: bold;">PRECIO:</font>
                            <h4 style="margin: 0;">$<span id="SpnPrec">86.16</span></h4>
                        </div>
                        <div class="col-md-4">
                            <font style="color: #17177C; font-weight: bold;">IVA:</font>
                            <h4 style="margin: 0;">$<span id="SpnIva">23.04</span></h4>
                        </div>
                    </div>
                    <div class="row col-12" id="total">
                        <div class="col-md-12 text-center mt-3">
                            <font style="color: #17177C; font-weight: bold;">TOTAL:</font>
                            <h1 class="text-primary-emphasis" style="margin: 0;">$<span id="total">110.00</span></h1>
                        </div>
                    </div>
                    <div class="row mt-3">
                        <div class="col-md-6">
                            <label class="label-sub">Cantidad</label>
                            <input type="number" class="form-control text-center input-form" id="cantidad-producto-precio" value="1" />
                        </div>
                        <div class="col-md-6 text-center">
                            <label class="label-sub">&nbsp;</label>
                            <button type="button" class="button-file col-12" onclick="agregarProductoTicket()">
                                <i class="fas fa-file me-2"></i>Pasar a ticket
                            </button>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!--MODAL PARA VERIFICAR SUPERVISOR-->
<div class="modal fade shadow-lg rounded rounded-5" id="modal-supervisor" tabindex="-1" aria-labelledby="exampleModalLabel" data-bs-backdrop="static" aria-hidden="true">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h4 class="modal-title fs-5" id="label-ingresos">Verificación de supervisor</h4>
                <button type="button" id="btn-close-modal" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">

                <div class="row py-2">
                    <div class="col-md-6">
                        <label class="label-form text-right" for="supervisor">Nombre de supervisor</label> <label class="mark-required text-danger fw-bold">*</label>
                        <div class="input-group mb-3">
                            <span class="input-group-text" id="basic-addon1"><i class="text-muted fas fa-user"></i></span>
                            <input type="text" class="form-control input-form" id="supervisor" name="supervisor" placeholder="Nombre de usuario" autocomplete="new-text">
                            <div id="supervisor-errors"></div>
                        </div>
                    </div>
                    <div class="col-md-6">
                        <label class="label-form text-right" for="contrasena">Contraseña de supervisor</label> <label class="mark-required text-danger fw-bold">*</label>
                        <div class="input-group mb-3">
                            <span class="input-group-text" id="basic-addon2"><i class="text-muted fas fa-lock"></i></span>
                            <input type="password" class="form-control input-form" id="contrasena" name="contrasena" placeholder="Contraseña de usuario" autocomplete="new-password">
                            <div id="contrasena-errors"></div>
                        </div>
                    </div>
                </div>
                <div id="complemento-corte" style="display: none;">
                    <div class="mb-3">
                        <label for="comentarios" class="form-label">Comentarios</label>
                        <textarea class="form-control input-form" id="comentarios" placeholder="Comentarios..." rows="3" style="height: 100px;"></textarea>
                    </div>
                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label for="total_sobrantes" class="form-label">Dinero sobrante </label>
                            <input type="text" class="form-control input-form" id="total_sobrantes" placeholder="0" oninput="this.value = this.value.replace(/[^0-9.]/g, '').replace(/(\..*)\./g, '$1');">
                        </div>
                        <div class="col-md-6 mb-3">
                            <label for="total_faltantes" class="form-label">Dinero faltante</label>
                            <input type="text" class="form-control input-form" id="total_faltantes" placeholder="0" oninput="this.value = this.value.replace(/[^0-9.]/g, '').replace(/(\..*)\./g, '$1');">
                        </div>
                    </div>
                </div>
                <div class="row py-1 justify-content-between">
                    <div class="col-md-6">
                        <div class="form-group d-flex aling-items-center">
                            <input class="input-check" id="comentarios-extras" name="comentarios-extras" type="checkbox">
                            <label class="label-form ms-3" for="comentarios-extras">¿Deseas ingresar algún comentario?</label>
                            <div id="comentarios-extras-errors">
                            </div>
                        </div>
                    </div>
                    <div class="col-md-4" id="btns">
                        <button class="button-file text-uppercase fw-bold col-12" onclick="validarSupervisor()" id="btn-form-entrada">Registrar corte <span class="fas fa-save"></span></button>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!--MODAL PARA CANCELAR VENTA-->
<div class="modal fade shadow-lg rounded rounded-5" id="modal-cancelacion" tabindex="-1" aria-labelledby="exampleModalLabel" data-bs-backdrop="static" aria-hidden="true">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h4 class="modal-title fs-5 fw-bold text-uppercase" id="label-ingresos">Cancelación de ticket</h4>
                <button type="button" id="btn-close-modal" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <div class="row py-2">
                    <div class="col-md-6 mb-3">
                        <label class="label-form text-right" for="nombre-vendedor">Nombre de usuario</label> <label class="mark-required text-danger fw-bold">*</label>
                        <div class="input-group">
                            <span class="input-group-text" id="basic-addon1"><i class="text-muted fas fa-user"></i></span>
                            <input type="text" class="form-control input-form" id="nombre-vendedor" name="nombre-vendedor" placeholder="Nombre de usuario" autocomplete="new-text">
                        </div>
                        <div id="nombre-vendedor-errors"></div>
                    </div>
                    <div class="col-md-6 mb-3">
                        <label class="label-form text-right" for="contrasena-vendedor">Contraseña de usuario</label> <label class="mark-required text-danger fw-bold">*</label>
                        <div class="input-group">
                            <span class="input-group-text" id="basic-addon2"><i class="text-muted fas fa-lock"></i></span>
                            <input type="password" class="form-control input-form" id="contrasena-vendedor" name="contrasena-vendedor" placeholder="Contraseña de usuario">
                        </div>
                        <div id="contrasena-vendedor-errors"></div>
                    </div>
                </div>
                <div id="complemento-corte">
                    <div class="mb-3">
                        <label for="motivo-cancelar" class="form-label">Motivo <label class="mark-required text-danger fw-bold">*</label></label>
                        <textarea class="form-control input-form" id="motivo-cancelar" placeholder="Motivo..." rows="3" style="height: 100px;"></textarea>
                        <div id="motivo-cancelar-errors"></div>
                    </div>
                </div>
                <div class="col-12 d-flex justify-content-end">
                <div class="col-md-4" id="btns">
                    <input type="hidden" id="idventa" name="idventa"/>
                        <button class="button-file text-uppercase fw-bold col-12" onclick="validarCancelacion()" id="btn-form-entrada">Registrar cancelación <span class="fas fa-save"></span></button>
                </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!--MODAL PARA VER MOTIVO DE CANCELACION-->
<div class="modal fade bs-example-modal-lg" id="cancelaciones" tabindex="-1" role="dialog" aria-labelledby="myModalLabel">
    <div class="modal-dialog modal-lg" role="document">
        <div class="modal-content">
        <div class="modal-header py-0">
                <div class="label-sub fs-5 py-0" id="titulo-alerta">
                    Motivo de cancelación
                </div>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <div id="loader" style="position: absolute;	text-align: center;	top: 55px;	width: 100%;display:none;"></div><!-- Carga gif animado -->
                <div id="datosproducto" class="outer_div" >
                    <div class="row">
                        <table id="cancelaciontabla" class="table table-hover table-condensed table-responsive table-row table-head">

                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>