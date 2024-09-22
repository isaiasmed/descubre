
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
                        <span class="hidden-xs"><kbd>F4</kbd> Cambiar Cliente</span>
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
                <div class="input-group">
                    <input class="form-control" type="text" id="cliente" value="PUBLICO GENERAL"
                           placeholder="Cliente">
                    <span class="input-group-btn">
                        <button class="btn btn-default" type="button" id="buscar_cliente" data-toggle="modal" data-target="#modal_clientes">
                            <i class="fa fa-search"></i> Buscar
                        </button>
                    </span>
                </div>
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
                <div class="form-group">
                    <label for="tipo">Tipo de pago</label>
                    <select id="tipo" class="form-control">
                        <option value="Transferencia">Transferencia</option>
                        <option value="Efectivo">Efectivo</option>
                        <option value="TPV">TPV</option>
                        <option value="Crédito">Crédito</option>
                        <option value="Museo">Museo</option>
                    </select>
                </div>
                <h2 hidden="hidden"><strong>Total: </strong><span id="contenedor_total_modal"></span></h2>
                <div id="defectivo" class="row" style="display:none;s">
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
                    <h2 hidden="hidden">Cambio: <span id="contenedor_cambio"></span></h2>
                </div>
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


<!-- Modal para consultar y agregar clientes -->
<div class="modal fade" id="modal_clientes" tabindex="-1" role="dialog" aria-labelledby="modalClientesLabel">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-label="Cerrar"><span aria-hidden="true">&times;</span></button>
                <h4 class="modal-title" id="modalClientesLabel">Gestión de Clientes</h4>
            </div>
            <div class="modal-body">
                <!-- Formulario para buscar clientes -->
                <div class="form-group">
                    <label for="buscar_cliente">Buscar cliente:</label>
                    <div class="input-group">
                        <input type="text" class="form-control" id="buscando_cliente" placeholder="Nombre o ID del cliente">
                        <span class="input-group-btn">
                            <button id="btn_buscar_cliente" class="btn btn-primary">Buscar</button>
                        </span>
                    </div>
                </div>

                <!-- Resultados de la búsqueda -->
                <div id="resultados_busqueda" class="mt-3">
                    <!-- Aquí se mostrarán los resultados de la búsqueda -->
                </div>

                <!-- Botón para mostrar el formulario -->
                <button id="btn_mostrar_formulario" class="btn btn-primary mt-4">Mostrar formulario para agregar cliente</button>

                <!-- Formulario para agregar nuevo cliente (oculto inicialmente) -->
                <div id="formulario_nuevo_cliente" style="display: none;">
                    <h5 class="mt-4">Agregar nuevo cliente</h5>
                    <form id="form_nuevo_cliente">
                        <div class="form-group">
                            <label for="nombre_cliente">Nombre:</label>
                            <input type="text" class="form-control" id="nombre_cliente" required>
                        </div>
                        <div class="form-group">
                            <label for="tipo_cliente">Tipo:</label>
                            <select class="form-control" id="tipo_cliente" required>
                                <option value="guia">Guía</option>
                                <option value="aseo">Aseo</option>
                            </select>
                        </div>
                        <button type="submit" class="btn btn-success">Agregar Cliente</button>
                    </form>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-default" data-dismiss="modal">Cerrar</button>
            </div>
        </div>
    </div>
</div>

<link rel="stylesheet" href="./css/eac.css">
<script src="./js/ventas.js?i=1.<?php echo uniqid();?>"></script>
<script src="./lib/eac.js"></script>