<!--F2 RGEISTRAR CANTIDAD INICIAL DEN CAJA-->
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
                            <label class="label-form text-right" for="monto-entrada">Cantidad</label> <label class="mark-required text-danger fw-bold">*</label>
                            <input class="input-form text-center form-control" type="text" placeholder="Monto" id="monto-entrada" />
                            <div id="monto-entrada-errors"></div>
                        </div>
                    </div>
                    <div class="row py-2">
                        <div class="form-group">
                            <label class="label-form text-right" for="concepto-entrada">Concepto</label> <label class="mark-required text-danger fw-bold">*</label>
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


<div class="modal fade shadow-lg rounded rounded-5" id="modal-cantidad" tabindex="-1" aria-labelledby="exampleModalLabel" data-bs-backdrop="static" aria-hidden="true">
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
                        <label class="label-form text-right" for="cantidad-producto">Cantidad</label>
                        <div class="input-group">
                            <input class="form-control text-center input-form" id="cantidad-producto" name="cantidad-producto" placeholder="Cantidad" type="number" oninput="calcularPrecio();" />
                        </div>

                        <label class="label-form text-right" for="precio-prod">Precio</label>
                        <div class="input-group">
                            <input class="form-control text-center input-form" id="precio-prod" name="precio-prod" placeholder="Cantidad" type="number" oninput="calcularCantidad();" />
                        </div>
                    </div>
                    <div id="precio-prod-errors"></div>
                </div>

                <div class="row">
                    <div class="text-right">
                        <button class="button-modal" onclick="actualizarCantidad()" id="btn-cantidad">Actualizar <span class="glyphicon glyphicon-usd"></span></button>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<div class="modal fade bs-example-modal-lg" id="modal-cobrar" role="dialog" aria-labelledby="myModalLabel">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <button type="button" class="close-modal" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
            <h4 class="titulo-modal" id="label-nuevo-producto">COBRAR</h4>
            <div class="modal-body">
                <!--<form id="form-cobrar" onsubmit="return false;">-->
                <div class="row">
                    <table class="table tab-hover table-condensed table-responsive row-venta ">
                        <tbody>
                            <tr class="text-center">
                                <td>
                                    <label class="titulo-modal">TOTAL A COBRAR:</label>
                                </td>
                                <td>
                                    <button class="button-modal" onclick="validarProductosVenta(1);" id="btn-print"><span class="glyphicon glyphicon-print"></span> Cobrar e imprimir ticket </button>
                                </td>
                            </tr>

                            <tr class="text-center">
                                <td>
                                    <label class="titulo-venta" id="label-total">$ 0.00</label>
                                    <input id="total-cobrar" name="total-cobrar" type="hidden" />
                                </td>
                                <td>
                                    <button class="button-modal" onclick="validarProductosVenta(0);" id="btn-form-reg"><span class="glyphicon glyphicon-usd"></span> Cobrar sin imprimir ticket </button>
                                </td>
                            </tr>

                            <tr class="text-center">
                                <td>
                                </td>
                                <td>
                                    <button class="button-modal" onclick="cerrarTicket();" id="btn-form-cancelar"><span class="glyphicon glyphicon-remove"></span> Cancelar Ticket </button>
                                </td>
                            </tr>

                            <tr class="text-center">
                                <td>
                                    <label class="label-form text-right">Forma de Pago:</label>
                                    <div id="btn-fmpago" class="row">
                                        <button pago-tab="cash" class="button-venta button-venta-active">Efectivo <span id="cash-icon"></span></button>
                                        <button pago-tab="card" class="button-venta">Tarjeta <span id="card-icon"></span></button>
                                        <button pago-tab="val" class="button-venta">Vales <span id="vales-icon"></span></button>
                                    </div>
                                </td>
                                <td>
                                    <p><label class="label-sub text-center">Total de articulos:</label></p>
                                    <label id="label-art" class="label-articulos text-center"></label>
                                </td>
                            </tr>

                            <tr class="text-center">
                                <td>
                                    <div class="div-forma" id="cash-div">
                                        <label class="label-form text-right">Pago con:</label>
                                        <input class="input-form text-center form-control" id="monto-pagado" name="monto-pagado" placeholder="Cantidad pagada" type="number" oninput="calcularCambio();" />
                                    </div>

                                    <div class="div-forma" id="ref-div" hidden>
                                        <label class="label-form text-right">Referencia:</label>
                                        <input class="input-form text-center form-control" id="referencia-pago" name="referencia-pago" placeholder="N° Referencia de la transaccion" type="text" />
                                    </div>
                                </td>
                                <td>
                                    <p>
                                        <label for="ChkDescuento" class="label-sub text-center">
                                            Descuento: <span id="Spndescuento" class="glyphicon glyphicon-unchecked"></span>
                                        </label>
                                        <input id="ChkDescuento" type="checkbox" value="1" onclick="HabilitarDescuento()" style="display: none;">
                                    </p>

                                    <div id="groupDesc" class="input-group" style="display: none;">
                                        <input type="number" class="input-form text-center form-control" id="PercentDescuento" min="0" max="100" value="5">
                                        <div class="input-group-addon">%</div>
                                    </div>
                                    <br>
                                </td>
                            </tr>

                            <tr>
                                <td class="text-center">
                                    <div class="div-forma" id="cambio-label">
                                        <label class="label-form text-center">Cambio:</label>
                                        <label id="label-cambio" class="label-cambio text-center">$0.00</label>
                                    </div>
                                </td>
                                <td class="text-center">
                                    <div class="div-forma">
                                        <label class="label-form text-center">Descuento:</label>
                                        <label id="label-descuento" class="label-articulos text-center"></label>
                                    </div>
                                    <input type="hidden" id="input-descuento" value="0" />
                                    <input type="hidden" id="input-descuento-original" value="0" />
                                    <input type="hidden" id="total-original" value="0" />
                                </td>
                            </tr>
                        </tbody>
                    </table>
                </div>
                <!--</form>-->
            </div>

        </div>
    </div>
