<?php
if (!isset($_SESSION)) exit("<script>window.location.href = '../';</script>");
?>
<?php if ($_SESSION["administrador"] !== 1) exit('<h1 class="text-center">Lo sentimos, solamente el administrador puede ver esta sección<br><br><i class="fa fa-hand-paper-o fa-4x"></i></h1>'); ?>
<div class="row visible-print-block">
    <h1 class="text-center">Reporte sobre caja</h1>
</div>
<div class="row hidden-print">
    <div class="col-xs-12">
        <p class="h5 text-justify">Elegir el dia para reporte de Consumos de Guias / Aseo<br>
        </p>
    </div>
</div>
<div class="row hidden-print">
    <div class="col-xs-6 text-center">
        <h4>Fecha</h4>
        <input id="fecha_inicio_guias" type="date" class="form-control">
    </div>
</div>
<br>
<div class="row hidden-print">
    <div class="col-xs-12">
        <button class="btn btn-info form-control" id="generar_lista">Generar reporte y lista<i class="fa fa-file-pdf-o"></i>
        </button>
    </div>
</div>
<div class="row"><br>
    <div class="col-xs-12">
        <div id="contenedor_tabla" class="table-responsive">
        </div>
    </div>
</div>
<script src="./js/reporte-caja.js"></script>