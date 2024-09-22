<?php
if ( !isset( $_POST["busqueda"] ) ) exit();
if ( !defined( "RAIZ" ) ) 
{
	define( "RAIZ", dirname( dirname( dirname( __FILE__ ) ) ) );
}
$busqueda = $_POST["busqueda"];
require_once RAIZ . "/modulos/db.php";
require_once RAIZ . "/modulos/ventas/ventas.php";
$datos = devolver_datos_clientes( $busqueda );
echo json_encode( $datos );
?>