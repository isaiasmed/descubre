<?php
if (!isset($_POST['nombre']) || !isset($_POST['tipo'])) {
    echo json_encode(['exito' => false, 'mensaje' => 'Datos incompletos']);
    exit();
}

if (!defined("RAIZ")) {
    define("RAIZ", dirname(dirname(dirname(__FILE__))));
}

require_once RAIZ . "/modulos/db.php";

$nombre = mb_strtoupper($_POST['nombre']);
$tipo = $_POST['tipo'];

try {
    $sentencia = $base_de_datos->prepare("INSERT INTO clientes (cliente, tipo) VALUES (?, ?)");
    $resultado = $sentencia->execute([$nombre, $tipo]);

    if ($resultado) {
        $ultimo_id = $base_de_datos->lastInsertId();
        
        echo json_encode(['exito' => true, 'mensaje' => 'Cliente agregado correctamente', 'id' => $ultimo_id]);
    } else {
        echo json_encode(['exito' => false, 'mensaje' => 'Error al agregar el cliente']);
    }
} catch (PDOException $e) {
    echo json_encode(['exito' => false, 'mensaje' => 'Error en la base de datos: ' . $e->getMessage()]);
}
?>
