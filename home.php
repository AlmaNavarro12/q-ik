<?php
session_start();
if (!isset($_SESSION[sha1('usuario')])) {
    header("Location: index.php");
    exit();
}
require_once 'Enrutador.php';
?>
<html>

<head>
    <?php
    include 'com.sine.common/commonhead.php';
    include './com.sine.controlador/ControladorPermiso.php';
    $cp = new ControladorPermiso();
    $permisos = $cp->getPermisos();

    $div = explode("</tr>", $permisos);
    $uid = $div[0];
    $nombreusuario = $div[1];
    $facturas = $div[2];
    $pago = $div[3];
    $nomina = $div[4];
    $listaempleado = $div[5];
    $listanomina = $div[6];
    $cartaporte = $div[7];
    $listaubicacion = $div[8];
    $listatransporte = $div[9];
    $listaremolque = $div[10];
    $listaoperador = $div[11];
    $listacarta = $div[12];
    $cotizacion = $div[13];
    $cliente = $div[14];
    $comunicado = $div[16];
    $producto = $div[17];
    $proveedor = $div[18];
    $impuesto = $div[19];
    $datosfacturacion = $div[20];
    $contrato = $div[21];
    $listausuario = $div[22];
    $reporte = $div[23];
    $reportefactura = $div[24];
    $reportepago = $div[25];
    $reportegrafica = $div[26];
    $reporteiva = $div[27];
    $datosiva = $div[28];
    $reporteventa = $div[29];
    $reporteinventario = $div[30];
    $reportepuntoventa = $div[31];
    $configuracion = $div[32];
    $ventas = $div[33];
    $crearventa = $div[34];
    $listaventa = $div[35];
    $registrarentrada = $div[36];
    $registrarsalida = $div[37];
    $acceso = $div[38];
    $imgperfil = $div[39];
    $modulos = $div[40];

    $notificaciones = $cp->getNotificacion();
    $divN = explode("<corte>", $notificaciones);
    $listN = $divN[0];
    $countN = $divN[1];
    $Mactive = "";
    if ($countN > 0) {
        $Mactive = "notification-marker-active";
    }
    $mod = explode("-", $modulos);
    echo "<script>var uid = '" . $uid . "';</script>";
    echo "<script>var nombreusuario = '" . $nombreusuario . "';</script>";
    echo "<script>var imagenperfil = '" . $imgperfil . "';</script>";
    echo "<script>var puntoventa = '" . $ventas . "';</script>";
    echo "<script>var crearventa = '" . $crearventa . "';</script>";
    echo "<script>var registrarentrada = '" . $registrarentrada . "';</script>";
    echo "<script>var registrarsalida = '" . $registrarsalida . "';</script>";

    ?>
</head>

