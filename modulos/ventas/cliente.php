<?php
if ( !isset( $_GET["guia"] ) && !isset( $_GET["aseo"] )  ) exit();
$cliente = isset($_GET["guia"])?$_GET["guia"]:$_GET["aseo"];


if ( !defined( "RAIZ" ) ) 
{
	define( "RAIZ", dirname( dirname( dirname( __FILE__ ) ) ) );
}
require_once RAIZ . "/modulos/db.php";
require_once RAIZ . "/modulos/ventas/ventas.php";
$resultado = comprueba_si_existe_cliente( $cliente );
echo json_encode( $resultado );
?>