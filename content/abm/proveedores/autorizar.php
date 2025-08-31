<?php require_once('../../../premaster.php'); if (Usuario::logueado()->puede('abm/proveedores/')) { ?>
<?php

function mailAutorizacion($prov, $autoriza, $motivo = false){
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
		$asunto = 'Spiral Shoes - Proveedor aprobado';
		$titulo = 'Proveedor <span style="' . $bold . '">aprobado</span>';
		$razon = 'Proveedor: <span style="' . $bold . '">Nº ' . $prov->id . '</span><br>';
		$razon .= 'Razón social: <span style="' . $bold . '">"' . $prov->razonSocial . '"</span><br>';
	} else {
		$asunto = 'Spiral Shoes - Proveedor no aprobado';
		$titulo = 'Proveedor <span style="' . $bold . '">no aprobado</span>';
		$razon = Usuario::logueado()->nombreApellido . ' desaprobó al <span style="' . $bold . '">proveedor ' . $prov->id . '</span> - ' . $prov->razonSocial . '.<br>';
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

	$mailAdministracion = 'alejandro@spiralshoes.com';
	$mailCompras = 'compras@spiralshoes.com';
	$para = array($mailAdministracion, $mailCompras);
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

$idProveedor = Funciones::post('idProveedor');
$autoriza = Funciones::post('autoriza');
$autoriza = ($autoriza == 'S' ? 'S' : 'N');
$numeroDeAutorizacion = Funciones::post('numeroDeAutorizacion');
$motivo = Funciones::post('motivo');
$motivo = ($motivo == 'true' ? '' : $motivo);
$confirmarUltima = (Funciones::get('confirmarUltima') == '1');
try {
	if (!isset($idProveedor) || !isset($numeroDeAutorizacion))
		throw new Exception();
	try {
		$proveedor = Factory::getInstance()->getProveedorTodos($idProveedor);
	} catch (Exception $ex) {
		throw new FactoryException('El proveedor que intenta autorizar no existe');
	}
	try {
		$autorizacionPersona = Factory::getInstance()->getAutorizacionPersona(TiposAutorizacion::altaProveedor, $numeroDeAutorizacion, Usuario::logueado()->id);
	} catch (Exception $ex) {
		throw new FactoryException('No tiene permisos para realizar esa autorización');
	}
	try {
		$autorizacion = Factory::getInstance()->getAutorizacion(TiposAutorizacion::altaProveedor, $numeroDeAutorizacion, $proveedor->id);
		if ($autorizacion->autorizado == 'S' || $autoriza == 'N')
			throw new FactoryException('Esa autorización ya fue realizada. Recargue la página');
		else {
			$autorizacion->borrar();
		}
	} catch (FactoryExceptionRegistroNoExistente $ex) {
	}
	$autorizacionTipo = Factory::getInstance()->getAutorizacionTipo(TiposAutorizacion::altaProveedor);

	if ($autoriza == 'S') {
		$ningunRechazo = true;
		foreach ($proveedor->autorizaciones->autorizaciones as $aut)
			if ($aut->autorizado == 'N'){
				$ningunRechazo = false;
				break;
		}
	}

	$ultimaAutorizacion = (count($proveedor->autorizaciones->autorizaciones) == ($autorizacionTipo->cantidad - 1));
	$hayQueConfirmar = ($autoriza) && ($ningunRechazo) && ($ultimaAutorizacion) && (!$confirmarUltima);

	if ($hayQueConfirmar) {
		Html::jsonConfirm('Con su autorización la nota quedará aprobada y pasará a ser pedido. ¿Desea continuar?', 'confirmarUltima');
	} else {
		$nuevaAutorizacion = Factory::getInstance()->getAutorizacion();
		$nuevaAutorizacion->autorizado = $autoriza;
		$nuevaAutorizacion->autorizacionTipo = $autorizacionTipo;
		$nuevaAutorizacion->idEspecifico = $proveedor->id;
		$nuevaAutorizacion->usuario = Usuario::logueado();
		$nuevaAutorizacion->motivo = $motivo;
		$nuevaAutorizacion->numero = $numeroDeAutorizacion;
		Factory::getInstance()->persistir($nuevaAutorizacion);

		if ($autoriza == 'N') {
			//Mando mail a quienes corresponda
			mailAutorizacion($proveedor, $autoriza, $motivo);
		} elseif (($ningunRechazo) && ($ultimaAutorizacion) && ($autoriza == 'S')) {
			//Habilito el proveedor
			$proveedor->anulado = 'N';
			$proveedor->autorizado = 'S';
			try {
				$proveedor->guardar()->notificar('abm/proveedores/autorizar/');
			} catch (Exception $ex) {
				//Si falla al hacer update del proveedor con AUTORIZADO = 'S'
				//entonces tengo que volver atrás: borro la autorización.
				$nuevaAutorizacion = Factory::getInstance()->getAutorizacion(TiposAutorizacion::altaProveedor, $numeroDeAutorizacion, $proveedor->id);
				Factory::getInstance()->marcarParaBorrar($nuevaAutorizacion);
				Factory::getInstance()->persistir($nuevaAutorizacion);
				throw $ex;
			}
			//Mando mail a quienes corresponda
			mailAutorizacion($proveedor, $autoriza);
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