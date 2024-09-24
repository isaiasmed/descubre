/***
 *     __ __    ___  ____   ______   ____  _____
 *    |  T  |  /  _]|    \ |      T /    T/ ___/
 *    |  |  | /  [_ |  _  Y|      |Y  o  (   \_
 *    |  |  |Y    _]|  |  |l_j  l_j|     |\__  T
 *    l  :  !|   [_ |  |  |  |  |  |  _  |/  \ |
 *     \   / |     T|  |  |  |  |  |  |  |\    |
 *      \_/  l_____jl__j__j  l__j  l__j__j \___j
 *
 *    Última modificación: 09 de junio de 2016 por Parzibyte
 *    Entorno: Desarrollo
 */
var productos_vender = [],
    ayudante_posicion = 0,
    puede_salir = true,
    total = 0,
    intervalo_ayudante_nprogress,
    TECLA_F1 = 112,f
    TECLA_F2 = 113,
    TECLA_F3 = 114,
    TECLA_F4 = 115,
    TECLA_F8 = 119,
    TECLA_ENTER = 13,
    TECLA_MENOS = 109,
    EL_CLIENTE_USA_TICKET = true;

window.onbeforeunload = function () {
    if (puede_salir == false || productos_vender.length > 0) return "Todavía no has completado la venta.";
};

$(document)
    .ready(function () {
        principal();
    })
    .ajaxStart(function () {
        NProgress.start();
        intervalo_ayudante_nprogress = setInterval(function () {
            NProgress.inc();
        }, 1000);
    })
    .ajaxStop(function () {
        clearInterval(intervalo_ayudante_nprogress);
        NProgress.done();
    });


function Producto(rowid, codigo, nombre, cantidad, precio, posicion, familia, precio_compra) {
    this.rowid = rowid;
    this.codigo = codigo;
    this.nombre = nombre;
    this.cantidad = cantidad;
    this.precio_venta = parseFloat(precio);
    this.posicion = posicion;
    this.total = this.cantidad * this.precio_venta;
    this.familia = familia;
    this.precio_compra = parseFloat(precio_compra);
    this.utilidad = (this.precio_venta - this.precio_compra >= 0 ? this.precio_venta - this.precio_compra : 0 );
};


Producto.prototype.aumentaCantidad = function () {
    this.cantidad += 1;
    this.refrescaTotal();
};


Producto.prototype.setCantidad = function (cantidad) {
    this.cantidad = parseFloat(cantidad);
    this.refrescaTotal();
};

Producto.prototype.refrescaTotal = function () {
    this.total = this.cantidad * this.precio_venta;
};


function isInt(n) {
    return n % 1 === 0;
}


function principal() {
    // Seleccionar todo el contenido del input #cliente al hacer foco
    $("#cliente").on("focus", function() {
        $(this).select();
    });
    
    autocompletado_input();
    escuchar_elementos();
    dibujar_productos();
    $("li#elem_ventas").addClass("active");
    $("#cliente").focus();
}


function dame_posicion_producto(posicion) {
    for (var x = 0; x < productos_vender.length; x++) {
        if (productos_vender[x].posicion === posicion) return x;
    }
    return -1;
}


function quita_producto_local(posicion) {
    var indice = dame_posicion_producto(posicion);
    if (indice !== -1) {
        productos_vender.splice(indice, 1);
        dibujar_productos();
        $("#codigo_producto").focus();
    }
}
function dame_total_productos_locales() {
    var numero_total = 0;
    for (var i = productos_vender.length - 1; i >= 0; i--) {
        numero_total += productos_vender[i].cantidad;
    }
    return numero_total;
}

function agrega_producto_local(producto) {
    var ya_esta_en_la_lista = producto_ya_esta_en_lista(producto.codigo);
    if (ya_esta_en_la_lista !== true) {
        var _producto = new
            Producto(
            producto.rowid,
            producto.codigo,
            producto.nombre,
            1,
            producto.precio_venta,
            ayudante_posicion,
            producto.familia,
            producto.precio_compra
        );
        if (producto.agregar === 1) {
            _producto.items = producto.items;
        }
        productos_vender.push(_producto);
        ayudante_posicion++;
    }
    dibujar_productos();
}


