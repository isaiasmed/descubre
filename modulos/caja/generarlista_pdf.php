<?php
require_once dirname(dirname(__DIR__)) . '/vendor/autoload.php';
require_once dirname(dirname(__DIR__)) . '/modulos/db.php';

use Mpdf\Mpdf;

try {
    ob_start();
    
    $fecha = $_GET['fecha'] ?? date('Y-m-d');

    global $base_de_datos;
    $sql = "SELECT v.fecha, v.nombre_producto, v.cliente, c.tipo AS tipo_cliente
            FROM ventas v
            INNER JOIN clientes c ON UPPER(v.cliente) = UPPER(c.cliente)
            WHERE DATE(v.fecha) = ? AND v.tipo = 'Museo'
            ORDER BY c.tipo, v.fecha";
    
    $sentencia = $base_de_datos->prepare($sql);
    $sentencia->execute([$fecha]);
    $datos = $sentencia->fetchAll(PDO::FETCH_ASSOC);

    $css = "
        <style>
            body { font-family: Arial, sans-serif; }
            table {
                width: 100%;
                max-width: 100%;
                margin-bottom: 1rem;
                background-color: transparent;
                border-collapse: collapse;
            }
            th, td {
                padding: 0.75rem;
                vertical-align: top;
                border-top: 1px solid #dee2e6;
                font-size:12px;
            }
            thead th {
                vertical-align: bottom;
                border-bottom: 2px solid #dee2e6;
            }
            .table-striped tbody tr:nth-of-type(odd) {
                background-color: rgba(0, 0, 0, 0.05);
            }
            .table-bordered {
                border: 1px solid #dee2e6;
            }
            .table-bordered th,
            .table-bordered td {
                border: 1px solid #dee2e6;
            }
            .col-num { width: 5%; }
            .col-hora { width: 15%; }
            .col-guia { width: 50%; }
            .col-producto { width: 30%; }
        </style>
    ";

    $mpdf = new Mpdf();
    $mpdf->SetFont('Arial');

    $tipoActual = '';
    $datosAgrupados = [];

    foreach ($datos as $fila) {
        $fechaVenta = new DateTime($fila['fecha']);
        $diaSemana = $fechaVenta->format('N');
        $hora = $fechaVenta->format('H');
        
        if ($diaSemana >= 6) {
            $grupo = "Fin de semana";
        } elseif ($hora < 14) {
            $grupo = "Matutino";
        } else {
            $grupo = "Vespertino";
        }

        if (!isset($datosAgrupados[$fila['tipo_cliente']])) {
            $datosAgrupados[$fila['tipo_cliente']] = [];
        }
        if (!isset($datosAgrupados[$fila['tipo_cliente']][$grupo])) {
            $datosAgrupados[$fila['tipo_cliente']][$grupo] = [];
        }
        $datosAgrupados[$fila['tipo_cliente']][$grupo][] = $fila;
    }

    foreach ($datosAgrupados as $tipo => $grupos) {
        
        $mpdf->AddPage();
        // Obtener la ruta relativa del logo
        $rutaLogo = dirname(dirname(__DIR__)). '/img/logo_color.png';
        
        // Verificar si el archivo existe
        $html = $css;
        $html .= "<table width='100%'><tr><td width='20%'>";
        if (file_exists($rutaLogo)) {
            $html .= "<img src='" . $rutaLogo . "' style='width: 100px;'>";
        } else {
            $html .= "<p style='color: red;'>Error: Logo no encontrado</p>";
        }
        $html .= "</td><td width='80%' style='vertical-align: top;'>";
        $html .= "<h2 style='text-align: left; margin-top: 0;'>Reporte diario de Consumo de Alimentos<br><br>" . ucfirst($tipo) . "</h2>";
        $html .= "<h3 style='text-align: left;'>Fecha: " . date('d/m/Y', strtotime($fecha)) . "</h3>";
        $html .= "</td></tr></table>";

        foreach (['Matutino', 'Vespertino', 'Fin de semana'] as $grupo) {
            if (isset($grupos[$grupo])) {
                $html .= "<h3>" . $grupo . "</h3>";
                $html .= "<table class='table table-striped table-bordered'>
                            <thead>
                                <tr>
                                    <th class='col-num'>#</th>
                                    <th class='col-hora'>Hora</th>
                                    <th class='col-guia'>Nombre</th>
                                    <th class='col-producto'>Producto</th>
                                </tr>
                            </thead>
                            <tbody>";
                $contador = 1;
                foreach ($grupos[$grupo] as $fila) {
                    $html .= "<tr>
                                <td class='col-num'>{$contador}</td>
                                <td class='col-hora'>" . date('H:i', strtotime($fila['fecha'])) . "</td>
                                <td class='col-guia'>" . strtoupper($fila['cliente']) . "</td>
                                <td class='col-producto'>{$fila['nombre_producto']}</td>
                              </tr>";
                    $contador++;
                }
                $html .= "</tbody></table>";
            }
        }

        $mpdf->WriteHTML($html);
        
    }
    
    ob_end_clean();
    
    $pdfContent = $mpdf->Output('', 'S');
    
    header('Content-Type: application/pdf');
    header('Content-Length: ' . strlen($pdfContent));
    header('Content-Disposition: inline; filename="reporte_guias_' . $fecha . '.pdf"');
    
    echo $pdfContent;
    exit;
} catch (\Exception $e) {
    echo 'Error al generar el PDF: ' . $e->getMessage();
}
