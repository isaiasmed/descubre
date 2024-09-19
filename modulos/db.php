<?php  
$usuario = "sql3732098";
$contraseña = "QvVQLlriuY";
const NOMBRE_BASE_DE_DATOS = "sql3732098";
const HOST = "sql3.freemysqlhosting.net";
try {
    $base_de_datos = new PDO('mysql:host='.HOST.';dbname=' . NOMBRE_BASE_DE_DATOS, $usuario, $contraseña);
    $base_de_datos->query("set names utf8;");
	$base_de_datos->setAttribute( PDO::ATTR_EMULATE_PREPARES, FALSE );
	$base_de_datos->setAttribute( PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION );
	$base_de_datos->setAttribute( PDO::ATTR_DEFAULT_FETCH_MODE, PDO::FETCH_ASSOC  );
} catch (PDOException $e) {
	echo json_encode( "Error fatal con la base de datos: " . $e->getMessage() );
}
?>