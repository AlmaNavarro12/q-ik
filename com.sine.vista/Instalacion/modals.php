<!--MODAL PARA AGREGAR GPS-->
<div class="modal fade bs-example-modal-lg" id="modal-gps" tabindex="-1" role="dialog" aria-labelledby="titleGPS">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
        <div class="modal-header py-0">
                <div class="label-sub fs-5 py-0">
                Agregar nuevo modelo de GPS
                </div>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <form class="form-horizontal" id="form-gps" onsubmit="return false;">
                <input type="hidden" id="cve-gps" value="0">
                    <div class="form-group">
                    <label class="label-form mb-1 col-md-4 text-right mb-1" for="nuevo-gps">Nombre / Modelo</label><br>
                        <div class="row">
                            <div class="col-md-12 form-group">
                                <div class="input-group">
                                    <input class="input-form form-control text-center" id="nuevo-gps" name="nuevo-gps" type="text" placeholder="Nombre o modelo del GPS"/>
                                    <div class="input-group-text text-muted"><span class="lnr lnr-tablet"></span></div>
                                </div>
                                <div id="nuevo-gps-errors"></div>
                            </div>
                        </div>
                    </div>
                </form>
            </div>
            <div class="modal-footer">
                <div class="row">
                <div class="col-md-12 text-right mx-0 px-0" id="gps-footer"> 
                    <button class="button-file text-uppercase fw-bold" onclick="insertarModeloGPS();" id="btn-form-gps">Guardar <span class="fas fa-save"></span></button>
                </div>	
            </div>
            </div>
        </div>
    </div>
</div>

<!--MODAL PARA VER IMÁGENES-->
<div id="verIMG" class="modal fade bs-example-modal-sm" tabindex="-1" role="dialog" aria-labelledby="mySmallModalLabel" aria-hidden="true">
  <div class="modal-dialog">
    <div class="modal-content">
    <div class="modal-header py-0">
                <div class="label-sub fs-5 py-0">
                <span id="titulo-imagen"></span>
                </div>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
        <div class="modal-body d-flex justify-content-center" id="verIMGbody">
            
        </div>
        
    </div>
  </div>
</div>

<!--MODAL PARA AGREGAR OTRAS FOTOS-->
<div class="modal fade" id="titulo-foto" tabindex="-1" role="dialog" aria-labelledby="titulo-fotoLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
        <div class="modal-header py-0">
                <div class="label-sub fs-5 py-0">
                Agregar fotos
                </div>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <label for="titulo-img"><b class="text-muted">Descripción</b></label>
                <textarea id="titulo-img" class="form-control input-form" style="height: 15rem;" placeholder="Ingresa la descripción aquí"></textarea>
                <div id="titulo-img-errors"></div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn button-file text-uppercase" onclick="subirImgTitulo()">Aceptar <span class="fas fa-save"></span></button>
            </div>
        </div>
    </div>
</div>

<!--MODAL PARA VER VIDEOS-->
<div id="verVID" class="modal fade bs-example-modal-sm" tabindex="-1" role="dialog" aria-labelledby="mySmallModalLabel" aria-hidden="true">
  <div class="modal-dialog">
    <div class="modal-content">
    <div class="modal-header py-0">
                <div class="label-sub fs-5 py-0" >
                <span id="titulo-alerta-video"></span>
                </div>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
        <div class="modal-body" id="verVIDbody">
            
        </div>
    </div>
  </div>
</div>

<!--MODAL PARA VER LOS VIDEOS DESDE LA LISTA DE INSTALACIONES-->
<div class="modal fade bs-example-modal-lg" data-backdrop="static" id="videos-instalacion" tabindex="-1" role="dialog" aria-labelledby="myModalLabel">
    <div class="modal-dialog modal-lg" role="document">
        <div class="modal-content">
        <div class="modal-header py-0">
                <div class="label-sub fs-5 py-0" >Videos de servicio</div>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">

                <div class="row">
                    <div class="col-md-12 form-group">
                        <div class="row">
                            <div class="col-md-6">
                                <table  class="table table-hover table-condensed table-bordered table-striped text-center" style="max-width: 100%;">
                                    <tbody id="reg-vid">

                                    </tbody>
                                </table>
                            </div>
                            <div class="col-md-6">
                                <div id="reproducir-video"></div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!--ENVIAR EMAIL-->
