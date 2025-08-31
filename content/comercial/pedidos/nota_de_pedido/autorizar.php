<?php require_once('../../../../premaster.php'); ?>
<?php

function mailAutorizacion($ndp, $autoriza, $motivo = false){
	$autoriza = ($autoriza == 'S' ? true : false);

	$bold = 'font-weight: bold; ';
	$orangeBg = 'background-color: #E67B19; ';
	$colorWhite = 'color: white; ';
	$fontArial = 'font-family: Arial, sans-serif; ';
	$alignCenter = 'text-align: center; ';
	$alignLeft = 'text-align: left; ';

	$estiloDiv1 = 'width: 500px; ';
	$estiloDiv1 .= 'margin: 0 auto; ';
	$estiloDiv1 .= 'border: solid 1px black; ';
	$estiloDiv1 .= '-moz-border-radius: 5px; ';
	$estiloDiv1 .= '-webkit-border-radius: 5px; ';
	$estiloDiv1 .= 'border-radius: 5px; ';
	$estiloDiv1 .= $alignCenter;
	$estiloDiv1 .= $fontArial;

	$estiloDiv2 = 'font-size: 20px; ' . $colorWhite . $orangeBg;

	$estiloDiv3 = 'font-size: 17px; ';
	$estiloDiv3 .= 'line-height: 1.5em; ';

	$estiloDivImg = 'padding-left: 10px; ' . $alignLeft;
	$estiloDivImg2 = 'float: right; ';

	$estiloTable = 'width: 100%; ';
	$estiloTh = $alignCenter . $colorWhite . $orangeBg . $bold . $fontArial;
	$estiloTr0 = 'background-color: #EEEFFF; ' . $alignCenter . $fontArial;
	$estiloTr1 = 'background-color: #DDDDDD; ' . $alignCenter . $fontArial;

	if ($autoriza) {
		$asunto = 'Spiral Shoes - Nota de pedido aprobada';
		$titulo = 'Nota de pedido  <span style="' . $bold . '">aprobada</span>';
		$razon = 'Nota de pedido: <span style="' . $bold . '">Nº ' . $ndp->numero . '</span><br>';
		$razon .= 'Cliente: <span style="' . $bold . '">"' . $ndp->cliente->id . ' - ' . $ndp->cliente->razonSocial . '"</span><br>';
		$razon .= 'Fecha: <span style="' . $bold . '">' . $ndp->fechaAlta . '</span><br>';
		$razon .= 'Cantidad de pares: <span style="' . $bold . '">' . $ndp->cantidadDePares . '</span><br>';
		$razon .= 'Importe sin IVA: <span style="' . $bold . '">$ ' . Funciones::formatearDecimales($ndp->importeTotal, 2) . '</span><br>';
		$detalle = '	<div><br>Detalle</div>';
		$detalle .= '	<div>';
		$detalle .= '		<table style="' . $estiloTable . '">';
		$detalle .= '			<tr>';
		$detalle .= '				<td style="width: 50%; ' . $estiloTh . '">Articulo</td>';
		$detalle .= '				<td style="width: 15%; ' . $estiloTh . '">Color</td>';
		$detalle .= '				<td style="width: 15%; ' . $estiloTh . '">Pares</td>';
		$detalle .= '				<td style="width: 20%; ' . $estiloTh . '">Total</td>';
		$detalle .= '			</tr>';
		$i = 0;
		foreach($ndp->detalle as $item) {
			$detalle .= '			<tr style="' . (($i % 2 == 0) ?$estiloTr0 : $estiloTr1) . '">';
			$detalle .= '				<td>' . $item->articulo->id . ' - ' . $item->articulo->nombre . '</td>';
			$detalle .= '				<td>' . $item->colorPorArticulo->id . '</td>';
			$detalle .= '				<td>' . Funciones::sumaArray($item->cantidad) . '</td>';
			$detalle .= '				<td>$ ' . Funciones::formatearDecimales(Funciones::sumaArray($item->cantidad) * $item->precioUnitario, 2) . '</td>';
			$detalle .= '			</tr>';
			$i++;
		}
		$detalle .= '		</table>';
		$detalle .= '	</div><br>';
	} else {
		$asunto = 'Spiral Shoes - Nota de pedido no aprobada';
		$titulo = 'Nota de pedido <span style="' . $bold . '">no aprobada</span>';
		$razon = Usuario::logueado()->nombreApellido . ' desaprobó la <span style="' . $bold . '">nota de pedido Nº ' . $ndp->numero . '</span> de ' . $ndp->cliente->razonSocial . '.<br>';
		$razon .= ($motivo ? '<span style="' . $bold . '">Motivo</span>: ' . $motivo : '');
		$detalle = '';
	}
	$cuerpo = '<div style="' . $estiloDiv1 . '">';
	$cuerpo .= '	<div style="' . $estiloDiv2 . '">';
	$cuerpo .= '		' . $titulo;
	$cuerpo .= '	</div>';
	$cuerpo .= '	<div style="' . $estiloDiv3 . '">';
	$cuerpo .= '		' . $razon;
	$cuerpo .= '	</div>';
	$cuerpo .= $detalle;
	$cuerpo .= '	<div style="' . $estiloDivImg . '">';
	$cuerpo .= '		<img src="cid:1001" width="55" height="50" />';
	$cuerpo .= '		<div style="' . $estiloDivImg2 . '">';
	$cuerpo .= '			<img src="cid:1002" width="100" height="50" />';
	$cuerpo .= '		</div>';
	$cuerpo .= '	</div>';
	$cuerpo .= '</div>';

	$mailComercial = 'gc@spiralshoes.com';
	$mailVentas = 'ventas@spiralshoes.com';
	$mailVendedor = $ndp->vendedor->email;
	$mailCliente = $ndp->cliente->email;
	$para = array($mailComercial, $mailVentas, $mailVendedor);
	($autoriza) && $para[] = $mailCliente;
	$images = array(Config::pathBase . '/img/varias/spiral_logo.jpg', Config::pathBase . '/img/varias/koi_bg.gif');
	Email::enviar(
		 array(
			 'para' => $para,
			 'asunto' => $asunto,
			 'contenido' => $cuerpo,
			 'imagenes' => $images
		 )
	);
}