function producto_ya_esta_en_lista(codigo) {
    for (var x = 0; x < productos_vender.length; x++) {
        if (productos_vender[x].codigo === codigo) {
            productos_vender[x].aumentaCantidad();
            return true;
        }
    }
    return false;
}


function quitar_ultimo_producto() {
    $("#codigo_producto").val("");
    if (productos_vender.length > 0) {
        productos_vender.splice(-1, 1);
        dibujar_productos();
    }
    $("#codigo_producto").focus();
    --ayudante_posicion;
}

function dibujar_productos() {
    if (productos_vender.length <= 0) {
        $("#contenedor_tabla")
            .empty()
            .html(
                $("<h2>")
                    .addClass('text-center')
                    .html('Aquí aparecerán los productos que agregues<br><i class = "fa fa-4x fa-cart-plus"></i>')
            );
        $("#contenedor_total").parent().hide();
        return;
    }

    $("#contenedor_tabla")
        .empty()
        .append(
            $("<table>")
                .addClass('table table-striped table-hover table-condensed')
                .append(
                    $("<thead>")
                        .append(
                            $("<tr>")
                                .append(
                                    $("<th>")
                                        .html('Código'),

                                    $("<th>")
                                        .html('Producto'),

                                    $("<th>")
                                        .html('Precio'),

                                    $("<th>")
                                        .html('Cantidad'),

                                    $("<th>")
                                        .html('Total'),

                                    $("<th>")
                                        .html('Quitar')
                                )
                        )
                )
                .append(
                    $("<tbody>")
                )
        );
    var ayudante_total = 0;
    for (var i = productos_vender.length - 1; i >= 0; i--) {
        
        $ing = '';
        if (productos_vender[i].hasOwnProperty('items') && productos_vender[i].items.length > 0) {
            $ing = '';
            for (var j = 0; j < productos_vender[i].cantidad; j++) {
                $ing += '<span style="display: block;border: 1px solid #ddd;padding: 5px;margin-top: 10px;">';
                productos_vender[i].items.forEach(function(item, index) {
                    if (item.nombre === "Guisado") {
                        $ing += '<select class="guisado-select" name="productos[' + i + '][items][' + j + '][' + index + ']" data-item-id="' + item.id_agregar + '">';
                        $ing += '<option value="">Seleccione un guisado</option>';
                        // Aquí deberías agregar opciones del select con los guisados de la base de datos
                        $ing += '</select>';
                    } else {
                        $ing += '<label><input type="checkbox" checked class="item-checkbox ingre" name="productos[' + i + '][items][' + item.item + ']" data-item-id="' + item.id_agregar + '"> ' + item.item + '</label><br>';
                    }
                });
                $ing += '</span>';
            }
        }
        ayudante_total += productos_vender[i].total;
        $("#contenedor_tabla tbody")
            .append(
                $("<tr>")
                    .append(
                        $("<td>")
                            .html(productos_vender[i].codigo),

                        $("<td>")
                            .html(productos_vender[i].nombre + $ing),

                        $("<td>")
                            .html("$" + productos_vender[i].precio_venta),

                        $("<td>")
                            .html(
                                $("<div>")
                                    .addClass('form-group')
                                    .html(
                                        $("<input>")
                                            .attr("placeholder", "Cantidad")
                                            .attr("type", "number")
                                            .attr("data-pos", productos_vender[i].posicion)
                                            .addClass('form-control modificar-cantidad')
                                            .val(productos_vender[i].cantidad)
                                    )
                            ),

                        $("<td>")
                            .html("$" + Math.round(productos_vender[i].total * 100) / 100),

                        $("<td>")
                            .html(
                                $("<button>")
                                    .addClass('btn btn-danger quitar-producto')
                                    .attr("data-pos", productos_vender[i].posicion)
                                    .html(
                                        $("<i>")
                                            .addClass('fa fa-trash')
                                    )
                            )
                    )
            );
    }
    ayudante_total = Math.round(ayudante_total * 100) / 100;
    $("#contenedor_total").text("$" + ayudante_total).parent().show();
    total = ayudante_total;
}

