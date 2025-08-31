<?php require_once('../../../../../premaster.php'); if (Usuario::logueado()->puede('administracion/tesoreria/egresos/orden_de_pago/agregar/')) { ?>
<?php

$datos = Funciones::post('datos');
$importes = Funciones::post('importes');
$confirm = Funciones::get('confirm') == '1';
$empresa = Funciones::session('empresa');
$datos['usuario'] = Usuario::logueado();

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
		$op = Factory::getInstance()->getOrdenDePago();
		$op->empresa = $empresa;
		$op->datosSinValidar = $datos;



		if ($empresa == '1' && $datos['retieneGanancias'] == 'S') {
			$prov = Factory::getInstance()->getProveedor($datos['idProveedor']);
			$importeSujetoRet = 0;
			foreach($importes as $tipo) {
				foreach($tipo as $imp) {
					$importeSujetoRet += Funciones::toFloat($imp['importe']);
				}
			}
			$importeNeto = $importeSujetoRet;
			$impRet = $prov->calcularRetencion($importeSujetoRet);
			$op->importeSujetoRetencion = $importeSujetoRet;
			$importes['R'] = array(
				array(
					'tipoRetencion'	=> array('id' => RetencionEfectuada::ID),
					'nombre'		=> $prov->razonSocial,
					'cuit'			=> $prov->cuit,
					'importe'		=> $impRet,
					'importeNeto'	=> $importeNeto,
					'fecha'			=> Funciones::hoy(),
					'proveedor'		=> array('id' => $prov->id)
				)
			);
		}
		$op->importesSinValidar['S'] = $importes;

		$op->guardar();
		Html::jsonSuccess('Se generó correctamente la orden de pago', $op);
	}

	Factory::getInstance()->commitTransaction();
} catch (FactoryExceptionCustomException $ex) {
	Html::jsonError($ex->getMessage());
} catch (Exception $ex){
	Html::jsonError('Ocurrió un error al intentar generar la orden de pago');
}

?>
<?php } ?>