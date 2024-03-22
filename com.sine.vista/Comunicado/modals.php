<!--MODAL PARA VER ARCHIVOS-->
<div class="modal fade shadow-lg rounded rounded-5" id="archivo"  tabindex="-1" aria-labelledby="staticBackdropLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
        <div class="modal-header">
                <h4 class="modal-title fs-5 fw-bold" id="label-nuevo-producto">Tabla de archivos adjuntos</h4>
                <button type="button" id="btn-close-modal" class="btn-close" data-bs-dismiss="modal" onclick="cerrarModal()" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <div class="row">
                    <div class="col-md-4">
                        <div class="col-12">
                            <table class="table table-responsive table-secondary border border-start-0 border-dark" id="listaarchivo">
                            </table> 
                        </div>
                    </div>
                    <div class="col-md-8 d-flex justify-content-center " id="foto">
                        <embed src="" class="col-12 mx-auto" type="application/pdf"/>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!--VER ARCHIVOS EN FORMULARIO-->
<div class="modal fade shadow-lg rounded rounded-5" id="tabla"  tabindex="-1" aria-labelledby="staticBackdropLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
        <div class="modal-header">
                <h4 class="modal-title fs-5 fw-bold" id="label-nuevo-producto">Visualización de imagen</h4>
                <button type="button" id="btn-close-modal" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <div id="fotito" class="d-flex justify-content-center">
                <embed src="" class="col-md-8"  type="application/pdf"/>
                </div>
            </div>
        </div>
    </div>
</div>