$('.addProd').click(function(){
    var e = $.Event("keypress");
    e.keyCode = 13; // # Some key code value
    $('#codigo_producto').val($(this).data('prod'));
    comprueba_si_existe_codigo($(this).data('prod'));
});





function preparar_para_realizar_venta() {
    if (productos_vender.length > 0) {
        $("#modal_procesar_venta").modal("show");
        // Establecer el valor por defecto del select #tipo a ""
        $("#tipo").val("Efectivo");
        $("#defectivo").show();
        $("#contenedor_total_modal").text("$" + total).parent().show();
    }   
}
function deshabilita_para_venta() {
    $("input, button").prop("disabled", true);
    puede_salir = false;
}
function habilita_para_venta() {
    $("input, button").prop("disabled", false);
    puede_salir = true;
}
function realizar_venta(productos, total, cambio, cliente, ticket, tipo,ingredientes) {
    cambio = parseFloat(cambio);
    if (cambio < 0) cambio = 0;
    deshabilita_para_venta();
    $("#realizar_venta")
        .html(
            $("<i>")
                .addClass('fa fa-spin fa-spinner')
        )
        .append(" Cargando...")
        .removeClass('btn-warning btn-info')
        .addClass('btn-warning');
    productos = JSON.stringify(productos);
    ticket = JSON.stringify(ticket);
    ticket = true;
    tipo = $("#tipo").val();
    ingredientes = JSON.stringify(ingredientes);
    var numero_productos = dame_total_productos_locales();
    if (!EL_CLIENTE_USA_TICKET) ticket = false;
    $.post('./modulos/ventas/realizar_venta.php', {
        "productos": productos,
        "total": total,
        "ticket": ticket,
        "cambio": cambio,
        "cliente": cliente,
        "tipo": tipo,
        "ingredientes": ingredientes
    }, function (respuesta) {
        habilita_para_venta();
        ayudante_posicion = 0;
        respuesta = JSON.parse(respuesta);
        if (respuesta === true) {
            $("#realizar_venta")
                .html(
                    $("<i>")
                        .addClass('fa fa-check-square')
                )
                .append(" ¡Venta correcta! ")
                .removeClass('btn-warning btn-info')
                .addClass('btn-info');
            $("#modal_procesar_venta").modal("hide");
            cancelar_venta();
            $("#codigo_producto").focus();
            $("#pago_usuario").val("");
            $("#contenedor_cambio").parent().hide();
        } else {
            console.log("Error, la respuesta es:", respuesta);
        }
    });
}


function cancelar_venta() {
    if (productos_vender.length > 0) {
        productos_vender.length = 0;
        dibujar_productos();
        ayudante_posicion = 0;
    }
    $("#codigo_producto").focus();
    puede_salir = true;
}

function comienza_venta() {
    $("#codigo_producto").focus();
    puede_salir = true;
}

function cambia_cantidad() {
    if (productos_vender.length > 0) {
        $('#contenedor_tabla tbody tr').first().find('input').focus();
    }
}


