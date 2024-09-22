<?php
require_once dirname(dirname(__DIR__)) . '/vendor/autoload.php';
require_once dirname(dirname(__DIR__)) . '/modulos/db.php';

use Mpdf\Mpdf;

try {
    // Asegúrate de que no haya salida antes de generar el PDF
    ob_start();
    
    // Obtener la fecha del parámetro POST
    $fecha = $_POST['fecha'] ?? date('Y-m-d');

    // Consultar los datos de la base de datos
    global $base_de_datos;
    $sql = "SELECT v.fecha, v.nombre_producto, v.cliente, c.tipo AS tipo_cliente
            FROM ventas v
            INNER JOIN clientes c ON UPPER(v.cliente) = UPPER(c.cliente)
            WHERE DATE(v.fecha) = ? AND v.tipo = 'Museo'
            ORDER BY c.tipo, v.fecha";
    
    $sentencia = $base_de_datos->prepare($sql);
    $sentencia->execute([$fecha]);
    $datos = $sentencia->fetchAll(PDO::FETCH_ASSOC);

    // Crear el contenido HTML del PDF
    $html = "<h1>Reporte de Consumo de Guías / Aseo</h1>";
    $html .= "<h2>Fecha: " . date('d/m/Y', strtotime($fecha)) . "</h2>";

    $tipoActual = '';
    foreach ($datos as $fila) {
        if ($tipoActual != $fila['tipo_cliente']) {
            if ($tipoActual != '') {
                $html .= "</table>";
            }
            $tipoActual = $fila['tipo_cliente'];
            $html .= "<h3>" . ucfirst($tipoActual) . "</h3>";
            $html .= "<table border='1' cellpadding='5'>
                        <tr>
                            <th>#</th>
                            <th>Hora</th>
                            <th>Guía</th>
                            <th>Producto</th>
                        </tr>";
            $contador = 1;
        }
        $html .= "<tr>
                    <td>{$contador}</td>
                    <td>" . date('H:i', strtotime($fila['fecha'])) . "</td>
                    <td>" . strtoupper($fila['cliente']) . "</td>
                    <td>{$fila['nombre_producto']}</td>
                  </tr>";
        $contador++;
    }
    if ($tipoActual != '') {
        $html .= "</table>";
    }

    $mpdf = new Mpdf();
    $mpdf->WriteHTML($html);
    
    // Limpia cualquier salida en el búfer
    ob_end_clean();
    
    // Genera el PDF
    $pdfContent = $mpdf->Output('', 'S');
    
    // Envía los encabezados correctos
    header('Content-Type: application/pdf');
    header('Content-Length: ' . strlen($pdfContent));
    header('Content-Disposition: inline; filename="reporte_guias_' . $fecha . '.pdf"');
    
    // Envía el contenido del PDF
    echo $pdfContent;
    exit;
} catch (\Exception $e) {
    echo 'Error al generar el PDF: ' . $e->getMessage();
}
