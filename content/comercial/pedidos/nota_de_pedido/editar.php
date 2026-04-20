<?php require_once('../../../../premaster.php'); if (Usuario::logueado()->puede('comercial/pedidos/nota_de_pedido/agregar/')) { ?>
<?php

$idNotaDePedido = Funciones::post('idNotaDePedido');
$idCliente = Funciones::post('idCliente');
$idSucursal = Funciones::post('idSucursal');
$observaciones = Funciones::post('observaciones');
$idTemporada = Funciones::post('idTemporada');
$formaDePago = Funciones::post('formaDePago');
$descuento = Funciones::post('descuento');
$recargo = Funciones::post('recargo');
$idVendedor = Funciones::post('idVendedor');
$detalleNotaDePedido = Funciones::post('notaDePedido');

try {
	$notaDePedido = Factory::getInstance()->getPedido($idNotaDePedido);
	if ($notaDePedido->esEcommerce()) {
		throw new FactoryException('No se pueden modificar pedidos de Ecommerce');
	}
	if ((Usuario::logueado()->esCliente() || Usuario::logueado()->esVendedor()) && count($notaDePedido->autorizaciones->autorizaciones) != 0)
		throw new FactoryException('No puede editar la nota de pedido ya que está siendo procesada');
	if (!Usuario::logueado()->esCliente() && !Usuario::logueado()->esVendedor()){
		//$notaDePedido->formaDePago = Factory::getInstance()->getFormaDePago($formaDePago);
		$notaDePedido->descuento = Funciones::toFloat($descuento);
		$notaDePedido->recargo = Funciones::toFloat($recargo);
	}
	if (Usuario::logueado()->esCliente())
		$idVendedor = Usuario::logueado()->contacto->cliente->vendedor->id;
	elseif (Usuario::logueado()->esVendedor())
		$idVendedor = Usuario::logueado()->getCodigoPersonal();
	$notaDePedido->temporada = Factory::getInstance()->getTemporada($idTemporada);
	$notaDePedido->observaciones = $observaciones;
	$notaDePedido->vendedor = Factory::getInstance()->getVendedor($idVendedor);

	$notaDePedido->detalle = array();
	$nroItem = 1;
	foreach ($detalleNotaDePedido as $idCombinado => $curvas){
		$idAlmacen = 1; //Esto hay que cambiarlo por algo que venga de JS, en el ID combinado.
		$idCombinado = explode('_', $idCombinado);
		$idArticulo = $idCombinado[0];
		$idColorPorArticulo = $idCombinado[1];
		$colorPorArticulo = Factory::getInstance()->getColorPorArticulo($idArticulo, $idColorPorArticulo);
		$arrValores = array();
		foreach ($curvas as $key => $val){
			if ($key == 'L'){
				$cantidades = explode('-', $val);
				$i = 1;
				foreach ($cantidades as $cantidad){
					if (!isset($arrValores[$i]))
						$arrValores[$i] = 0;
					$arrValores[$i] += Funciones::toInt($cantidad);
					$i++;
				}
				break;
			} else {
				$curva = Factory::getInstance()->getCurva($key);
				$i = 1;
				foreach ($curva->cantidad as $cantidad){
					if (!isset($arrValores[$i]))
						$arrValores[$i] = 0;
					$arrValores[$i] += Funciones::toInt($cantidad) * Funciones::toInt($val);
					$i++;
				}
			}
		}
		$notaDePedidoItem = Factory::getInstance()->getPedidoItem();
		$notaDePedidoItem->empresa = $notaDePedido->empresa;
		$notaDePedidoItem->idAlmacen = $idAlmacen;
		$notaDePedidoItem->idArticulo = $idArticulo;
		$notaDePedidoItem->idColorPorArticulo = $idColorPorArticulo;
		$notaDePedidoItem->numeroDeItem = $nroItem;
		$notaDePedidoItem->precioUnitario = ($notaDePedido->cliente->listaAplicable == 'D' ? Funciones::iIsSet($colorPorArticulo->precioDistribuidor, 0) : Funciones::iIsSet($colorPorArticulo->precioMayoristaDolar, 0));
		for ($i = 1; $i <= 10; $i++) {
			$notaDePedidoItem->cantidad[$i] = Funciones::toInt(Funciones::keyIsSet($arrValores, $i, 0));
		}
		$notaDePedido->addItem($notaDePedidoItem);
		$nroItem++;
	}
	$notaDePedido->calcularTotal();
	Factory::getInstance()->persistir($notaDePedido);
	Html::jsonSuccess('La nota de pedido fue guardada correctamente');
} catch (FactoryException $ex){
	Html::jsonError($ex->getMessage());
} catch (Exception $ex){
	Html::jsonError('Ocurrió un error al intentar guardar la nota de pedido');
}
?>
<?php } ?>