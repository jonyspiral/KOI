<?php require_once('../../../../premaster.php'); if (Usuario::logueado()->puede('comercial/facturas/generacion/agregar/')) { ?>
<?php

$empresa = Funciones::session('empresa');
$remitos = Funciones::post('remitos');

try {
	Factory::getInstance()->beginTransaction();

	$arrAux = array();
	foreach ($remitos as $rem) {
		$rem['letra'] = ($empresa == 1 ? 'R' : 'X');
		$remito = Factory::getInstance()->getRemito($empresa, $rem['numero'], $rem['letra']);
		$rem['idCliente'] = $remito->idCliente;
		$rem['observaciones'] = $remito->observaciones;
		$arrAux[$rem['idCliente']][] = $rem;
	}
	$i = 0;

	foreach ($arrAux as $remitos) {
		$idCliente = $remitos[0]['idCliente'];
		$observaciones = (($idCliente == 291 || $idCliente == 589) ? $remitos[0]['observaciones'] : ''); //Hardcodeo a clientes varios por la observación obligatoria

		$datos = array(
			'empresa' => $empresa,
			'idCliente' => $idCliente,
			'observaciones' => $observaciones,
			'remitos' => $remitos
		);

		$factura = Factura::facturar($datos, 'comercial/facturas/generacion/agregar/');
		$i++;
	}

	Factory::getInstance()->commitTransaction();
	Html::jsonSuccess('Se generaron correctamente ' . $i . ' facturas(s)');
} catch (FactoryExceptionCustomException $ex) {
	Factory::getInstance()->rollbackTransaction();
	Html::jsonError($ex->getMessage());
} catch (Exception $ex){
	Factory::getInstance()->rollbackTransaction();
	Html::jsonError('Ocurrió un error al intentar generar la(s) factura(s)');
}

?>
<?php } ?>