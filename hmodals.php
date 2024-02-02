<!--IMAGEN DE PERFIL-->
<div class="modal fade shadow-lg rounded rounded-5" id="modal-profile-img" tabindex="-1" aria-labelledby="exampleModalLabel" data-bs-backdrop="static" aria-hidden="true">
    <div class="modal-dialog modal-sm">
        <div class="modal-content">
            <div class="modal-header">
                <h4 class="modal-title fs-5" id="exampleModalLabel">Imagen de Perfil</h4>
                <button type="button" id="btn-close-modal" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <form action="com.sine.enlace/enlaceusuario.php" onsubmit="return false;" id="form-profile">
                    <div class="ex-profile text-center" id="profimg">
                    </div>
                    <div class="col-12 mt-3">
                        <label class="button-file col-12" for="imgprof"><span class="fas fa-camera"></span> Seleccionar Imagen</label>
                        <input class="form-control text-center upload" id="imgprof" name="imgprof" type="file" accept=".png, .jpeg, .jpg, .gif" onchange="cargarImgPerfil();" hidden />
                        <input id="fileuser" name="fileuser" type="hidden"/>
                        <div id="imgprof-errors">
                        </div>
                        <div class="row d-flex justify-content-evenly mt-2">
                            <button id="btn-edit-profile" type="button" class="button-file col-6">Editar Perfil <span class="fas fa-edit"></span></button>
                            <button id="btn-form-profile" type="button" class="button-file col-5" disabled>Guardar <span class="fas fa-save"></span></button>
                        </div>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>

<script>
    disabledButton();
    window.addEventListener('resize', disabledButton);
</script>

<!--SOPORTE TÉCNICO-->
<div class="modal fade" id="modal-contacto" data-bs-backdrop="static" data-bs-keyboard="false" tabindex="-1" aria-labelledby="staticBackdropLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h1 class="modal-title fs-5" id="staticBackdropLabel">Solicitar soporte técnico</h1>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <div class="row mx-2">
                    <div class="col-md-6">
                        <label class="label-form text-right" for="nombre-soporte">Nombre</label>
                        <div class="form-group">
                            <input class="input-form text-center form-control" id="nombre-soporte" name="nombre-soporte" placeholder="Nombre" type="text" />
                            <div id="nombre-soporte-errors"></div>
                        </div>
                    </div>

                    <div class="col-md-6">
                        <label class="label-form text-right" for="telefono-soporte">Celular</label>
                        <div class="form-group">
                            <input class="input-form text-center form-control" id="telefono-soporte" name="telefono-soporte" placeholder="Telefono de contacto" type="text" />
                            <div id="telefono-soporte-errors"></div>
                        </div>
                        <div class="new-tooltip icon tip mt-2 d-flex align-items-start">
                            <input class='input-check' type='checkbox' id='check-soporte' name="check-soporte" title='Desea recibir soporte por Whatsapp?' />
                            <label class="form-check-label ms-2" for="check-soporte">¿Tiene Whatsapp?</label>
                            <div id="check-soporte-errors"></div>
                            <span class="tiptext">Si usted cuenta con Whatsapp y quiere que se le pueda brindar soporte técnico por ese medio, marque la casilla.</span>
                        </div>
                    </div>
                </div>

                <div class="row mx-2 mt-3">
                    <div class="col-md-6">
                        <label class="label-form text-right" for="correo-soporte">Correo</label>
                        <div class="form-group">
                            <input class="input-form text-center form-control" id="correo-soporte" name="correo-soporte" placeholder="Correo de contacto" type="text" />
                            <div id="correo-soporte-errors"></div>
                        </div>
                    </div>

                    <div class="col-md-6">
                        <label class="label-form text-right" for="mensaje-soporte">Mensaje</label>
                        <div class="form-group">
                            <textarea rows="5" cols="60" id="mensaje-soporte" class="form-control input-form" style="height: 100px" placeholder="Mensaje" maxlength="400"></textarea>
                            <div id="mensaje-soporte-errors"></div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="modal-footer">
                <button class="button-file fs-6" onclick="enviarSoporte();" id="send-soporte">Enviar <span class="fas fa-envelope"></span></button>
            </div>
        </div>
    </div>
</div>

<!--NOTIFICACIONES-->
<div class="modal fade" id="modal-notification" tabindex="-1" aria-labelledby="exampleModalLabel" data-bs-backdrop="static" aria-hidden="true">
    <div class="modal-dialog shadow-lg rounded rounded-5">
        <div class="modal-content">
            <div class="modal-header py-0">
                <div class="label-sub fs-5 py-0" id="notification-date">
                    05/Jul/2021
                </div>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <div class="label-sub fs-6 fw-bold text-justify" id="notification-body">
                    Si
                </div>
            </div>
        </div>
    </div>
</div>