<body style="position: fixed; width: 100%;">
    <div style="overflow-y: scroll; height: 100%;" id="">
        <header class="w-100 position-fixed mb-0 pb-0">
            <div class="smh-square position-absolute"></div>
            <div class="mdh-square position-absolute"></div>
            <span id="menu-icon" class="lnr lnr-menu show-menu position-fixed p-2 mt-4"></span>

            <div id="head-info">
                <div class="logo-color position-absolute"></div>
                <div class="position-fixed user-info mx-3">
                    <div class="dropdown zn-3">
                        <button class="btn button-home dropdown-toggle" type="button" data-bs-toggle="dropdown" aria-expanded="false">
                            <span id="user-name"> <?php echo iconv('UTF-8', 'windows-1252', $nombreusuario); ?> </span> <span class="caret"></span>
                        </button>
                        <ul class="dropdown-menu dropdown-menu-end ">
                            <?php
                            if ($configuracion == 1) {
                            ?>
                                <li class="py-1"><a class="option-link list-conf px-3" data-submenu="config">Configuración</a></li>
                            <?php
                            }
                            if ($listausuario == 1) {
                            ?>
                                <li><a class="option-link list-conf px-3" data-submenu="listasuarioaltas">Usuarios</a></li>
                            <?php
                            }
                            ?>
                            <li class="py-1"><a class="logout-link px-3" onclick="logout();">Cerrar Sesión</a></li>
                        </ul>
                    </div>
                </div>
                <div id="notification-alert" class="notification-marker <?php echo $Mactive; ?>"></div>
                <div class="img-user mx-3">
                    <div class="dropdown text-center" style="height: 70px;">
                        <button class="btn button-home" title="Notificaciones" type="button" data-bs-toggle="dropdown">
                            <img src="img/usuarios/<?php echo $imgperfil; ?>" />
                        </button>
                        <ul class="dropdown-menu user-option" id="list-notificaciones">
                            <li class="py-1">
                                <a class="notification-link" onclick="loadImgPerfil(<?php echo $uid; ?>)" data-bs-toggle="modal" data-bs-target="#modal-profile-img" title="Cambiar imagen de perfil">
                                    <span class="fas fa-user"></span> Cambiar imagen de perfil
                                </a>
                            </li>
                            <?php
                            echo $listN;
                            ?>
                            <li class="py-1">
                                <a class="notification-link list-conf" data-submenu="notificacion">Ver todas las notificaciones</a>
                            </li>
                        </ul>
                    </div>
                </div>
            </div>
        </header>

        <main>
            <?php
            require_once 'hmodals.php';
            ?>

            <div id="main-menu" class="content-menu">
                <div class="elipse"></div>
                <div id="accordion" class="scroll text-white fw-normal position-absolute">
                    <div class="item-direction pt-1">
                        <li class="list-element mt-1 list-menu ps-5 menu-active" data-submenu='paginicio'>
                            <div class="marker marker-active"></div>
                            <div class="pad"></div><label> Inicio </label>
                        </li>
                        <?php
                        foreach ($mod as $modactual) {
                            switch ($modactual) {
                                case '1':
                                    if ($facturas == '1') {
                        ?>
                                        <li id="factura-menu" class='list-element mt-1 list-menu ps-5' data-submenu='listafactura'>
                                            <div class='marker'></div>
                                            <div class='pad'></div><label> Facturas</label>
                                        </li>
                                    <?php
                                    }
                                    break;
                                case '2':
                                    if ($pago == '1') {
                                    ?>
                                        <li id="pago-menu" class='list-element mt-1 list-menu ps-5' data-submenu='listapago'>
                                            <div class='marker'></div>
                                            <div class='pad'></div><label> Pagos </label>
                                        </li>
                                    <?php
                                    }
                                    break;
                                case '3':
                                    if ($nomina == '1') {
                                    ?>
                                        <a href="#colnomina" class="text-white" style="text-decoration: none;" data-bs-toggle="collapse" href="#colreporte" role="button" aria-expanded="false">
                                            <li class="list-element mt-1 ps-5">
                                                <div class="marker"></div>
                                                <div class="pad"></div><label> Nóminas</label>
                                            </li>
                                        </a>
                                        <div id='colnomina' class='panel-collapse collapse'>
                                            <ul>
                                                <?php
                                                if ($listaempleado == '1') {
                                                ?>
                                                    <li class='lista-submenu-elemento ps-5 list-menu' data-submenu='listaempleado'> Empleados</li>
                                                <?php
                                                }
                                                if ($listanomina == '1') {
                                                ?>
                                                    <li class='lista-submenu-elemento ps-5 list-menu' data-submenu='listanomina'> N&oacute;minas</li>
                                                <?php
                                                }
                                                ?>
                                            </ul>
                                        </div>
                                    <?php
                                    }
                                    break;
                                case '4':
                                    if ($cartaporte == '1') {
                                    ?>
                                        <a href="#colcartaporte" class="text-white" style="text-decoration: none;" data-bs-toggle="collapse" data-bs-target="#colcartaporte" href="#colreporte" role="button" aria-expanded="false">
                                            <li class="list-element mt-1 ps-5">
                                                <div class="marker"></div>
                                                <div class="pad"></div><label> Carta porte</label>
                                            </li>
                                        </a>
                                        <div id='colcartaporte' class='panel-collapse collapse'>
                                            <ul>
                                                <?php
                                                if ($listaubicacion == '1') {
                                                ?>
                                                    <li class='lista-submenu-elemento ps-5 list-menu' data-submenu='listadireccion'> Ubicaciones</li>
                                                <?php
                                                }
                                                if ($listatransporte == '1') {
                                                ?>
                                                    <li class='lista-submenu-elemento ps-5 list-menu' data-submenu='listatransporte'> Transportes</li>
                                                <?php
                                                }
                                                if ($listaremolque == '1') {
                                                ?>
                                                    <li class='lista-submenu-elemento ps-5 list-menu' data-submenu='listaremolque'> Remolques</li>
                                                <?php
                                                }
                                                if ($listaoperador == '1') {
                                                ?>
                                                    <li class='lista-submenu-elemento ps-5 list-menu' data-submenu='listaoperador'> Operadores</li>
                                                <?php
                                                }
                                                if ($listacarta == '1') {
                                                ?>
                                                    <li class='lista-submenu-elemento ps-5 list-menu' data-submenu='listacarta'> Carta porte</li>
                                                <?php
                                                }
                                                ?>
                                            </ul>
                                        </div>
                                    <?php
                                    }
                                    break;
                                case '5':
                                    if ($cotizacion == '1') {
                                    ?>
                                        <li class='list-element mt-1 list-menu ps-5' data-submenu='listacotizacion'>
                                            <div class='marker'></div>
                                            <div class='pad'></div><label> Cotizaciones</label>
                                        </li>
                                    <?php
                                    }
                                    break;
                                case '6':
                                    if ($cliente == '1') {
                                    ?>
                                        <li class='list-element mt-1 list-menu ps-5' data-submenu='listaclientealtas'>
                                            <div class='marker'></div>
                                            <div class='pad'></div><label> Clientes</label>
                                        </li>
                                    <?php
                                    }
                                    break;
                                case '7':
                                    if ($comunicado == '1') {
                                    ?>
                                        <li class='list-element mt-1 list-menu ps-5' data-submenu='listacomunicado'>
                                            <div class='marker'></div>
                                            <div class='pad'></div><label> Comunicados</label>
                                        </li>
                                    <?php
                                    }
                                    break;
                                case '8':
                                    if ($producto == '1') {
                                    ?>
                                        <li class='list-element mt-1 list-menu ps-5' data-submenu='listaproductoaltas'>
                                            <div class='marker'></div>
                                            <div class='pad'></div><label> Productos</label>
                                        </li>
                                    <?php
                                    }
                                    break;
                                case '9':
                                    if ($proveedor == '1') {
                                    ?>
                                        <li class='list-element mt-1 list-menu ps-5' data-submenu='listaproveedor'>
                                            <div class='marker'></div>
                                            <div class='pad'></div><label> Proveedor</label>
                                        </li>
                                    <?php
                                    }
                                    break;
                                case '10':
                                    if ($impuesto == '1') {
                                    ?>
                                        <li class='list-element mt-1 list-menu ps-5' data-submenu='listaimpuesto'>
                                            <div class='marker'></div>
                                            <div class='pad'></div><label> Impuestos</label>
                                        </li>
                                    <?php
                                    }
                                    break;
                                case '11':
                                    if ($datosfacturacion == '1') {
                                    ?>
                                        <li class='list-element mt-1 list-menu ps-5' data-submenu='listaempresa'>
                                            <div class='marker'></div>
                                            <div class='pad'></div><label> Datos facturación</label>
                                        </li>
                                    <?php
                                    }
                                    break;
                                case '12':
                                    if ($contrato == '1') {
                                    ?>
                                        <li class="list-element mt-1 list-menu ps-5" data-submenu='listacontratos'>
                                            <div class="marker"></div>
                                            <div class="pad"></div><label>Factura automática</label>
                                        </li>
                                    <?php
                                    }
                                    break;
                                case '13':
                                    if ($reporte == '1') {
                                    ?>
                                        <a href="#colreporte" class="text-white" style="text-decoration: none;" data-bs-toggle="collapse" href="#colreporte" role="button" aria-expanded="false">
                                            <li class="list-element mt-1 ps-5">
                                                <div class="marker"></div>
                                                <div class="pad"></div><label> Reportes</label>
                                            </li>
                                        </a>
                                        <div id="colreporte" class="panel-collapse collapse">
                                            <ul>
                                                <?php
                                                if ($reportefactura == '1') {
                                                ?>
                                                    <li class="lista-submenu-elemento ps-5 list-menu" data-submenu='reportefactura'> Facturas</li>
                                                <?php
                                                }
                                                if ($reportepago == '1') {
                                                ?>
                                                    <li class="lista-submenu-elemento ps-5 list-menu" data-submenu='reportepago'> Pagos</li>
                                                <?php
                                                }

                                                if ($reportegrafica == '1') {
                                                ?>
                                                    <li class="lista-submenu-elemento ps-5 list-menu" data-submenu='reportegrafica'> Gráfica</li>
                                                <?php
                                                }
                                                if ($reporteiva == '1') {
                                                ?>
                                                    <li class="lista-submenu-elemento ps-5 list-menu" data-submenu='reportesat'> Impuestos</li>
                                                <?php
                                                }
                                                if ($datosiva == '1') {
                                                ?>
                                                    <li class="lista-submenu-elemento ps-5 list-menu" data-submenu='datosiva'> Datos impuestos</li>
                                                <?php
                                                }
                                                if ($reporteventa == '1') {
                                                ?>
                                                    <li class="lista-submenu-elemento ps-5 list-menu" data-submenu='reporteventas'> Ventas</li>
                                                <?php
                                                }
                                                if ($reporteinventario == '1') {
                                                ?>
                                                    <li class="lista-submenu-elemento ps-5 list-menu" data-submenu='reporteinventario'> Inventario</li>
                                                <?php
                                                }
                                                if ($reportepuntoventa == '1') {
                                                ?>
                                                    <li class="lista-submenu-elemento ps-5 list-menu" data-submenu='reportepuntoventa'> Punto de venta</li>
                                                <?php
                                                }
                                                ?>
                                            </ul>
                                        </div>
                                    <?php
                                    }
                                    break;
                                case '14':
                                    if ($crearventa == '1' || $listaventa == '1') {
                                        $ventavista = ($crearventa == '1') ? 'puntodeventa' : 'listaticket';
                                    ?>
                                        <li id="punto-venta" class="list-element mt-1 list-menu ps-5" data-submenu='<?php echo $ventavista; ?>'>
                                            <div class="marker"></div>
                                            <div class="pad"></div><label> Punto de venta</label>
                                        </li>
                        <?php
                                    }
                                    break;
                            }
                        }
                        ?>
                        <li data-bs-toggle='modal' data-bs-target='#modal-contacto' class='list-element mt-1 ps-5' onclick='getNombreUsuario();'>
                            <div class='marker'></div>
                            <div class='pad'></div><label> Soporte técnico</label>
                        </li>
                        <br />
                    </div>
                </div>
            </div>
            <article id="contenedor-vista-right" class="wrapper left-pad">
                <?php
                $e = new Enrutador();
                if (isset($_GET['view'])) {
                    $vista = $_GET['view'];
                    $e->cargarVista($vista);
                } else {
                    $e->cargarVista("venta");
                }
                ?>
            </article>
        </main>
    </div>
</body>
<script>
    resetMenu();
    window.addEventListener('resize', resetMenu);
</script>

</html>