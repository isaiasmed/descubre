
<link rel="stylesheet" href="./css/abc.css">
<div class="container-fluid">
    <div class="row">
        <div class="col-xs-12">
            <div class="btn-group btn-group-justified">
                <div class="btn-group">
                    <button id="quitar_ultimo_producto" type="button" class="btn btn-warning">
                        <i class="fa-minus fa visible-xs"></i>
                        <span class="hidden-xs"><kbd>-</kbd> Quitar último producto</span>
                    </button>
                </div>
                <div class="btn-group">
                    <button id="preparar_venta" type="button" class="btn btn-success">
                        <i class="fa-check-circle-o fa visible-xs"></i>
                        <span class="hidden-xs"><kbd>F1</kbd> Realizar venta</span>
                    </button>
                </div>
                <div class="btn-group">
                    <button id="cancelar_toda_la_venta" type="button" class="btn btn-danger">
                        <i class="fa-ban fa visible-xs"></i>
                        <span class="hidden-xs"><kbd>F2</kbd> Cancelar toda la venta</span>
                    </button>
                </div>
				<div class="btn-group">
                    <button id="cancelar_toda_la_venta" type="button" class="btn btn-info" disabled>
                        <i class="fa-ban fa visible-xs"></i>
                        <span class="hidden-xs"><kbd>F3</kbd> Comenzar Venta</span>
                    </button>
                </div>
                <div class="btn-group">
                    <button id="mod_cliente" type="button" class="btn btn-info" style="background:#011759;border: 1px solid #011759;">
                        <i class="fa-ban fa visible-xs"></i>
                        <span class="hidden-xs"><kbd>F4</kbd> Comenzar Venta</span>
                    </button>
                </div>
				<div class="btn-group">
                    <button id="cancelar_toda_la_venta" type="button" class="btn btn-default" disabled>
                        <i class="fa-ban fa visible-xs"></i>
                        <span class="hidden-xs"><kbd>F8</kbd> Modificar Cantidad</span>
                    </button>
                </div>
            </div>
            <div class="form-group">
                <label for="cliente">Nombre o referencia cliente</label>
                <input class="form-control" type="text" id="cliente" value="PUBLICO GENERAL"
                       placeholder="Cliente">
            </div>
            <div class="form-group">
                <label for="codigo_producto">Comienza a escribir o escanea el código</label>
                <input class="form-control" type="text" id="codigo_producto"
                       placeholder="Comienza a escribir o escanea el código">
            </div>            
            <h1 hidden="hidden"><strong>Total: </strong><span id="contenedor_total"></span></h1>
        </div>
    </div>
    <br>
    <div class="row">
        <div class="col-xs-12 table-responsive" id="contenedor_tabla">

        </div>
    </div>
	<nav class="navbar navbar-inverse navbar-fixed-bottom">
	  <div class="container-fluid">
		<div class="navbar-header">
		  <a class="navbar-brand" href="#">Productos Destacados</a>
		</div>
		<ul class="nav navbar-nav">
		  <li><a href="#" class="addProd" data-prod="TORTJAM"><kbd>CTRL + 1</kbd> Torta de Guisado </a></li>
		  <li><a href="#" class="addProd" data-prod="AGUADIA1LT"><kbd>CTRL + 2</kbd> Agua del Día 1 lt. (Varios sabores)</a></li>
		  <li><a href="#" class="addProd" data-prod="AGUAKIRK1LT"><kbd>CTRL + 3</kbd>Agua Litro Kirkland</a></li>
		</ul>
	  </div>
	</nav>
</div>


<div id="modal_procesar_venta" class="modal fade" role="dialog">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal">&times;</button>
                <h4 class="modal-title">Realizar venta</h4>
            </div>
            <div class="modal-body">
                <h2 hidden="hidden"><strong>Total: </strong><span id="contenedor_total_modal"></span></h2>
                <div class="row">
                    <div class="form-group">
                        <div class="col-xs-12 col-md-10">
                            <label for="pago_usuario">El cliente paga con...</label>
                            <input placeholder="El cliente paga con..." type="number" id="pago_usuario"
                                   class="form-control">
                        </div>
                    </div>
                    <div class="col-xs-12 col-md-2">
                        <div class="checkbox checkbox-primary checkbox-circle">
                            <input type="checkbox" id="imprimir_ticket" checked>
                            <label for="imprimir_ticket">
                                Ticket <i class="fa fa-ticket"></i>
                            </label>
                        </div>
                    </div>
                </div>
                <h2 hidden="hidden">Cambio: <span id="contenedor_cambio"></span></h2>
            </div>
            <div class="modal-footer">
                <div class="row">
                    <div class="col-xs-12">
                        <div hidden="hidden" class="alert">
                            <span id="mostrar_resultados_eliminar"></span>
                        </div>
                    </div>
                </div>
                <div class="row">
                    <div class="col-xs-12">
                        <button id="realizar_venta" class="form-control btn btn-info">Realizar venta</button>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
<link rel="stylesheet" href="./css/eac.css">
<script src="./js/ventas.js"></script>
<script src="./lib/eac.js"></script>