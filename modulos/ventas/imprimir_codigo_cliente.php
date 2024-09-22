<?php
if (!defined("RAIZ")) {
    define("RAIZ", dirname(dirname(dirname(__FILE__))));
}
require_once RAIZ . "/modulos/funciones.php";
require_once RAIZ . "/modulos/ticket.php";
require_once RAIZ . "/modulos/db.php";
// Crear un array con el contenido del ticket
$contenido_ticket = array(
    array("texto" => "Hola Mundo", "estilo" => "")
);
$cliente=$_POST['id'];
// Llamar a la función para imprimir el ticket
imprime_codigo_cliente($cliente);

echo json_encode(array("success" => true, "message" => "Ticket impreso correctamente"));
?>

