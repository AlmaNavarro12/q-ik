<?php
session_start();
if (isset($_SESSION[sha1('usuario')])) {
    header("Location: home.php");
    exit();
}
require_once 'Enrutador.php';
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <?php include 'com.sine.common/commonhead.php'; ?>
</head>

<body id="index" style="background: #09096B;">
    <div class="sm-square"></div>
    <div class="md-square"></div>
    <div class="lg-square"></div>
    <div class="elipse-login"></div>
    <div id="body-card" class="body-registrar d-flex align-items-center justify-content-center" style="min-height: 100vh;">
        <div id="form-card" class="div-recover z-0 bg-light p-5 rounded-3 shadow-lg col-md-6 col-sm-11">
            <div class="d-flex align-items-center justify-content-center">
                <div class="me-4">
                    <img src="img/LogoQik.png" class="img-rounded" style="max-height: 140px;" id="logo-login" />
                </div>
                <div class="line"></div>
                <div class="ms-4">
                    <i class="far fa-clock" style="font-size: 8.5rem; color: #09096B; "></i>
                </div>
            </div>
            <h1 class="text-center mt-5 fw-normal" style="color: #09096B;">Tu tiempo de sesión a expirado</h1>
            <h4 class="text-center fs-6 text-muted ">Por razones de seguridad hemos finalizado tu sesi&oacute;n por inactividad.</h4>
            <div class="text-center">
            <button style="cursor: pointer; width: 50%; height: 55px;" class="button-login mt-4" onclick="javascript:location.href='index.php'">ACEPTAR</button>
            </div>
        </div>
    </div>
</body>

</html>