function escuchar_elementos() {
    $("#tipo").change(function() {
        if ($(this).val() !== "Efectivo") {
            var totalSinSigno = $("#contenedor_total_modal").text().replace("$", "");
            $("#pago_usuario").val(totalSinSigno*1);
            $("#defectivo").hide();
        } else {
            $("#pago_usuario").val("");
            $("#defectivo").show();
        }
    });


    $("#imprimir_ticket").click(function () {
        $("#pago_usuario").focus();
    });

    $(window).resize(function (event) {
        $("#codigo_producto").css("width", $(".btn-group.btn-group-justified").width());
    });
    $("#quitar_ultimo_producto").click(function () {
        quitar_ultimo_producto();
    });
    $("#preparar_venta").click(function () {
        preparar_para_realizar_venta();
    });
    $("#cancelar_toda_la_venta").click(function () {
        cancelar_venta();
    });
     $("#mod_cliente").click(function () {
        $("#cliente").focus();
    });
    
    $("#pago_usuario").keyup(function (evento) {
        $(this).parent().removeClass('has-error');
        var pago = $(this).val(),
            cambio = pago - total;
        if (cambio >= 0 && !isNaN(pago)) {
            $("#contenedor_cambio").text("$" + cambio).parent().show();
        } else {
            $("#contenedor_cambio").parent().hide();
        }
        if (evento.keyCode === 13) {
            if (cambio >= 0 && !isNaN(pago)) {
                var cliente =$("#cliente").val();
                var ingredientes =$(".ingre").serializeArray();
                console.log("Ingredientes");
                console.log(ingredientes);
                var tipo = $("#tipo").val();
                realizar_venta(productos_vender, total, cambio,cliente, $("#imprimir_ticket").prop("checked"),tipo, ingredientes);
            } else {
                $(this).animateCss("shake");
                $(this).parent().addClass('has-error');
            }
        }
    });

    
    $("#realizar_venta").click(function () {
        var pago = $("#pago_usuario").val(),
            cambio = pago - total;
        if (cambio >= 0 && !isNaN(pago)) {
            var cliente =$("#cliente").val();
            var ingredientes =$(".ingre").serializeArray();
            console.table(ingredientes);
            var tipo=$("#tipo").val();
            realizar_venta(productos_vender, total, cambio, cliente , $("#imprimir_ticket").prop("checked"),tipo,  ingredientes);
        } else {
            $("#pago_usuario").animateCss("shake");
            $("#pago_usuario").parent().addClass('has-error');
        }
    });


    $("#modal_procesar_venta").on("shown.bs.modal", function () {
        $("#pago_usuario").focus();
    });
    $("#modal_procesar_venta").on("hidden.bs.modal", function () {
        $("#realizar_venta").html("Realizar venta");
        $("#pago_usuario").val("").parent().removeClass('has-error');
        $("#codigo_producto").focus();
    });

    $("body").keydown(function (evento) {
        //console.log(evento);
        if (evento.ctrlKey) evento.preventDefault();
        switch (evento.keyCode) {
            case TECLA_F3:
                preparar_para_realizar_venta();
                evento.preventDefault();
                break;
            case TECLA_F1:
                comienza_venta();
                evento.preventDefault();
                break;
            case TECLA_F8:
                cambia_cantidad();
                evento.preventDefault();
                break;
        }
    });


    $(document).keydown(function(event) {
        if (!event.ctrlKey){ return true; }
        var listcomida=String.fromCharCode(event.which);
        switch (listcomida) {
            case '1':
                var selprod='TORTJAM';
                $('#codigo_producto').val(selprod).focus();
                break;
            case '2':
                comprueba_si_existe_codigo('AGUADIA1LT');
                break;
        }
    });

    // Limpiar el campo de búsqueda y los resultados al mostrar el modal de clientes
    $('#modal_clientes').on('show.bs.modal', function () {
        $("#buscando_cliente").val('');
        $("#resultados_busqueda").empty();
    });

    $("#cliente").keydown(function (evento) {
        console.log(evento);
        if (evento.keyCode === TECLA_ENTER || evento.which === 13) {
            evento.preventDefault();
            var contenido = $(this).val().trim();
            if (contenido.startsWith('...')) {
                $.ajax({
                    url: "./modulos/ventas/cliente.php?"+ contenido.substring(3, contenido.indexOf('&')) + "=" + contenido.substring(contenido.indexOf('&') + 1),
                    method: 'GET',
                    dataType: 'json',
                    success: function(respuesta) {
                        if (respuesta) {
                            $("#cliente").val(respuesta.cliente);
                            if(respuesta.tipo=='aseo'){
                                comprueba_si_existe_codigo('ALMTASEO');
                            }else{
                                comprueba_si_existe_codigo(156845632);
                            }
                            $("#tipo").val("Museo");
                            $("#modal_procesar_venta").modal("show");
                        } else {
                            console.log("Cliente no encontrado");
                        }
                    },
                    error: function(xhr, status, error) {
                        console.error("Error en la búsqueda del cliente:", error);
                        $("#codigo_producto").focus();
                    }
                });
            } else {
                $("#codigo_producto").focus();
            }
        }
    });
    // Manejar el evento de clic en el botón de búsqueda de cliente
    $("#btn_buscar_cliente").click(function() {
        buscarCliente();
    });

    // Función para buscar cliente
    function buscarCliente() {
        var buscar = $("#buscando_cliente").val();
        $.ajax({
            url: "./modulos/ventas/buscar_cliente.php",
            method: "POST",
            data: { "busqueda": buscar },
            dataType: "json",
            success: function(respuesta) {
                mostrarResultadosBusqueda(respuesta);
            },
            error: function(xhr, status, error) {
                console.error("Error en la búsqueda del cliente:", error);
            }
        });
    }

    // Función para mostrar resultados de búsqueda
    function mostrarResultadosBusqueda(clientes) {
        var resultadosDiv = $("#resultados_busqueda");
        resultadosDiv.empty();

        if (clientes.length > 0) {
            var lista = $("<ul>").addClass("list-group");
            clientes.forEach(function(cliente) {
                var item = $("<li>").addClass("list-group-item");
                var span = $("<span>")
                    .css({
                        'color': '#040676',
                        'text-decoration': 'underline',
                        'cursor': 'pointer',
                        'margin-left':'10px'
                    })
                    .text(cliente.cliente + " - " + cliente.tipo)
                    .click(function() {
                        seleccionarCliente(cliente.cliente);
                    });
                var boton = $("<button>")
                    .addClass("btn btn-sm btn-info mr-4")
                    .text("Imprimir Código")
                    .css({
                        'margin-right':'20px'
                    })
                    .click(function() {
                        imprimirCodigoCliente(cliente.id);
                    });
                item.append(span).prepend(boton);
                lista.append(item);
            });
            resultadosDiv.append(lista);
        } else {
            resultadosDiv.append("<p>No se encontraron resultados.</p>");
        }
    }

    function imprimirCodigoCliente(idCliente) {
        $.ajax({
            url: "./modulos/ventas/imprimir_codigo_cliente.php",
            method: "POST",
            data: { id: idCliente },
            success: function(response) {
                console.log("Código de cliente impreso");
            },
            error: function(xhr, status, error) {
                console.error("Error al imprimir código de cliente:", error);
            }
        });
    }

    // Función para seleccionar un cliente
    function seleccionarCliente(cliente) {
        $("#cliente").val(cliente);
        $("#modal_clientes").modal("hide");
        comprueba_si_existe_codigo(156845632);
        $("#tipo").val("Museo");
        $("#modal_procesar_venta").modal("show");
        // Llamar a la función después de seleccionar un cliente
        limpiarYOcultarFormularioCliente();
    }

    // Limpiar el formulario de agregar cliente y ocultarlo
    function limpiarYOcultarFormularioCliente() {
        $("#nombre_cliente").val("");
        $("#tipo_cliente").val("guia"); // Establecer el valor predeterminado
        $("#formulario_nuevo_cliente").hide();
    }
    
    // Manejar el envío del formulario para agregar nuevo cliente
    $("#form_nuevo_cliente").submit(function(e) {
        e.preventDefault();
        var nombre = $("#nombre_cliente").val();
        var tipo = $("#tipo_cliente").val();

        $.ajax({
            url: "./modulos/ventas/agregar_cliente.php",
            method: "POST",
            data: { nombre: nombre, tipo: tipo },
            dataType: "json",
            success: function(respuesta) {
                if (respuesta.exito) {
                    alert("Cliente agregado con éxito");
                    $("#modal_clientes").modal("hide");
                    seleccionarCliente(nombre);
                } else {
                    alert("Error al agregar el cliente: " + respuesta.mensaje);
                }
            },
            error: function(xhr, status, error) {
                console.error("Error al agregar el cliente:", error);
            }
        });
    });

    // Acción para mostrar el formulario de agregar cliente
    $("#btn_mostrar_formulario").click(function() {
        $("#formulario_nuevo_cliente").slideToggle();
    });

    $("#codigo_producto").keydown(function (evento) {

        if (evento.ctrlKey) evento.preventDefault();
        switch (evento.keyCode) {
            case TECLA_ENTER:
                comprueba_si_existe_codigo($(this).val());
                break;
            case TECLA_F1:
                preparar_para_realizar_venta();
                evento.preventDefault();
                break;
            case TECLA_F2:
                cancelar_venta();
                evento.preventDefault();
                break;
            case TECLA_F3:
                comienza_venta();
                evento.preventDefault();
                break;
            case TECLA_F4:
                $('#cliente').focus();
                evento.preventDefault();
                break;
            case TECLA_MENOS:
                quitar_ultimo_producto();
                evento.preventDefault();
                break;
            default:
                // statements_def
                break;
        }
    });

    $(document).on("keyup", ".modificar-cantidad", function (evento) {
        $(this).parent().removeClass('has-error');
        if (evento.keyCode === 13) {
            var nueva_cantidad = $(this).val(),
                posicion = dame_posicion_producto($(this).data("pos"));
            if (
                nueva_cantidad.length > 0
                && nueva_cantidad > 0
                && !isNaN(nueva_cantidad)
            ) {
                productos_vender[posicion].setCantidad(nueva_cantidad);
                dibujar_productos();
                $("#codigo_producto").focus();
            } else {
                $(this).animateCss('shake');
                $(this).parent().addClass('has-error');
            }
        }

    });

    $(document).on("mouseout", ".modificar-cantidad", function () {
        $(this).parent().removeClass('has-error');
        var nueva_cantidad = $(this).val(),
            posicion = dame_posicion_producto($(this).data("pos"));
        if (
            nueva_cantidad.length > 0
            && nueva_cantidad > 0
            && !isNaN(nueva_cantidad)
        ) {
            productos_vender[posicion].setCantidad(nueva_cantidad);
            dibujar_productos();
            $(this).focus();
        } else {
            $(this).animateCss('shake');
            $(this).parent().addClass('has-error');
            $(this).focus();
        }
    });


    $(document).on("click", ".quitar-producto", function () {
        var posicion = $(this).data("pos");
        quita_producto_local(posicion);
    });
}

