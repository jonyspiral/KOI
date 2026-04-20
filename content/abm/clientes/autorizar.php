<?php require_once('../../../premaster.php'); if (Usuario::logueado()->puede('abm/clientes/')) { ?>
<?php

function mailAutorizacion($cli, $autoriza, $motivo = false){
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

	if ($autoriza) {
		$asunto = 'Spiral Shoes - Cliente aprobado';
		$titulo = 'Cliente <span style="' . $bold . '">aprobado</span>';
		$razon = 'Cliente: <span style="' . $bold . '">Nº ' . $cli->id . '</span><br>';
		$razon .= 'Razón social: <span style="' . $bold . '">"' . $cli->razonSocial . '"</span><br>';
		$razon .= 'Vendedor: <span style="' . $bold . '">' . $cli->vendedor->nombreApellido . '</span><br>';
	} else {
		$asunto = 'Spiral Shoes - Cliente no aprobado';
		$titulo = 'Cliente <span style="' . $bold . '">no aprobado</span>';
		$razon = Usuario::logueado()->nombreApellido . ' desaprobó al <span style="' . $bold . '">cliente ' . $cli->id . '</span> - ' . $cli->razonSocial . '.<br>';
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
	$mailJony = 'gg@spiralshoes.com';
	$mailMarketing = 'diegoperezmartinez@gmail.com';
	$mailVendedor = $cli->vendedor->email;
	$para = array($mailComercial, $mailJony, $mailMarketing);
	($autoriza) && $para[] = $mailVendedor;
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

$idCliente = Funciones::post('idCliente');
$autoriza = Funciones::post('autoriza');
$autoriza = ($autoriza == 'S' ? 'S' : 'N');
$numeroDeAutorizacion = Funciones::post('numeroDeAutorizacion');
$motivo = Funciones::post('motivo');
$motivo = ($motivo == 'true' ? '' : $motivo);
$confirmarUltima = (Funciones::get('confirmarUltima') == '1');
try {
	if (!isset($idCliente) || !isset($numeroDeAutorizacion))
		throw new Exception();
	try {
		$cliente = Factory::getInstance()->getClienteTodos($idCliente);
	} catch (Exception $ex) {
		throw new FactoryException('El cliente que intenta autorizar no existe');
	}
	try {
		$autorizacionPersona = Factory::getInstance()->getAutorizacionPersona(TiposAutorizacion::altaCliente, $numeroDeAutorizacion, Usuario::logueado()->id);
	} catch (Exception $ex) {
		throw new FactoryException('No tiene permisos para realizar esa autorización');
	}
	try {
		$autorizacion = Factory::getInstance()->getAutorizacion(TiposAutorizacion::altaCliente, $numeroDeAutorizacion, $cliente->id);
		if ($autorizacion->autorizado == 'S' || $autoriza == 'N')
			throw new FactoryException('Esa autorización ya fue realizada. Recargue la página');
		else {
			Factory::getInstance()->marcarParaBorrar($autorizacion);
			Factory::getInstance()->persistir($autorizacion);
		}
	} catch (FactoryExceptionRegistroNoExistente $ex) {
	}
	$autorizacionTipo = Factory::getInstance()->getAutorizacionTipo(TiposAutorizacion::altaCliente);

	if ($autoriza == 'S') {
		$ningunRechazo = true;
		foreach ($cliente->autorizaciones->autorizaciones as $aut)
			if ($aut->autorizado == 'N'){
				$ningunRechazo = false;
				break;
		}
	}

	$ultimaAutorizacion = (count($cliente->autorizaciones->autorizaciones) == ($autorizacionTipo->cantidad - 1));
	$hayQueConfirmar = ($autoriza) && ($ningunRechazo) && ($ultimaAutorizacion) && (!$confirmarUltima);

	if ($hayQueConfirmar) {
		Html::jsonConfirm('Con su autorización la nota quedará aprobada y pasará a ser pedido. ¿Desea continuar?', 'confirmarUltima');
	} else {
		$nuevaAutorizacion = Factory::getInstance()->getAutorizacion();
		$nuevaAutorizacion->autorizado = $autoriza;
		$nuevaAutorizacion->autorizacionTipo = $autorizacionTipo;
		$nuevaAutorizacion->idEspecifico = $cliente->id;
		$nuevaAutorizacion->usuario = Usuario::logueado();
		$nuevaAutorizacion->motivo = $motivo;
		$nuevaAutorizacion->numero = $numeroDeAutorizacion;
		Factory::getInstance()->persistir($nuevaAutorizacion);

		if ($autoriza == 'N') {
			//Mando mail a Jony y a Comercial (Javier)
			mailAutorizacion($cliente, $autoriza, $motivo);
		} elseif (($ningunRechazo) && ($ultimaAutorizacion) && ($autoriza == 'S')) {
			//Habilito el cliente
			$cliente->anulado = 'N';
			$cliente->autorizado = 'S';
			try {
				$cliente->guardar()->notificar('abm/clientes/autorizar/');
			} catch (Exception $ex) {
				//Si falla al hacer update del cliente con AUTORIZADO = 'S'
				//entonces tengo que volver atrás: borro la autorización.
				$nuevaAutorizacion = Factory::getInstance()->getAutorizacion(TiposAutorizacion::altaCliente, $numeroDeAutorizacion, $cliente->id);
				Factory::getInstance()->marcarParaBorrar($nuevaAutorizacion);
				Factory::getInstance()->persistir($nuevaAutorizacion);
				throw $ex;
			}
			//Mando mail al vendedor, a Jony y a Javier
			mailAutorizacion($cliente, $autoriza);
		}
		Html::jsonSuccess('La autorización se realizó correctamente');
	}
} catch (FactoryException $ex){
	Html::jsonError($ex->getMessage());
} catch (Exception $ex){
	Html::jsonError('Ocurrió un error al intentar autorizar la nota de pedido');
}

?>
<?php } ?>