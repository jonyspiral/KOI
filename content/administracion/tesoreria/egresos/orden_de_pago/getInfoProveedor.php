<?php require_once('../../../../../premaster.php'); if (Usuario::logueado()->puede('administracion/tesoreria/egresos/orden_de_pago/buscar/')) { ?>
<?php

$idProveedor = Funciones::get('idProveedor');
$empresa = Funciones::session('empresa');
$confirm = Funciones::get('confirm') == '1';

try {
	Factory::getInstance()->beginTransaction();

	try {
		if (!$confirm) {
			RetencionTabla::validarExistencia();
			Html::jsonConfirm('No se crearon las alicuotas de retención para este mes. ¿Quiere copiar las del mes anterior?', 'confirm');
		} else {
			RetencionTabla::clonarMesAnterior();
			throw new Exception('Todo bien');
		}
	} catch (Exception $ex) {
		$proveedor = Factory::getInstance()->getProveedor($idProveedor);
		$attrSaldo = 'saldo' . $empresa;
		Html::jsonEncode('', array(
								  'saldo' => $proveedor->$attrSaldo,
								  'retener' => $proveedor->retenerImpuestoGanancias,
								  'imputacion' => $proveedor->imputacionGeneral->id . ' - ' . $proveedor->imputacionGeneral->nombre,
								  'plazoPago' => $proveedor->plazoPago
							 ));
	}

	Factory::getInstance()->commitTransaction();
} catch (Exception $ex) {
	Html::jsonNull();
}

?>
<?php } ?>