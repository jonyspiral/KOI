<?php require_once('../../../premaster.php'); if (Usuario::logueado()->puede('abm/curvas/editar/')) { ?>
<?php
 

$id= Funciones::post('id');
$nombre = Funciones::post('nombre');
$tipoDeCurva = Funciones::post('tipoDeCurva');
$cantidad == Funciones::post('cantidad');

try {
	if (!isset($id))
		throw new FactoryExceptionRegistroNoExistente();
	
	$curva= Factory::getInstance()->getCurva($id);
	
	$curva->nombre = $nombre;
	$curva->tipoDeCurva= $tipoDeCurva;
	$curva->cantidad= $cantidad;

	Factory::getInstance()->persistir($curva);
		
	Html::jsonSuccess('La curva fue guardada correctamente');
} catch (FactoryExceptionRegistroNoExistente $e) {
	Html::jsonError('La curva que intentó editar no existe');
} catch (Exception $ex){
	Html::jsonError('Ocurrió un error al intentar guardar curva');
}
?>
<?php } ?>