function autocompletado_input() {
    var opciones = {
        theme: "bootstrap",

        url: function (busqueda) {
            var sugerencias = "./modulos/ventas/autocompletado.php?busqueda=" + busqueda;
            return sugerencias;
        },

        getValue: function (producto) {
            return producto.nombre;
        },

        requestDelay: 200,

        list: {
            maxNumberOfElements: 20,


            onChooseEvent: function () {
                var producto_seleccionado = $("#codigo_producto").getSelectedItemData();
                comprueba_si_existe_codigo(producto_seleccionado.codigo);
            },

            showAnimation: {
                type: "fade", //normal|fade|fade
                time: 100
            },

            hideAnimation: {
                type: "fade", //normal|fade|fade
                time: 100
            }
        }
    };

    $("#codigo_producto").easyAutocomplete(opciones);
}


function comprueba_si_existe_codigo(codigo) {
    $.post('./modulos/ventas/comprueba_si_existe_codigo.php', {"codigo": codigo}, function (respuesta) {
        $("#codigo_producto")
            .val("")
            .trigger(
                jQuery.Event(
                    'keyup', {
                        keyCode: 27,
                        which: 27
                    }
                )
            )
            .focus();
        respuesta = JSON.parse(respuesta);
        if (respuesta !== false) {
            agrega_producto_local(respuesta);
        }

    });
}

