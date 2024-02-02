<!DOCTYPE html>
<html>

<head>
    <?php
    include 'com.sine.common/commonhead.php';
    ?>
</head>

<body id="index">
    <div class="sm-square"></div>
    <div class="mdr-square"></div>
    <div class="lg-square"></div>
    
    <div class="modal fade shadow-lg rounded rounded-5" id="modal-recover" aria-labelledby="exampleModalLabel" data-bs-backdrop="static" aria-hidden="true">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h4 class="modal-title fs-5" id="exampleModalLabel">Selecciona el usuario del que deseas recupera la cuenta y el proceso de cambio de contraseña se enviara al correo registrado:</h4>
                <button type="button" id="btn-close-modal" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
            <div class="row">
                        <div id="chuser-errors">
                        </div>
                    </div>
                    <table class="table table-hover table-condensed table-responsive table-row" id="usuarios-registrados">

                    </table>
                    <div class="row">
                        <div class="col-md-12 d-flex justify-content-end" id="btn">
                            <button class="btn button-file fs-6 col-auto" onclick="recuperarCuenta();" id="btn-recover">Recuperar cuenta <span class="fas fa-envelope"></span></button>
                        </div>
                    </div>
            </div>
        </div>
    </div>
</div>

    <div id="body-card" class="body-registrar">
        <div class="col-12 pt-5 z-0 position-absolute" id="contenedor-formulario-login">
            <div id="form-card" class="div-recover bg-white shadow col-md-6 col-sm-12 p-5 rounded-4 mt-5 mx-auto">
            <img src="img/LogoQik.png" class="img-rounded mx-auto d-block" style="max-height: 158px;" id="logo-login" />
            <br /><br />
                <form action="#" method="post" id="formulario-pago" onsubmit="return false;" name="formulario-pago">
                    <div class="row">
                        <div class="col-md-12 form-group">
                            <label class="label-form text-left" for="rfc-usuario">Ingrese el RFC o correo con el que se realizo el registro para buscar su cuenta.</label>
                            <input class="form-control text-center input-form mt-2" id="rfc-usuario" name="rfc-usuario" placeholder="RFC o correo con el que se registro el usuario" type="text" />
                            <div id="rfc-usuario-errors">
                            </div>
                        </div>
                    </div>
                    <div class="row d-flex justify-content-end mt-3 px-3">
                        <a class="btn button-file fs-6 col-auto me-3" onclick="goBack(); return false;">Cancelar <span class="fas fa-times"></span></a>
                        <a class="btn button-file fs-6 col-auto" id="btn-form-recover" onclick="buscarCuenta()" data-placement="left"> Buscar <span class="fas fa-search"></span></a>
                    </div>
                </form>
            </div>
        </div>
        <br />
    </div>
    <script src="js/scriptregistrar.js"></script>
    <script>
        function goBack() {
            window.history.back();
        }
    </script>
</body>

</html>