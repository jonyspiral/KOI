<?php require_once('../../../../../premaster.php'); if (Usuario::logueado()->puede('administracion/cobranzas/ingresos/recibos/agregar/')) { ?>
<?php

$datos = Funciones::post('datos');
$importes = Funciones::post('importes');
$empresa = Funciones::session('empresa');
$datos['usuario'] = Usuario::logueado();

try {
	if (empty($datos['numeroReciboProvisorio'])) {
		throw new FactoryExceptionCustomException('Debe completar todos los campos obligatorios');
	}

	$rec = Factory::getInstance()->getRecibo();
	$rec->empresa = $empresa;
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

	Html::jsonSuccess('Se generó correctamente el recibo', $rec);
} catch (FactoryExceptionCustomException $ex) {
	Html::jsonError($ex->getMessage());
} catch (Exception $ex){
	Html::jsonError('Ocurrió un error al intentar generar el recibo');
}

?>
<?php } ?>