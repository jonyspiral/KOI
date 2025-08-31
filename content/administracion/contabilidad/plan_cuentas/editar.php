<?php require_once('../../../../premaster.php'); if (Usuario::logueado()->puede('administracion/contabilidad/plan_cuentas/editar/')) { ?>
<?php

$id = Funciones::post('id');
$cuenta = Funciones::post('cuenta');
$concepto = Funciones::post('concepto');
$imputable = Funciones::post('imputable');

try {
	if (!isset($id))
		throw new FactoryExceptionRegistroNoExistente();

	if (strlen($cuenta) != 7)
		throw new FactoryExceptionCustomException('El número de cuenta debe ser de 7 caracteres');

	if (is_int($cuenta))
		throw new FactoryExceptionCustomException('El número de cuenta debe ser un entero positivo');

	if($imputable != 'S' && $imputable != 'N')
		throw new FactoryExceptionCustomException('Error en el formato del campo no imputable');

	Factory::getInstance()->beginTransaction();

	$imputacion = Factory::getInstance()->getImputacion($id);

	if ($cuenta != $imputacion->id){
		try {
			$imputacionExistente = Factory::getInstance()->getImputacion($cuenta);
			if ($imputacionExistente->anulado()){
				$imputacionExistente->idNuevo = $imputacionExistente->id;
				$imputacionExistente->anulado = 'N';
				$imputacionExistente->nombre = $imputacion->nombre;
				$imputacionExistente->imputable = $imputacion->imputable;
				$imputacion->borrar();
				$imputacion = $imputacionExistente;
			} else {
				throw new FactoryExceptionCustomException('El número de cuenta ingresado ya existe en el sistema');
			}
		} catch(FactoryExceptionRegistroNoExistente $ex) {
		}
	}

	$imputacion->idNuevo = $cuenta;
	$imputacion->nombre = $concepto;
	$imputacion->imputable = $imputable;
	$imputacion->guardar()->notificar('abm/imputaciones/editar/');

	Factory::getInstance()->commitTransaction();

	Html::jsonSuccess('La imputación fue editada correctamente');
} catch (FactoryExceptionRegistroNoExistente $ex) {
	Html::jsonError('La imputación que intentó editar no existe');
} catch (FactoryExceptionCustomException $ex){
	Html::jsonError($ex->getMessage());
} catch (Exception $ex){
	Html::jsonError('Ocurrió un error al intentar guardar la imputación');
}
?>
<?php } ?>