<?php

require_once '../com.sine.dao/Consultas.php';
require_once '../vendor/autoload.php';
require_once '../com.sine.modelo/Session.php';
require_once '../com.sine.modelo/SendMail.php';

date_default_timezone_set("America/Mexico_City");
session_start();

class ControladorVenta {

    private $consultas;

    function __construct() {
        $this->consultas = new Consultas();
    }

    private function genTag()
    {
        $fecha = date('YmdHis');
        $idusu = $_SESSION[sha1("idusuario")];
        $sid = session_id();
        $ranstr = substr(str_shuffle("0123456789abcdefghijklmnopqrstuvwxyz"), 0, 5);
        $tag = $ranstr . $fecha . $idusu . $sid;
        return $tag;
    }

    public function loadNewTicket($ticket) {
        $tag = $this->genTag();
        $tab = "<button id='tab-$tag' class='sm-tab sub-tab-active' data-tab='$tag' name='tab' >Ticket $ticket &nbsp; <span  class='close-button' data-tab='$tag' type='button' aria-label='Close'><span aria-hidden='true'>&times;</span></span></button>
                <cut>
                    <div id='ticket-$tag' class='sub-div'>
                        <table id='prod-$tag' class='table tab-hover table-condensed table-responsive table-row table-venta'>
                            <thead class='sin-paddding'>
                                <tr>
                                    <th class='text-center'>COD BARRAS</th>
                                    <th class='text-center'>CLV FISCAL</th>
                                    <th class='text-center'>DESCRIPCIÓN</th>
                                    <th class='text-center'>PRECIO</th>
                                    <th class='text-center'>CANT.</th>
                                    <th class='text-center'>TRASLADOS</th>
                                    <th class='text-center'>RETENCIONES</th>
                                    <th class='text-center'>IMPORTE</th>
                                    <th class='text-center'>ELIMINAR</th>
                                </tr>
                            </thead>
                            <tbody>
                                <tr>
                                    <td></td>
                                    <td></td>
                                    <td></td>
                                    <td></td>
                                    <td></td>
                                </tr>
                            </tbody>
                            <tfoot>
                                <tr>
                                   <th colspan='3'></th>
                                </tr>
                            </tfoot>
                        </table>
                    </div><cut>$tag";
        return $tab;
    }
}
