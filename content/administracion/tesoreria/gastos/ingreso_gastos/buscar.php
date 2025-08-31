<?php require_once('../../../../../premaster.php'); if (Usuario::logueado()->puede('administracion/tesoreria/gastos/ingreso_gastos/buscar/')) { ?>
<?php

$empresa = Funciones::session('empresa');
$idCaja = Funciones::get('idCaja');
$comprobante = Funciones::get('comprobante');

try {
	if (empty($idCaja)) {
		throw new FactoryExceptionCustomException('Debe especificar una caja para realizar la búsqueda');
	}
	Factory::getInstance()->getPermisoPorUsuarioPorCaja($idCaja, Usuario::logueado()->id, PermisosUsuarioPorCaja::verCaja); //Esto puede tirar un FactoryExceptionRegistroNoExistente

	$where = 'cod_caja = ' . Datos::objectToDB($idCaja) . ' AND ';
	$where .= 'empresa = ' . Datos::objectToDB($empresa) . ' AND ';
	if ($comprobante == 2) {
		$where .= 'comprobante = ' . Datos::objectToDB('S') . ' AND ';
	} elseif ($comprobante == 3) {
		$where .= 'comprobante = ' . Datos::objectToDB('N') . ' AND ';
	}
	$where .= 'cod_rendicion_gastos IS NULL ';
	$order = ' ORDER BY fecha DESC';

	$gastitos = Factory::getInstance()->getListObject('Gastito', $where . $order);

	foreach($gastitos as $gastito) {
		/** @var Gastito $gastito */
		$gastito->expand();
	}
	Html::jsonEncode('', $gastitos);
} catch (FactoryExceptionCustomException $ex) {
	Html::jsonInfo($ex->getMessage());
} catch (FactoryExceptionRegistroNoExistente $ex) {
	Html::jsonError('No tiene permiso para buscar gastos en la caja indicada');
} catch (Exception $ex) {
	Html::jsonError();
}

?>
<?php } ?>