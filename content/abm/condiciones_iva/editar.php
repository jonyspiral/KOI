<?php require_once('../../../premaster.php'); if (Usuario::logueado()->puede('abm/condiciones_iva/editar/')) { ?>
<?php

$id = Funciones::post('id');
$nombre = Funciones::post('nombre');
$letraFactura = Funciones::post('letraFactura');
$tratamiento = Funciones::post('tratamiento');
$porcentaje = array();
for ($i = 1; $i <= 5; $i++)
	$porcentaje[$i] = (is_null(Funciones::post('porcentaje' . $i)) ? 0 : Funciones::post('porcentaje' . $i));

try {
	if (!isset($id))
		throw new FactoryExceptionRegistroNoExistente();
	$condicionIva = Factory::getInstance()->getCondicionIva($id);
	$condicionIva->nombre = $nombre;
	$condicionIva->letraFactura = $letraFactura;
	$condicionIva->tratamiento = $tratamiento;
	for ($i = 1; $i <= 5; $i++)
		$condicionIva->porcentajes[$i]= Funciones::toFloat($porcentaje[$i]);

	Factory::getInstance()->persistir($condicionIva);
	Html::jsonSuccess('La condición de IVA fue guardada correctamente');
} catch (FactoryExceptionRegistroNoExistente $e) {
	Html::jsonError('La condición de IVA que intentó editar no existe');
} catch (Exception $ex){
	Html::jsonError('Ocurrió un error al intentar guardar la condición de IVA');
}
?>
<?php } ?>