<div class="modal fade bs-example-modal-lg" id="enviar-mail" tabindex="-1" role="dialog" aria-labelledby="myModalLabel">
    <div class="modal-dialog modal-lg" role="document">
        <div class="modal-content">
        <div class="modal-header py-0">
                <div class="label-sub fs-5 py-0" >Seleccionar correo electrónico:</div>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <form class="form-horizontal" id="form-mail" onsubmit="return false;">
                    <input id="idinstalacionenvio" name="idinstalacionenvio" type="hidden"/>
                    <div class="row">
                       <div class="col-6 py-2">
                       <label class="label-form mb-1 col-md-12 text-right" for="correorecibo1">Correo información</label>
                        <div class="col-md-12 form-group">
                            <div class="input-group">
                                <input class="form-control text-center input-form" id="correoinstalacion1" name="correoinstalacion1" placeholder="Correo de información" type="text" disabled/>
                                <div class="input-group-text"><input class="input-check" id="chcorreoinstalacion1" name="chcorreoinstalacion1" type="checkbox"/></div>
                            </div>
                            <div id="correoinstalacion1-errors">
                            </div>
                        </div>
                       </div>
                        <div class="col-6 py-2">
                        <label class="label-form mb-1 col-md-12 text-right" for="correoinstalacion2">Correo facturación</label>
                        <div class="col-md-12 form-group">
                            <div class="input-group">
                                <input class="form-control text-center input-form" id="correoinstalacion2" name="correoinstalacion2" placeholder="Correo de facturación" type="text" disabled/>
                                <div class="input-group-text"><input class="input-check" id="chcorreoinstalacion2" name="chcorreoinstalacion2" type="checkbox" checked/></div>
                            </div>
                            <div id="correoinstalacion2-errors">
                            </div>
                        </div>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-6 py-2">
                        <label class="label-form mb-1 col-md-12 text-right" for="correoinstalacion3">Correo gerencia</label>
                        <div class="col-md-12 form-group">
                            <div class="input-group">
                                <input class="form-control text-center input-form" id="correoinstalacion3" name="correoinstalacion3" placeholder="Correo de gerencia" type="text" disabled/>
                                <div class="input-group-text"><input class="input-check" id="chcorreoinstalacion3" name="chcorreoinstalacion3" type="checkbox"/></div>
                            </div>
                            <div id="correoinstalacion3-errors">
                            </div>
                        </div>
                        </div>
                        <div class="col-6 py-2">
                        <label class="label-form mb-1 col-md-12 text-right" for="correoinstalacion4">Correo alternativo</label>
                        <div class="col-md-12 form-group">
                            <div class="input-group">
                                <input class="form-control text-center input-form" id="correoinstalacion4" name="correoinstalacion4" placeholder="Otro correo" type="text"/>
                                <div class="input-group-text"><input class="input-check" id="chcorreoinstalacion4" name="chcorreoinstalacion4" type="checkbox"/></div>
                            </div>
                            <div id="correoinstalacion4-errors">
                            </div>
                        </div>
                        </div>
                    </div>
                    <div class="row mt-3">
                        <div class="col-md-12 text-end" id="btn">
                            <button class="button-file text-uppercase" onclick="enviarInstalacion()" id="btn-pago">Enviar <span class="fas fa-envelope"></span></button>
                        </div>	
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>

<!--MODAL PARA FIRMAR LA INSTALACION DESDE LA LISTA-->
<div class="modal fade" id="sign-modal" tabindex="-1" role="dialog" aria-labelledby="myModalLabel">
    <div class="modal-dialog modal-lg" role="document">
        <div class="modal-content">
        <div class="modal-header py-0">
                <div class="label-sub fs-5 py-0" >Firmar instalación:</div>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <input id="idinstalacionfirma" name="idinstalacionfirma" type="hidden"/>
                    <div class="row">
                    <div class="col-6">
                        <div class="row aviso-responsabilidad px-3 pt-2" style="text-align: justify;">
                        El cliente acepta lo reflejado en este documento, y también acepta que la empresa que ofreció dicho servicio, así como sus empleados no se hacen responsables por daños causados antes, durante o después de la instalación. Para conservar la Garantía del Sistema de Rastreo Satelital GPS es muy importante evitar que éste sea manipulado o alterado por personal ajeno a que ofreció dicho servicio y esto incluye a técnicos de agencias de vehículos nuevos. La empresa no responderá por ningún tipo de daño si el dispositivo o su instalación fueron previamente intervenidos por terceros. La empresa no se hace responsable por la pérdida o robo de objetos de valor personales dentro del vehículo
                    </div>
                        </div>
                        <div class="col-6">
                        <label class="label-form mb-1 col-md-12 text-right" for="encargado-firma">Encargado</label>
                        <div class="col-md-12 form-group">
                            <div class="input-group">
                                <input class="form-control text-center input-form" id="encargado-firma" name="encargado-firma" placeholder="Nombre del encargado" type="text"/>
                            </div>
                            <div id="encargado-firma-errors">
                            </div>
                        </div>
                        <div class="col-md-12 py-2">
                            <label class="label-form mb-1 text-left" for="firma">Firma</label>
                            <div class="form-group">
                                <canvas id="firma-modal" width="auto" height="200" class="sign-canvas"></canvas>
                                <div id="firma-modal-errors">
                                </div>
                            </div>
                            <div class="d-flex justify-content-between mt-2">
                                <button class="btn btn-sm button-file text-uppercase fw-semibold col-5 small" id="clearmod" >Limpiar <span class="fas fa-eraser"></span></button>
                                <button class="btn btn-sm button-file text-uppercase fw-semibold col-5 small" id="undomod" >Borrar <span class="fas fa-backward"></span></button>
                            </div>
                        </div>
                        </div>
                    </div>
                    <div class="row mt-3">
                        <div class="col-md-12 text-start" id="btn">
                            <button class="btn btn-sm button-file text-uppercase fw-semibold" onclick="actualizarFirma()" id="btn-pago">Firmar instalación <span class="fas fa-save"></span></button>
                        </div>	
                    </div>
            </div>
        </div>
    </div>