$idNotaDePedido = Funciones::post('idNotaDePedido');
$autoriza = Funciones::post('autoriza');
$autoriza = ($autoriza == 'S' ? 'S' : 'N');
$numeroDeAutorizacion = Funciones::post('numeroDeAutorizacion');
$motivo = Funciones::post('motivo');
$motivo = ($motivo == 'true' ? '' : $motivo);
$confirmarUltima = (Funciones::get('confirmarUltima') == '1');
try {
	if (!isset($idNotaDePedido) || !isset($numeroDeAutorizacion))
		throw new Exception();
	try {
		$notaDePedido = Factory::getInstance()->getPedido($idNotaDePedido);
	} catch (Exception $ex) {
		throw new FactoryException('La nota de pedido que intenta autorizar no existe');
	}
	if ($notaDePedido->cliente->autorizado == 'N')
		throw new FactoryException('El cliente no fue autorizado aún');
	if ($notaDePedido->cliente->anulado == 'S')
		throw new FactoryException('El cliente está anulado y no puede operar');
	try {
		$autorizacionPersona = Factory::getInstance()->getAutorizacionPersona(TiposAutorizacion::notaDePedido, $numeroDeAutorizacion, Usuario::logueado()->id);
	} catch (Exception $ex) {
		throw new FactoryException('No tiene permisos para realizar esa autorización');
	}
	try {
		$autorizacion = Factory::getInstance()->getAutorizacion(TiposAutorizacion::notaDePedido, $numeroDeAutorizacion, $notaDePedido->numero);
		if ($autorizacion->autorizado == 'S' || $autoriza == 'N')
			throw new FactoryException('Esa autorización ya fue realizada. Recargue la página');
		else {
			Factory::getInstance()->marcarParaBorrar($autorizacion);
			Factory::getInstance()->persistir($autorizacion);
		}
	} catch (FactoryExceptionRegistroNoExistente $ex) {
	}
	if ((Funciones::toFloat($notaDePedido->descuento) > 0 || Funciones::toFloat($notaDePedido->recargo) > 0) && $autoriza == 'S' && !(Usuario::logueado()->tieneRol('gerencia comercial') || Usuario::logueado()->tieneRol('capomafia')))
		throw new FactoryException('Sólo el gerente comercial puede autorizar un pedido con descuento o recargo');
	$autorizacionTipo = Factory::getInstance()->getAutorizacionTipo(TiposAutorizacion::notaDePedido);
	
	if ($autoriza == 'S') {
		$ningunRechazo = true;
		foreach ($notaDePedido->autorizaciones->autorizaciones as $aut)
			if ($aut->autorizado == 'N'){
				$ningunRechazo = false;
				break;
		}
	}

	$ultimaAutorizacion = (count($notaDePedido->autorizaciones->autorizaciones) == ($autorizacionTipo->cantidad - 1));
	$hayQueConfirmar = ($autoriza) && ($ningunRechazo) && ($ultimaAutorizacion) && (!$confirmarUltima);

	if ($hayQueConfirmar) {
		Html::jsonConfirm('Con su autorización la nota quedará aprobada y pasará a ser pedido. ¿Desea continuar?', 'confirmarUltima');
	} else {
		$nuevaAutorizacion = Factory::getInstance()->getAutorizacion();
		$nuevaAutorizacion->autorizado = $autoriza;
		$nuevaAutorizacion->autorizacionTipo = $autorizacionTipo;
		$nuevaAutorizacion->idEspecifico = $notaDePedido->numero;
		$nuevaAutorizacion->usuario = Usuario::logueado();
		$nuevaAutorizacion->motivo = $motivo;
		$nuevaAutorizacion->numero = $numeroDeAutorizacion;
		Factory::getInstance()->persistir($nuevaAutorizacion);

		if ($autoriza == 'N') {
			//Mando mail a Comercial (Javier)
			mailAutorizacion($notaDePedido, $autoriza, $motivo);
		} elseif (($ningunRechazo) && ($ultimaAutorizacion) && ($autoriza == 'S')) {
			//Actualizo el pedido y lo pongo como aprobado
			$notaDePedido->aprobado = 'S';
			$arrDetalle = array();
			foreach($notaDePedido->detalle as $item) {
				$arrDetalle[] = Factory::getInstance()->marcarParaInsertar($item);
			}
			$notaDePedido->detalle = $arrDetalle;
			try {
			    Factory::getInstance()->beginTransaction();
				$notaDePedido->guardar()->notificar('comercial/pedidos/nota_de_pedido/autorizar/');
				//Si la nota de pedido si guardó como aprobada correctamente, creo un registro de predespacho por cada artículo (sólo se pueden autorizar 1 vez, entonces no habría duplicados)
				$notaDePedido->generarPredespacho();
				$notaDePedido->actualizarEstadoPedidoCliente();
				Factory::getInstance()->commitTransaction();
				try {
					//Mando mail al cliente
					mailAutorizacion($notaDePedido, $autoriza);
				} catch (Exception $ex) {
					//Si falla el envío de mail no hago nada, total... ¿qué importa?
				}
			} catch (Exception $ex) {
			    Factory::getInstance()->rollbackTransaction();
				//Si falla al modificar el pedido con el dato de aprobada, entonces tengo que volver atrás: borro la autorización.
				$nuevaAutorizacion = Factory::getInstance()->getAutorizacion(TiposAutorizacion::notaDePedido, $numeroDeAutorizacion, $notaDePedido->numero);
				Factory::getInstance()->marcarParaBorrar($nuevaAutorizacion);
				Factory::getInstance()->persistir($nuevaAutorizacion);
				if (Funciones::getType($ex) == 'FactoryException')
					throw new FactoryException($ex->getMessage());
				throw new Exception($ex->getMessage());
			}
		}
		Html::jsonSuccess('La autorización se realizó correctamente');
	}
} catch (FactoryException $ex){
	Html::jsonError($ex->getMessage());
} catch (Exception $ex){
	Html::jsonError('Ocurrió un error al intentar autorizar la nota de pedido: ' . $ex->getMessage());
}

?>