<?php  
$usuario = "vesta_yardbornis";
$contraseña = "Vesta2024#";
const NOMBRE_BASE_DE_DATOS = "vesta_yardbornis";
const HOST = "6re.h.filess.io:3307";
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