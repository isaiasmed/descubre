<?php
if( !isset( $_POST["fecha"] ) ) exit();
if ( !defined( "RAIZ" ) ) 
{
	define( "RAIZ", dirname( dirname( dirname( __FILE__ ) ) ) );
}

require_once RAIZ . "/modulos/db.php";
$fecha = $_POST['fecha'];

// Preparar la consulta SQL
$sql = "SELECT v.fecha, v.nombre_producto, v.cliente, c.tipo AS tipo_cliente
        FROM ventas v
        INNER JOIN clientes c ON UPPER(v.cliente) = UPPER(c.cliente)
        WHERE DATE(v.fecha) = ? AND v.tipo = 'Museo'";

global $base_de_datos;
$sentencia = $base_de_datos->prepare($sql);

$sentencia->execute([$fecha]);
//$consultaPreparada = $sentencia->queryString;


$datos = array();

// Procesar los resultados
while ($fila = $sentencia->fetch(PDO::FETCH_ASSOC)) {
    $datos[] = array(
        'fecha' => $fila['fecha'],
        'nombre_producto' => $fila['nombre_producto'],
        'cliente' => $fila['cliente'],
        'tipo_cliente' => $fila['tipo_cliente']
    );
}

// Devolver los datos en formato JSON
echo json_encode($datos);