<?php require_once('../../../../../premaster.php'); if (Usuario::logueado()->puede('administracion/tesoreria/cheques/chequera/buscar/')) { ?>
<?php

$fechaHasta = Funciones::get('fechaHasta');
$fechaDesde = Funciones::get('fechaDesde');
$idCuentaBancaria = Funciones::get('idCuentaBancaria');
$chequeras = array();

try {
	$where = Funciones::strFechas($fechaDesde, $fechaHasta, 'fecha') . ' AND ';
	$where .= (is_null($idCuentaBancaria) ? '' : 'cod_cuenta_bancaria = ' . Datos::objectToDB($idCuentaBancaria));
	$where = trim($where, ' AND ');
	$order = ' ORDER BY fecha DESC';

	$listaCheques = Factory::getInstance()->getArrayFromView('chequera_v', (empty($where) ? '1=1' . $order : $where . $order));

	if(empty($listaCheques))
		throw new FactoryExceptionCustomException('No existen chequeras con los filtros especificados');

	for ($i = 0; $i < count($listaCheques); $i++){
		$cheque = $listaCheques[$i];

		if($cheque['cod_chequera'] != $idAnterior){
			$aux = array();
			$aux['id'] = $cheque['cod_chequera'];
			$aux['cuentaBancaria'] = $cheque['nombre_cuenta'];
			$aux['fecha'] = Funciones::formatearFecha($cheque['fecha'], 'd/m/Y');
			$aux['numeroInicio'] = Funciones::padLeft($cheque['numero_inicio'], 8, 0);
			$aux['numeroFin'] = Funciones::padLeft($cheque['numero_fin'], 8, 0);
			$aux['detalle'] = array();
			$chequeras[$cheque['cod_chequera']] = $aux;
		}

		$aux = array();
		$aux['idChequera'] = $cheque['cod_chequera'];
		$aux['idChequeraItem'] = $cheque['cod_chequera_d'];
		$aux['numero'] = $cheque['numero'];

		$chequeras[$cheque['cod_chequera']]['detalle'][] = $aux;
		$idAnterior = $cheque['cod_chequera'];
	}

	Html::jsonEncode('', $chequeras);

} catch (FactoryExceptionCustomException $ex) {
	Html::jsonInfo($ex->getMessage());
} catch (Exception $ex) {
	Html::jsonError();
}
?>
<?php } ?>