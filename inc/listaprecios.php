<?php
require_once dirname(__DIR__) . '/vendor/autoload.php';
require_once dirname(__DIR__) . '/modulos/db.php';

use Mpdf\Mpdf;

try {
    ob_start();
    
    global $base_de_datos;
    $sql = "SELECT rowid, codigo, nombre, precio_venta, familia 
            FROM inventario ORDER BY familia, nombre";
    
    $sentencia = $base_de_datos->prepare($sql);
    $sentencia->execute();
    $datos = $sentencia->fetchAll(PDO::FETCH_ASSOC);

    $css = "
        <style>
            body { font-family: Arial, sans-serif; }
            table {
                width: 100%;
                margin-bottom: 0.7rem;
                border-collapse: collapse;
            }
            th, td {
                padding: 0.5rem;
                border-top: 1px solid #dee2e6;
                font-size: 10px;
            }
            thead th {
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
            .familia-header {
                background-color: #f8f9fa;
                font-weight: bold;
                font-size: 12px;
            }
            .page-footer {
                text-align: center;
                font-size: 8px;
            }
            .col-codigo { width: 15%; }
            .col-nombre { width: 40%; }
            .col-precio { width: 20%; }
            .col-barcode { width: 35%; }
        </style>
    ";

    $mpdf = new Mpdf([
        'margin_left' => 5,
        'margin_right' => 5,
        'margin_top' => 10,
        'margin_bottom' => 10,
        'margin_header' => 3,
        'margin_footer' => 3
    ]);
    $mpdf->SetFont('Arial', '', 10);

    $rutaLogo = dirname(__DIR__). '/img/logo_color.png';
    
    $html = $css;
    $html .= "<table width='100%'><tr><td width='20%'>";
    if (file_exists($rutaLogo)) {
        $html .= "<img src='" . $rutaLogo . "' style='width: 70px;'>";
    } else {
        $html .= "<p style='color: red;'>Error: Logo no encontrado</p>";
    }
    $html .= "</td><td width='80%' style='vertical-align: top;'>";
    $html .= "<h2 style='text-align: left; margin-top: 0; font-size: 16px;'>Lista de Precios</h2>";
    $html .= "</td></tr></table>";

    $productosPorFamilia = [];
    foreach ($datos as $producto) {
        $productosPorFamilia[$producto['familia']][] = $producto;
    }

    foreach ($productosPorFamilia as $familia => $productos) {
        $html .= "<div style='page-break-inside: avoid;'>";
        $html .= "<table class='table table-striped table-bordered'>";
        $html .= "<tr class='familia-header'><td colspan='4'>" . ucfirst($familia) . "</td></tr>";
        $html .= "<tr>
                    <th class='col-codigo'>Código</th>
                    <th class='col-nombre'>Nombre</th>
                    <th class='col-precio'>Precio</th>
                    <th class='col-barcode'>Código de Barras</th>
                  </tr>";
        
        foreach ($productos as $producto) {
            $html .= "<tr>
                        <td class='col-codigo'>{$producto['codigo']}</td>
                        <td class='col-nombre'>{$producto['nombre']}</td>
                        <td class='col-precio'>$" . number_format($producto['precio_venta'], 2) . "</td>
                        <td class='col-barcode'><barcode code='{$producto['codigo']}' type='C128A' size='0.8' height='0.9' /></td>
                      </tr>";
        }
        $html .= "</table>";
        $html .= "</div>";
    }

    $mpdf->SetHTMLHeader('<div style="text-align: right; font-weight: bold; font-size: 10px;">Lista de Precios Cafetería</div>');
    $mpdf->SetHTMLFooter('<div class="page-footer">Página {PAGENO} de {nbpg}</div>');
    $mpdf->WriteHTML($html);
    
    ob_end_clean();
    $pdfContent = $mpdf->Output('', 'S');
    
    header('Content-Type: application/pdf');
    header('Content-Length: ' . strlen($pdfContent));
    header('Content-Disposition: inline; filename="lista_precios.pdf"');
    
    echo $pdfContent;
    exit;
} catch (\Exception $e) {
    echo 'Error al generar el PDF: ' . $e->getMessage();
}
