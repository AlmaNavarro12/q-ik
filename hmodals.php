<!--Modal de editar imagen de perfil-->
<div class="modal fade" id="modal-profile-img" tabindex="-1" role="dialog" aria-labelledby="myModalLabel">
    <div class="modal-dialog modal-sm" role="document">
        <div class="modal-content">
            <button type="button" class="" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
            <h4 class="titulo-modal" id="myModalLabel">Imagen de Perfil</h4>
            <div class="modal-body">
                <form action="com.sine.enlace/enlaceusuario.php" onsubmit="return false;" id="form-profile">
                    <div class="ex-profile text-center" id="profimg">
                    </div>
                    <div class="text-center">
                        <label class="button-file text-right" for="imgprof"><span class="glyphicon glyphicon-camera" ></span> Seleccionar Imagen</label>
                        <input class="form-control text-center upload"  id="imgprof" name="imgprof"  type="file" onchange="cargarImgPerfil();"/>
                        <input id="fileuser" name="fileuser"  type="hidden"/>
                        <div id="imgprof-errors">
                        </div>
                        <button id="btn-edit-profile" type="button" class="button-modal">Editar Perfil <span class="glyphicon glyphicon-edit"></span></button>
                        <button id="btn-form-profile" type="button" class="button-modal">Guardar <span class="glyphicon glyphicon-floppy-disk"></span></button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>


<!-- Modal -->
<div class="modal fade" id="exampleModal" tabindex="-1" aria-labelledby="exampleModalLabel" data-bs-backdrop="static" aria-hidden="true">
  <div class="modal-dialog modal-sm">
    <div class="modal-content">
      <div class="modal-header">
      <h4 class="modal-title fs-5" id="exampleModalLabel">Imagen de Perfil</h4>
        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
      </div>
      <div class="modal-body">
      </div>
    </div>
  </div>
</div>

<!--Modal para pedir soporte tecnico-->
<div class="modal fade" id="modal-alert" tabindex="-1" role="dialog" aria-labelledby="myModalLabel">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <button type="button" class="close-modal" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
            
            <div class="modal-body">
                <div class="alert-text text-justify" id="alert-body">
                    
                </div>
                <div class="text-right" >
                    <a href="https://q-ik.mx/Registro/comprar.php"><button id="btn-alert" type="button" class="button-modal">Comprar Timbres <span class="glyphicon glyphicon-credit-card"></span></button></a>
                </div>
            </div>
        </div>
    </div>
</div>

<div class="modal fade" id="modal-notification" tabindex="-1" role="dialog" aria-labelledby="myModalLabel">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <button type="button" class="close-modal" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
            <div class="modal-body">
                <div class="alert-text text-left" id="notification-date">
                    05/Jul/2021
                </div>
                <div class="alert-text text-justify" id="notification-body">
                    Si
                </div>
            </div>
        </div>
    </div>
</div>
            
<div class="modal fade" id="modal-contacto" tabindex="-1" role="dialog" aria-labelledby="myModalLabel">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <button type="button" class="close-modal" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
            <h4 class="titulo-modal" id="myModalLabel">Solicitar soporte técnico</h4>
            <div class="modal-body">
                <div class="row">
                    <div class="col-md-6">
                        <label class="label-form text-right" for="nombre-soporte">Nombre</label>
                        <div class="form-group">
                            <input class="input-form text-center form-control" id="nombre-soporte" name="nombre-soporte" placeholder="Nombre" type="text"/>
                            <div id="nombre-soporte-errors"></div>
                        </div>
                    </div>

                    <div class="col-md-4">
                        <label class="label-form text-right" for="telefono-soporte">Celular</label>
                        <div class="form-group">
                            <input class="input-form text-center form-control" id="telefono-soporte" name="telefono-soporte" placeholder="Telefono de contacto" type="text"/>
                            <div id="telefono-soporte-errors"></div>
                        </div>
                    </div>

                    <div class="col-md-2">
                        <div class="new-tooltip icon tip">
                            <label class="label-form text-left" for="chlogo">Tiene Whatsapp?</label>
                            <input class='input-check' type='checkbox' id='check-soporte' name="check-soporte" title='Desea recibir soporte por Whatsapp?'/>
                            <div id="check-soporte-errors"></div>
                            <span class="tiptext">Cuenta con Whatsapp? de esta forma se le puede brindar soporte tecnico por este medio</span>
                        </div>
                    </div>
                </div>

                <div class="row">
                    <div class="col-md-6">
                        <label class="label-form text-right" for="correo-soporte">Correo</label>
                        <div class="form-group">
                            <input class="input-form text-center form-control" id="correo-soporte" name="correo-soporte" placeholder="Correo de contacto" type="text"/>
                            <div id="correo-soporte-errors"></div>
                        </div>
                    </div>

                    <div class="col-md-6">
                        <label class="label-form text-right" for="mensaje-soporte">Mensaje</label>
                        <div class="form-group">
                            <textarea rows="5" cols="60" id="mensaje-soporte" class="form-control input-form" placeholder="Mensaje" maxlength="400" ></textarea>
                            <div id="mensaje-soporte-errors"></div>
                        </div>
                    </div>
                </div>

                <div class="row">
                    <div class="col-md-12 text-right">
                        <button class="button-modal" onclick="enviarSoporte();" id="send-soporte">Enviar <span class="glyphicon glyphicon-envelope"></span></button>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
