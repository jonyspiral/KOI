<?php require_once('../../../premaster.php'); if (Usuario::logueado()->puede('abm/curvas/agregar/')) { ?>
<?php


$nombre = Funciones::post('nombre');
$tipoDeCurva = Funciones::post('tipoDeCurva');
$cantidad == Funciones::post('cantidad');
try {

	$curva= Factory::getInstance()->getCurva();
	
	$curva->nombre = $nombre;
	$curva->tipoDeCurva= $tipoDeCurva;
	$curva->cantidad= $cantidad;

	Factory::getInstance()->persistir($curva);
	Html::jsonSuccess('La curva fue guardada correctamente');
} catch (Exception $ex){
	Html::jsonError('Ocurrió un error al intentar guardar la curva');
}
?>
<?php } ?>