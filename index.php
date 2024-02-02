<?php
if (isset($_POST['usuario'], $_POST['contrasena'])) {
    include_once './com.sine.controlador/ControladorSessionPost.php';
    $cs = new ControladorSessionPost();
    $usuario = $_POST['usuario'];
    $contrasena = sha1($_POST['contrasena']);
    echo $cs->loginPost($usuario, $contrasena) ? 'Si' : 'Usuario o contraseña no válidos.';
} else {
    include 'com.sine.common/commonhead.php';
?>
    <!DOCTYPE html>
    <html>

    <head>
        <?php include 'com.sine.common/commonhead.php'; ?>
    </head>

    <body>
        <div class="row p-0 m-0 h-100 full-height">
            <div id="body-index" class="h-100 float-start col-md-5" style="background-color: #EDEDF4;"></div>
            <div id="body-right" class="body-right col-md-7">
                <div class="sm-square"></div>
                <div class="md-square"></div>
                <div class="elipse-login"></div>
                <div class="div-demo">
                    <div class="row login-info">
                        Usar Q-ik es más facil que llevar tus cuentas en Excel
                    </div>
                    <div class="row login-demo">
                        Compruebalo con nuestra demo de 14 días*
                    </div>
                    <br>
                    <div class="row">
                        <div class="demo-block px-0" style="max-width:205px;">
                            <a href="registrar.php"><button class="button-demo"> Solicitar demo </button></a>
                        </div>
                    </div>
                </div>
                <div class="div-card">
                    *No solicitamos tarjeta
                </div>
            </div>
        </div>
        <script src="js/scriptlogin.js"></script>
    </body>

    </html>
<?php } ?>