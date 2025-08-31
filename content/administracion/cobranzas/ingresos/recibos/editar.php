<?php require_once('../../../../../premaster.php'); if (Usuario::logueado()->puede('administracion/cobranzas/ingresos/recibos/editar/')) { ?>
<?php

$datos = Funciones::post('datos');
$importes = Funciones::post('importes');
$empresa = Funciones::session('empresa');
$datos['usuario'] = Usuario::logueado();

try {
	$rec = Factory::getInstance()->getRecibo($datos['idRecibo'], $empresa);
	if ($rec->esEcommerce()) {
		throw new FactoryExceptionCustomException('No se pueden editar recibos de ecommerce');
	}
	if ($rec->importePendiente != $rec->importeTotal) {
		throw new FactoryExceptionCustomException('No se puede editar un recibo ya aplicado');
	}
	$datos['idCaja_E'] = $rec->importePorOperacion->idCaja;
	$rec->datosSinValidar = $datos;
	if (isset($datos['idCliente']) && !empty($datos['idCliente'])) {
		if (count($importes[TiposImporte::cheque]) || count($importes[TiposImporte::retencionSufrida])) {
			$cli = Factory::getInstance()->getCliente($datos['idCliente']);
			foreach ($importes[TiposImporte::cheque] as &$importe) {
				$importe['cliente'] = array('id' => $cli->id);
			}
			foreach ($importes[TiposImporte::retencionSufrida] as &$importe) {
				$importe['cliente'] = array('id' => $cli->id);
				$importe['nombre'] = $cli->razonSocial;
				$importe['cuit'] = $cli->cuit;
			}
		}
	}
	$rec->importesSinValidar['E'] = $importes;
	$rec->guardar();

	Html::jsonSuccess('Se editó correctamente el recibo', $rec);
} catch (FactoryExceptionCustomException $ex) {
	Html::jsonError($ex->getMessage());
} catch (Exception $ex){
	Html::jsonError('Ocurrió un error al intentar editar el recibo');
}

?>
<?php } ?>