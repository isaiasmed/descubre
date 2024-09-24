<?php
date_default_timezone_set("America/Mexico_City"); 
    
require __DIR__ . '/ticket/autoload.php';
use Mike42\Escpos\Printer;
use Mike42\Escpos\EscposImage;
use Mike42\Escpos\PrintConnectors\WindowsPrintConnector;

function abre_cajon()
{
    /*Conectamos con la impresora*/
    $nombre_impresora = trim(file(__DIR__ . '/impresora.ini')[0]);
    $connector = new WindowsPrintConnector($nombre_impresora);
    $printer = new Printer($connector);
    /*No imprimimos nada, solamente abrimos cajón*/
    $printer->cut();
    $printer->pulse();
    $printer->close();
}
function imprime_codigo_cliente($cliente)
{
    // Incluir las funciones de base de datos
    if (!defined("RAIZ")) {
        define("RAIZ", dirname(dirname(__FILE__)));
    }
    require_once RAIZ . "/modulos/db.php";
    global $base_de_datos;
    $sentencia = $base_de_datos->prepare("SELECT cliente, tipo FROM clientes WHERE id = ?");
    $sentencia->execute([$cliente]);
    $datos_cliente = $sentencia->fetch();
    
    if (!$datos_cliente) {
        return;
    }
    
    $nombre_cliente = $datos_cliente['cliente'];
    $tipo_cliente = $datos_cliente['tipo'];
    $id_cliente = $cliente;
    $nombre_impresora = trim(file(__DIR__ . '/impresora.ini')[0]);
    $connector = new WindowsPrintConnector($nombre_impresora);
    $printer = new Printer($connector);
    $printer->setJustification(Printer::JUSTIFY_CENTER);
    $ruta_imagen_logo = dirname(__DIR__) . "/img/logo.png";
    $printer->feed();
    $logo = EscposImage::load($ruta_imagen_logo, false);
    $printer->bitImage($logo);
    $printer->feed();
    $printer->setEmphasis(true);
    $printer->setTextSize(2, 2);
    $printer->text("MUSEO DESCUBRE"."\n");
    $printer->setEmphasis(false);
    $printer->setTextSize(1, 1);
    $printer->text("SISTEMA DE CONTROL DE ALIMENTOS"."\n");
    $printer->feed();
    $printer->setEmphasis(false);
    $codigo_texto2 = '{B...'.$tipo_cliente . "^" . $id_cliente;
    $printer->setBarcodeHeight(150);
    $printer->setBarcodeWidth(3);
    $printer->barcode($codigo_texto2, Printer::BARCODE_CODE128);
    $printer->setEmphasis(true);
    $printer->text("\n".mb_strtoupper($tipo_cliente.' '.$nombre_cliente) . "\n");
    $printer->cut();
    $printer->close();
    
    // Agregar mensaje de depuración
    echo 'si imprimio';
}

