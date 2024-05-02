<?php
include 'modals.php';
?>
<div class="form-horizontal ps-3 fijo z-2">
<div><div class="titulo-lista">Lista de instalaciones creadas</div></div>
    <div class="row col-12 p-0">
        <div class="col-sm-2 py-1">
            <select class="form-select text-center input-search" id="filtro-busqueda" name="filtro-busqueda" onchange="buscarInstalacion();">
                <option class="text-start" value="" >Filtro</option>
                <option class="text-start fw-normal small" value="1" >No. Orden</option>
                <option class="text-start fw-normal small" value="2" >Cliente</option>
                <option class="text-start fw-normal small" value="3" >Plataforma</option>
                <option class="text-start fw-normal small" value="4" >GPS Vehículo</option>
                <option class="text-start fw-normal small" value="5" >IMEI</option>
                <option class="text-start fw-normal small" value="6" >No. Teléfono</option>
                <option class="text-start fw-normal small" value="7" >Modelo anterior</option>
                <option class="text-start fw-normal small" value="8" >IMEI anterior</option>
                <option class="text-start fw-normal small" value="9" >SIM anterior</option>
                <option class="text-start fw-normal small" value="10" >No. Económico</option>
                <option class="text-start fw-normal small" value="11" >Serie</option>
            </select>
        </div>
        <div class="col-sm-2 py-1">
            <select class="form-select text-center input-search" id="tipo-servicio" name="tipo-servicio" onchange="buscarInstalacion();">
                <option class="text-start" value="" >Servicio</option>
                <option class="text-start fw-normal small" value="1" >Instalación</option>
                <option class="text-start fw-normal small" value="2" >Reubicación</option>
                <option class="text-start fw-normal small" value="3" >Reposición</option>
                <option class="text-start fw-normal small" value="4" >Retiro</option>
                <option class="text-start fw-normal small" value="5" >Revisión</option>
                <option class="text-start fw-normal small" value="6" >Cambio de unidad</option>
                <option class="text-start fw-normal small" value="8" >Cambio de equipo</option>
                <option class="text-start fw-normal small" value="9" >Cambio de SIM</option>
                <option class="text-start fw-normal small" value="7" >Otros</option>
            </select>
        </div>
        <div class="col-sm-2 py-1">
            <select class="form-select input-search text-center" id="num-reg" name="num-reg" onchange="buscarInstalacion()">
                <option value="10"> 10</option>
                <option value="15"> 15</option>
                <option value="20"> 20</option>
                <option value="30"> 30</option>
                <option value="50"> 50</option>
                <option value="100">100</option>
            </select>
        </div>
        <div class="col-sm-3 py-1">
            <input type="text" class="form-control input-search text-secondary-emphasis" id="buscar-instalacion"
                placeholder="Buscar instalación" oninput="buscarInstalacion()">
        </div>
        <div class="col-sm-3 py-1 text-end">
            <div class='dropdown'>
                <button class='btn button-create dropdown-toggle col-12' class="button-ventas col-12" style="background-color: #327AB7 !important; color: #fff !important;" title='Imprimir'  type='button' data-bs-toggle='dropdown'>Imprimir <span class='fas fa-print'></span>
                    <span class='caret'></span>
                </button>
                <ul class='dropdown-menu dropdown-menu-right'>
                    <li class='notification-link py-1 ps-3'><a class='text-decoration-none text-secondary-emphasis' data-bs-toggle='modal' data-bs-target='#generar-bitacora'>Generar bitácora <span class='fas fa-save text-muted'></span></a></li>
                    <li class='notification-link py-1 ps-3'><a class='text-decoration-none text-secondary-emphasis' onclick="hojaenBlanco();">Hoja de servicio en blanco <span class='fas fa-save text-muted'></span></a></li>
                </ul>
            </div>
        </div>
    </div>
</div>
<div class="scrollX div-form mw-100 bg-light mx-3 border border-secondary-subtle pb-5" >
    <table class="table table-hover table-condensed table-responsive table-row table-head" id="body-lista-instalacion">
        <thead class="sin-paddding">
            <tr class="info align-middle">
                <th class='text-center'>Folio</th>
                <th class='text-center'>Fecha Servicio </th>
                <th class='text-center'>Cliente</th>
                <th class='text-center'>Servicio </th>
                <th class='text-center'>Equipo </th>
                <th class='text-center'>Plataforma</th>
                <th class='text-center'>IMEI </th>
                <th class='text-center'>No. Económco </th>
                <th class='text-center'>Instalador </th>
                <th class='text-center'>Encargado </th>
                <th class='text-center'>Creó </th>
                <th class='text-center'>Editó </th>
                <th class='text-center'>Finalizó </th>
                <th class='text-center'>Estado </th>
                <th class='text-center'>Opción</th>
            </tr>
        </thead>
    </table>
</div>
<script type="text/javascript" src="js/scriptinstalacion.js"></script>