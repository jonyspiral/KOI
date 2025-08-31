<?php require_once('../../../../premaster.php'); if (Usuario::logueado()->puede('comercial/remitos/reimpresion/borrar/')) { ?>
<?php

function jsonRemito(Remito $remito) {

	$json = array();
	$json['facturado'] = (isset($remito->facturaNumero) ? 'S' : 'N');
	$json['fecha'] = $remito->fecha;
	$json['idCliente'] = $remito->cliente->id;
	$json['razonSocialCliente'] = $remito->cliente->razonSocial;
	$json['importe'] = $remito->importe;
	$json['numero'] = $remito->numero;
	$json['anulado'] = 'S';
	$json['usuarioBaja'] = Usuario::logueado()->id . ' (' . Funciones::hoy() . ')';

	return $json;
}

$empresa = Funciones::session('empresa');
$numero = Funciones::post('numero');
$letra = ($empresa == 1 ? 'R' : 'X');

try {
	$remito = Factory::getInstance()->getRemito($empresa, $numero, $letra);

	if(isset($remito->facturaNumero)){
		throw new FactoryExceptionCustomException('No se puede borrar un remito que ya fue facturado');
	}

	if($remito->anulado()){
		throw new FactoryExceptionCustomException('No se puede borrar un remito anulado');
	}

	$remito->borrar()->notificar('comercial/remitos/reimpresion/borrar/');

	$arr = jsonRemito($remito);
	$arr['nro'] = $remito->numero;
	$arr['letra'] = $remito->letra;
	Html::jsonSuccess('', $arr);
} catch (FactoryExceptionRegistroNoExistente $ex) {
	Html::jsonError('El remito que intentó borrar no existe');
} catch (FactoryExceptionCustomException $ex) {
	Html::jsonError($ex->getMessage());
} catch (Exception $ex){
	Html::jsonError('Ocurrió un error al intentar borrar el remito: ' . $ex->getMessage());
}

?>
<?php } ?>