function imprime_ticket($productos, $id_venta, $cambio, $client, $tipo, $ingredientes )
{

	
    if (!defined("RAIZ")) define("RAIZ", dirname(__FILE__));

    $cc=0;
    foreach ($productos as $producto) {
       if($producto->nombre!="ALIMENTOS GUIAS" && $producto->nombre!="ALIMENTOS PERSONAL ASEO"){
		$cc++;
       }
    }
    $cc=1;
     /*Conectamos con la impresora*/
    $nombre_impresora = trim(file(__DIR__ . '/impresora.ini')[0]);
    $connector = new WindowsPrintConnector($nombre_impresora);
    $printer = new Printer($connector);

    /*Cargamos el logo*/
    $ruta_imagen_logo = dirname(__DIR__) . "/img/logo.png";
    $logo = EscposImage::load($ruta_imagen_logo, false);

    /*Revisamos si solo es guia o aseo*/	
    if($cc>0){
        $datos_empresa_recuperados = file(RAIZ . "/modulos/datos_empresa.txt");
        $datos_empresa = array(
            "nombre" => trim($datos_empresa_recuperados[0]),
            "telefono" => trim($datos_empresa_recuperados[1]),
            "rfc" => trim($datos_empresa_recuperados[2]),
            "direccion" => trim($datos_empresa_recuperados[3]),
            "colonia" => trim($datos_empresa_recuperados[4]),
            "cp" => trim($datos_empresa_recuperados[5]),
            "ciudad" => trim($datos_empresa_recuperados[6])
        );

        $num_copias = ($tipo == "Crédito") ? 2 : 1;

        for ($i = 0; $i < $num_copias; $i++) {
            /*Le decimos que centre lo que vaya a imprimir*/
            $printer->setJustification(Printer::JUSTIFY_CENTER);
            /*Imprimimos imagen y avanzamos el papel*/
            $printer->setTextSize(1, 1);
            $printer->bitImage($logo);
            $printer->feed();

            /*Imprimimos los datos de la empresa*/
            $printer->setTextSize(2, 2);
            $printer->text("CAFETERIA"."\n");
            $printer->text("MUSEO DESCUBRE"."\n");
            $printer->feed(1);
            $printer->setEmphasis(false);
            $printer->setTextSize(1, 1);
            $fecha=date('d/m/Y H:i');
            $printer->setEmphasis(false);
            $printer->text($fecha."\n");
            foreach ($datos_empresa as $dato) {
                //$printer->text($dato . "\n");
            }
            
            /*Hacemos que el texto sea en negritas e imprimimos el nùmero de venta*/
            $printer->setEmphasis(true);
            $printer->setTextSize(2, 2);
            $printer->text("Venta #" . $id_venta);
            $printer->setEmphasis(false);
            $printer->feed(3);
            $printer->setTextSize(1, 1);
            if($tipo=='TPV'){
                $printer->setEmphasis(true);
                $printer->text("=== VENTA REALIZADA CON TERMINAL BANCARIA === \n");
                $printer->feed();
                $printer->feed();
                $printer->setEmphasis(false);
            }
            
            $printer->setJustification(Printer::JUSTIFY_LEFT);
            $total = 0;
            foreach ($productos as $producto) {
                $importe = $producto->cantidad * $producto->precio_venta;
                $total += $importe;
                $importe_formateado = number_format($importe, 2, ".", ",");
                $printer->setJustification(Printer::JUSTIFY_LEFT);
                $printer->text($producto->cantidad . "x " . $producto->nombre . "\n");
                $printer->setJustification(Printer::JUSTIFY_RIGHT);
                $printer->text(' $' . $importe_formateado . "\n");
            }
           
            $ayudante_total = number_format($total, 2, ".", ",");
            $ayudante_cambio = number_format($cambio, 2, ".", ",");
            $ayudante_pago = number_format($total + $cambio, 2, ".", ",");

            $printer->selectPrintMode(Printer::MODE_EMPHASIZED | Printer::MODE_FONT_B);
            $printer->text("------------------------------------\n \n ");
             
            if($tipo!='Museo'){
            	$printer->text("SU PAGO $" . $ayudante_pago . "\n");
            	$printer->text("TOTAL");
            	$printer->setTextSize(3, 2);
            	$printer->text(" $" . $ayudante_total . "\n");
	        }
            
            if($tipo=="Crédito" || $tipo=="Museo"){
                $printer->text("\n");                
                $printer->setTextSize(1, 1);
                $printer->setJustification(Printer::JUSTIFY_CENTER);
                $printer->text("***************************************************");
                $printer->setTextSize(2, 1);
                $printer->text("\n");
                if($tipo=="Museo"){
                    $printer->setJustification(Printer::JUSTIFY_CENTER);
                    $printer->text("RECIBI ALIMENTOS:");
                }else{
                    $printer->setJustification(Printer::JUSTIFY_CENTER);
                    $printer->text("CONSUMO A CREDITO");
                }
                $printer->text("\n");
                $printer->setTextSize(1, 1);
                $printer->setJustification(Printer::JUSTIFY_CENTER);
                $printer->text("***************************************************");
                if($i>0){
                    $printer->text("\n");
                    $printer->setTextSize(2, 1);
                    $printer->text("=== COPIA CLIENTE ===");
                    $printer->text("\n");
                }
                if($i==0){
                    $printer->text("\n");
                    $printer->text("\n");
                    $printer->text("\n");
                    $printer->text("\n");
                    $printer->text("\n");
                    $printer->feed();
                    $printer->feed();
                    $printer->setTextSize(2, 1);
                    $printer->text("______________________________");         
                    $printer->text("\n");
                    $printer->setTextSize(1, 1);
                    $printer->text("FIRMA");
                    $printer->text("\n");
                    $printer->setTextSize(2, 2);
                    $printer->text($client);
                    $printer->text("\n");
                    $printer->text("\n");
                }
            }
            $printer->feed();

	        if($tipo!='Museo'){
            	$printer->setJustification(Printer::JUSTIFY_RIGHT);
            	$printer->setTextSize(2, 1);
            	$printer->text("\n");
                $printer->text("\n");
            	$printer->text("CAMBIO $" . $ayudante_cambio);
            	$printer->text("\n");
            	$printer->feed();
            }

            /*Calculamos la hora para desearle buenos días, tardes o noches*/
            $hora = date("G");
            $str_deseo = "a";
            if ($hora >= 6 and $hora <= 12) {
                $str_deseo = "le deseamos un buen dia";
            }
            if ($hora >= 12 and $hora <= 19) {
                $str_deseo = "le deseamos una buena tarde";
            }
            if ($hora >= 19 and $hora <= 24) {
                $str_deseo = "le deseamos una buena noche";
            }
            if ($hora >= 0 and $hora <= 6) {
                $str_deseo = "le deseamos un buen dia";
            }
            /*Le deseamos al cliente buenas tardes, noches o días*/

            $printer->selectPrintMode(Printer::MODE_FONT_A);
            $printer->setJustification(Printer::JUSTIFY_CENTER);
            $printer->text(strtoupper($str_deseo));
            $printer->feed();
            $printer->cut();
            $printer->feed();

            if ($i < $num_copias - 1) {
                $printer->feed(3); // Espacio entre copias
            }
        }
    }

    $contar=0;
    foreach ($productos as $producto) {
	if($producto->familia=='DESCUBRE'){
          $contar++;
        }
    }
    if($contar>0){
       $ingredientes=json_decode($ingredientes);
       $printer->setJustification(Printer::JUSTIFY_CENTER);
       $printer->text("C O M A N D A   C O C I N A");
       $printer->text("\n");
       $printer->text("\n");
       $printer->setEmphasis(true);
       $printer -> setTextSize(1, 1);
       $printer->text("ORDEN # ");
       $printer -> setTextSize(3, 3);
       $printer->text($id_venta);
       $printer->feed(2);
       $printer -> setTextSize(2, 1);
       $printer->text("\n");
       $printer->text("\n");
       $printer->text("***********************");
       $printer->feed();
       $printer -> setTextSize(2, 2);
       $printer->text($client);
       $printer->feed();
       $printer -> setTextSize(2, 1);
       $printer->text("***********************");
       $printer->setJustification(Printer::JUSTIFY_LEFT);
       $printer->text("\n");
       $printer->feed(2);
       foreach ($productos as $producto) {
	if($producto->familia=='DESCUBRE'){
          $printer -> setTextSize(2, 2);
          $printer->text($producto->cantidad . "x " . $producto->nombre . "\n");
        }
       }
       $printer->feed(2);
       $printer->text("\n");
       $printer->feed(2);
       $printer->feed(2);
       $printer->text("________________________");
       $printer->feed(2);
       $printer->cut();
    }
   
    /*Terminamos el trabajo de impresión y abrimos el cajón*/
    $printer->pulse();
    $printer->close();
}