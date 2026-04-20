<?php require_once('../../../../premaster.php'); if (Usuario::logueado()->puede('comercial/pedidos/nota_de_pedido/facturar/')) { ?>
<?php

$empresa = Funciones::session('empresa'); // Se usa a partir del despacho

$idCliente = Funciones::post('idCliente');
$idSucursal = Funciones::post('idSucursal');
$idAlmacen = Funciones::post('idAlmacen');
$observaciones = Funciones::post('observaciones');
//$formaDePago = Funciones::post('formaDePago');
$idTemporada = Funciones::post('idTemporada');
$descuento = Funciones::post('descuento');
$recargo = Funciones::post('recargo');
$idVendedor = Funciones::post('idVendedor');
$detalleNotaDePedido = Funciones::post('notaDePedido');

/*
 * Para facturar un pedido de forma directa, se requieren los siguientes pasos:
 * 1) Se crea una nota de pedido (Pedido) ya marcada como " aprobado = 'S' "
 * 2) Se genera el predespacho ya asignado (->generarPredespacho(true))
 * 3) Se actualiza el estado del PedidoCliente (->actualizarEstadoPedidoCliente())
 * 4) Hacer el Despacho
 * 5) Mandar a facturar el Despacho
 * 6) Retornar los datos de la factura, para poder abrirla desde el front
 * 7) Abrir una pestaña con la factura
 */

try {
    Factory::getInstance()->beginTransaction();

    // Paso 1
	try {
        $notaDePedido = Factory::getInstance()->getPedido();
        $notaDePedido->empresa = 1;
        $notaDePedido->cliente = Factory::getInstance()->getClienteTodos($idCliente);
        $notaDePedido->sucursal = Factory::getInstance()->getSucursal($idCliente, $idSucursal);
        if (!Usuario::logueado()->esCliente() && !Usuario::logueado()->esVendedor()) {
            $notaDePedido->idAlmacen = (isset($idAlmacen) ? $idAlmacen : '01');
            //$notaDePedido->formaDePago = Factory::getInstance()->getFormaDePago($formaDePago);
            $notaDePedido->descuento = Funciones::toFloat($descuento);
            $notaDePedido->recargo = Funciones::toFloat($recargo);
        } else {
            //Hardcodeo para los vendedores y clientes
            $notaDePedido->idAlmacen = '01';
        }
        if (Usuario::logueado()->esCliente()) {
            $idVendedor = Usuario::logueado()->contacto->cliente->vendedor->id;
        } elseif (Usuario::logueado()->esVendedor()) {
            $idVendedor = Usuario::logueado()->getCodigoPersonal();
        }
        $notaDePedido->temporada = Factory::getInstance()->getTemporada($idTemporada);
        $notaDePedido->observaciones = $observaciones;
        $notaDePedido->vendedor = Factory::getInstance()->getVendedor($idVendedor);
        $notaDePedido->usuario = Usuario::logueado();
        $notaDePedido->precioAlFacturar = 'S';
        $notaDePedido->aprobado = 'S';

        $nroItem = 1;
        foreach ($detalleNotaDePedido as $idCombinado => $curvas) {
            $idCombinado = explode('_', $idCombinado);
            $idArticulo = $idCombinado[0];
            $idColorPorArticulo = $idCombinado[1];
            $colorPorArticulo = Factory::getInstance()->getColorPorArticulo($idArticulo, $idColorPorArticulo);
            $arrValores = array();
            foreach ($curvas as $key => $val) {
                if ($key == 'L') {
                    $cantidades = explode('-', $val);
                    $i = 1;
                    foreach ($cantidades as $cantidad) {
                        if (!isset($arrValores[$i]))
                            $arrValores[$i] = 0;
                        $arrValores[$i] += Funciones::toInt($cantidad);
                        $i++;
                    }
                    break;
                } else {
                    $curva = Factory::getInstance()->getCurva($key);
                    $i = 1;
                    foreach ($curva->cantidad as $cantidad) {
                        if (!isset($arrValores[$i]))
                            $arrValores[$i] = 0;
                        $arrValores[$i] += Funciones::toInt($cantidad) * Funciones::toInt($val);
                        $i++;
                    }
                }
            }
            $notaDePedidoItem = Factory::getInstance()->getPedidoItem();
            $notaDePedidoItem->empresa = $notaDePedido->empresa;
            $notaDePedidoItem->idAlmacen = $notaDePedido->idAlmacen;
            $notaDePedidoItem->idArticulo = $idArticulo;
            $notaDePedidoItem->idColorPorArticulo = $idColorPorArticulo;
            $notaDePedidoItem->numeroDeItem = $nroItem;
            $notaDePedidoItem->precioUnitario = $colorPorArticulo->getPrecioSegunCliente($notaDePedido->cliente);
            for ($i = 1; $i <= 10; $i++) {
                $notaDePedidoItem->cantidad[$i] = Funciones::toInt(Funciones::keyIsSet($arrValores, $i, 0));
                $notaDePedidoItem->pendiente[$i] = $notaDePedidoItem->cantidad[$i];
            }
            $notaDePedido->addItem($notaDePedidoItem);
            $nroItem++;
        }
        $notaDePedido->calcularTotal();
        $notaDePedido->guardar()->notificar('comercial/pedidos/nota_de_pedido/agregar/');
    } catch (Exception $ex) {
		$msg = 'Error al crear pedido';
        Logger::addError($msg . ' para facturación: ' . $ex->getMessage());
		throw new Exception($msg);
    }

    // Paso 2
    try {
        $notaDePedido->generarPredespacho();
    } catch (Exception $ex) {
        $msg = 'Error en la generación del predespacho';
        Logger::addError($msg . ' para facturación: ' . $ex->getMessage());
        throw new Exception($msg);
    }

    // Paso 3
    try {
        $notaDePedido->predespachar();
    } catch (Exception $ex) {
        $msg = 'Error al predespachar';
        Logger::addError($msg . ' para facturación: ' . $ex->getMessage());
        throw new Exception($msg);
    }

	// Paso 4
    try {
        $notaDePedido->actualizarEstadoPedidoCliente();
    } catch (Exception $ex) {
        $msg = 'Error al actualizar el estado del pedido-cliente';
        Logger::addError($msg . ' para facturación: ' . $ex->getMessage());
        throw new Exception($msg);
    }

	// Paso 5

    try {
    	$predespachos = array();
        foreach ($notaDePedido->detalle as $item) {
            $aux = array(
                'pedidoNumero' => $notaDePedido->numero,
                'pedidoNumeroDeItem' => $item->numeroDeItem,
                'cant' => array()
            );
            for ($i = 1; $i <= 10; $i++) {
                $aux['cant'][$i] = $item->cantidad[$i];
            }
            $predespachos[] = $aux;
        }
		$datos = array(
			'empresa' => $empresa,
			'idCliente' => $notaDePedido->cliente->id,
			'idSucursal' => $notaDePedido->sucursal->id,
			'observaciones' => $notaDePedido->observaciones,
			'predespachos' => $predespachos
		);
		$despacho = Despacho::despachar($datos, 'comercial/despachos/generacion/agregar/');
    } catch (Exception $ex){
        $msg = 'Error al generar el despacho';
        Logger::addError($msg . ' para facturación: ' . $ex->getMessage());
        throw new Exception($msg);
    }

	// Paso 6
	try {
    	$factura = $despacho->facturar();
	} catch (Exception $ex){
		$msg = 'Error al generar el remito o la factura';
		Logger::addError($msg . ' para facturación: ' . $ex->getMessage());
		throw new Exception($msg . ': ' . $ex->getMessage());
	}

    Factory::getInstance()->commitTransaction();

	// Paso 7
	Html::jsonSuccess('El pedido fue facturado correctamente', array(
		'puntoDeVenta' => $factura->puntoDeVenta,
		'letra' => $factura->letra,
		'numero' => $factura->numero
	));
} catch (Exception $ex){
    Factory::getInstance()->rollbackTransaction();
	Html::jsonError('Ocurrió un error al intentar facturar el pedido. ' . $ex->getMessage());
}

?>
<?php } ?>