</div>

<div class="modal fade bs-example-modal-lg" id="modal-entradas" role="dialog" aria-labelledby="myModalLabel">
    <div class="modal-dialog modal-sm" role="document">
        <div class="modal-content">
            <button type="button" class="close-modal" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
            <h4 class="titulo-modal" id="label-ingresos">Registrar Entrada de efectivo</h4>
            <div class="modal-body">
                <form id="form-cobrar" onsubmit="return false;">
                    <input type="hidden" id="type-movimiento" />
                    <div class="row">
                        <label class="label-form text-right" for="monto-entrada">Cantidad</label> <label class="mark-required text-right">*</label>
                        <div class="form-group">
                            <input class="input-form text-center form-control" type="text" placeholder="Monto" id="monto-entrada" />
                            <div id="monto-entrada-errors"></div>
                        </div>
                    </div>

                    <div class="row">
                        <label class="label-form text-right" for="concepto-entrada">Concepto</label> <label class="mark-required text-right">*</label>
                        <div class="form-group">
                            <input class="input-form text-center form-control" type="text" placeholder="Concepto" id="concepto-entrada" />
                            <div id="concepto-entrada-errors"></div>
                        </div>
                    </div>

                    <div class="row">
                        <div class="text-right" id="btns">
                            <button class="button-modal" onclick="registrarEntrada()" id="btn-form-entrada">Registrar <span class="glyphicon glyphicon-usd"></span></button>
                        </div>
                    </div>
                </form>
            </div>

        </div>
    </div>
</div>

<div class="modal fade bs-example-modal-lg" id="modal-dincaja" role="dialog" aria-labelledby="myModalLabel">
    <div class="modal-dialog modal-sm" role="document">
        <div class="modal-content">

            <h4 class="titulo-modal" id="label-ingresos">Registrar Cantidad Inicial</h4>
            <div class="modal-body">
                <form id="form-cobrar" onsubmit="return false;">
                    <div class="row">
                        <label class="label-form text-right" for="monto-inicial">Dinero en caja</label> <label class="mark-required text-right">*</label>
                        <div class="form-group">
                            <input class="input-form text-center form-control" type="text" placeholder="Monto" id="monto-inicial" name="monto-inicial" />
                            <div id="monto-inicial-errors"></div>
                        </div>
                    </div>

                    <div class="row">
                        <div class="text-right" id="btns">
                            <button class="button-modal" onclick="registrarDineroInicial()" id="btn-form-inicial">Registrar <span class="glyphicon glyphicon-usd"></span></button>
                        </div>
                    </div>
                </form>
            </div>

        </div>
    </div>
</div>

<!-- Modal para consultar precios -->
<div class="modal fade" id="modal-consulta-precios" tabindex="-1" role="dialog" aria-labelledby="myModalLabel">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <button type="button" class="close-modal" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
            <h4 class="titulo-modal" id="myModalLabel">Consulta Precio</h4>
            <div class="modal-body">

                <div class="row">
                    <div class="col-md-8 text-left">
                        <label class="label-sub">Código del producto</label>
                        <input type="text" class="form-control text-center input-form" id="buscar-producto-precio" placeholder="Buscar codigo" oninput="aucompletarBuscarProducto();" />
                    </div>
                    <div class="col-md-4 text-center">
                        <div class="space-div"></div>
                        <button id="btn-nuevo-producto" type="button" class="button-file" onclick="buscarPrecioProducto();">
                            <i class="glyphicon glyphicon-search"></i> Buscar Prod
                        </button>
                    </div>
                </div>

                <div id="CollapsePrecio" class="collapse">

                    <div class="row text-center" style="margin-top: 2rem;">
                        <div class="col-md-6">
                            <font style="color: #17177C; font-weight: bold;">CÓDIGO:</font>
                            <h4 style="margin: 0;"><span id="SpnCodigo">086</span></h4>
                        </div>
                        <div class="col-md-6">
                            <font style="color: #17177C; font-weight: bold;">PRODUCTO:</font>
                            <h4 style="margin: 0;"><span id="SpnProd">Cueritos Grandes</span></h4>
                        </div>
                    </div>

                    <div class="row text-center" style="margin-top: 2rem;" id="impuestos_modal">
                        <div class="col-md-4">
                            <font style="color: #17177C; font-weight: bold;">PRECIO:</font>
                            <h4 style="margin: 0;">$<span id="SpnPrec">86.16</span></h4>
                        </div>
                        <div class="col-md-4">
                            <font style="color: #17177C; font-weight: bold;">IVA:</font>
                            <h4 style="margin: 0;">$<span id="SpnIva">23.04</span></h4>
                        </div>
                        <div class="col-md-4">
                            <font style="color: #17177C; font-weight: bold;">TOTAL:</font>
                            <h1 style="margin: 0;">$<span id="SpnTotal">110.00</span></h1>
                        </div>
                    </div>

                    <div class="row" style="margin-top: 2rem;">
                        <div class="col-md-4"></div>
                        <div class="col-md-4">
                            <label class="label-sub">Cantidad</label>
                            <input type="number" class="form-control text-center input-form" id="cantidad-producto-precio" value="1" />
                        </div>
                        <div class="col-md-4 text-center">
                            <div class="space-div"></div>
                            <button type="button" class="button-file" onclick="agregarProductoTicket()">
                                <i class="glyphicon glyphicon-file"></i>Pasar a ticket
                            </button>
                        </div>
                    </div>

                </div>

            </div>
        </div>
    </div>
</div>