</div>

<!--MODAL PARA GENERAR BITÁCORA-->
<div class="modal fade bs-example-modal-lg" id="generar-bitacora" tabindex="-1" role="dialog" aria-labelledby="myModalLabel">
    <div class="modal-dialog modal-lg" role="document">
        <div class="modal-content">
        <div class="modal-header py-0">
                <div class="label-sub fs-5 py-0" >seleccionar fechas:</div>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <form class="form-horizontal" id="form-bitacora" onsubmit="return false;">
                    <div class="row">
                        <div class="col-6 py-2">
                        <label class="label-form mb-1 col-md-12 text-right" for="date-inicio">Fecha inicio</label>
                        <div class="col-md-12 form-group">
                            <div class="input-group">
                                <input class="form-control text-center input-form" id="date-inicio" name="date-inicio" type="date"/>
                            </div>
                            <div id="date-inicio-errors">
                            </div>
                        </div>
                        </div>
                        <div class="col-6 py-2">
                        <label class="label-form mb-1 col-md-12 text-right" for="date-fin">Fecha fin</label>
                        <div class="col-md-12 form-group">
                            <div class="input-group">
                                <input class="form-control text-center input-form" id="date-fin" name="date-fin" type="date"/>
                            </div>
                            <div id="date-fin-errors">
                            </div>
                        </div>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-6 py-2">
                        <label class="label-form mb-1 col-md-12 text-right" for="cliente-search">Cliente</label>
                        <div class="col-md-12 form-group">
                            <div class="input-group">
                                <input class="form-control text-center input-form" id="cliente-search" name="cliente-search" placeholder="Nombre del cliente" oninput="autocompletarCliente();" type="text"/>
                            </div>
                            <div id="cliente-search-errors">
                            </div>
                        </div>
                        </div>
                        <div class="col-6 py-2">
                        <label class="label-form mb-1 col-md-12 text-right" for="search-servicio">Servicio</label>
                        <div class="col-md-12 form-group">
                            <div class="input-group">
                                <select class="form-select text-center input-form" id="search-servicio" name="search-servicio">
                                    <option class="text-center" value="">- - - -</option>
                                    <option class="text-start" value="1" >Instalacion</option>
                                    <option class="text-start" value="2" >Reubicación</option>
                                    <option class="text-start" value="3" >Reposición</option>
                                    <option class="text-start" value="4" >Retiro</option>
                                    <option class="text-start" value="5" >Revisión</option>
                                    <option class="text-start" value="6" >Cambio de unidad</option>
                                    <option class="text-start" value="8" >Cambio de equipo</option>
                                    <option class="text-start" value="9" >Cambio de SIM</option>
                                    <option class="text-start" value="7" >Otros</option>
                                </select>
                            </div>
                            <div id="search-servicio-errors">
                            </div>
                        </div>
                        </div>
                    </div>
                    <div class="row mt-3">
                        <div class="col-md-12 text-start" id="btn">
                            <button class="btn button-file text-uppercase fw-semibold" onclick="generarBitacora();" id="btn-form-reporte">Imprimir <span class="fas fa-print"></span></button>
                        </div>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>

<!--MODAL PARA CANCELAR UNA INSTALACIÓN-->
<div class="modal fade" id="cancelInst" tabindex="-1" role="dialog" aria-labelledby="cancelInstLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
        <div class="modal-header py-0">
                <div class="label-sub fs-5 py-0" >Cancelar instalación:</div>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <label for="motivo-cancel"><b class="text-muted mb-1">Motivo de la cancelación</b></label>
                <textarea id="motivo-cancel" class="form-control input-form" style="height: 15rem;" placeholder="Porque se reliza la cancelación"></textarea>
                <div id="motivo-cancel-errors"></div>
            </div>
            <div class="modal-footer" id="cancelInst-footer">
            </div>
        </div>
    </div>
</div>

<!--MODAL PARA VER CANCELACIÓN DESDE LA LISTA DE INSTALACIONES-->
<div class="modal fade" id="verCancelInst" tabindex="-1" role="dialog" aria-labelledby="verCancelInstLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
        <div class="modal-header py-0">
                <div class="label-sub fs-5 py-0" >Cancelar instalación:</div>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body" id="verCancelInst-body">
            </div>
        </div>
    </div>
</div>
