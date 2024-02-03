<?php
if (isset($_POST['usuario'], $_POST['contrasena'])) {
    include_once './com.sine.controlador/ControladorSession.php';
    $cs = new ControladorSession();
    $usuario = $_POST['usuario'];
    $contrasena = sha1($_POST['contrasena']);
    echo $cs->login($usuario, $contrasena) ? 'Si' : 'Usuario o contraseña no válidos.';
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
            </div>
        </div>
        <script src="js/scriptlogin.js"></script>
    </body>

    </html>
<?php } ?>