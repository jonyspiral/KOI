<?php require_once('../../../../premaster.php'); if (Usuario::logueado()->puede('administracion/tesoreria/reimpresion_ordenes_de_pago/borrar/')) { ?>
<?php

function jsonOrdenDePago(OrdenDePago $ordenDePago) {

	$json = array();
	$json['empresa'] = $ordenDePago->empresa;
	$json['mailEnviado'] = $ordenDePago->mailEnviado;
	$json['fecha'] = $ordenDePago->fecha;
	$json['idProveedor'] = $ordenDePago->proveedor->id;
	$json['razonSocialProveedor'] = $ordenDePago->proveedor->razonSocial;
	$json['importeTotal'] = $ordenDePago->importeTotal;
	$json['importePendiente'] = $ordenDePago->importePendiente;
	$json['numero'] = $ordenDePago->numero;
	$json['idImputacion'] = $ordenDePago->imputacion->id;
	$json['beneficiario'] = $ordenDePago->beneficiario;
	$json['observaciones'] = $ordenDePago->observaciones;
	$json['anulado'] = 'S';
	$json['usuarioBaja'] = Usuario::logueado()->id . ' (' . Funciones::hoy() . ')';
	return $json;
}

$idOp = Funciones::post('numero');
$empresa = Funciones::session('empresa');

try {
	$op = Factory::getInstance()->getOrdenDePago($idOp, $empresa);

	if($op->anulado == 'S')
		throw new FactoryExceptionCustomException('No puede borrar una orden de pago anulada.');

	if ($op->importePendiente != $op->importeTotal)
		throw new FactoryExceptionCustomException('No se puede borrar una órden de pago ya aplicada');

	$op->borrar();

	Html::jsonSuccess('Se borró correctamente la orden de pago', jsonOrdenDePago($op));
} catch (FactoryExceptionCustomException $ex) {
	Html::jsonError($ex->getMessage());
} catch (Exception $ex){
	Html::jsonError('Ocurrió un error al intentar borrar la orden de pago');
}

?>
<?php } ?>