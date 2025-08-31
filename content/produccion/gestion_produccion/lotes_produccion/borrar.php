<?php require_once('../../../../premaster.php'); if (Usuario::logueado()->puede('produccion/gestion_produccion/lotes_produccion/borrar/')) { ?>
<?php

$id = Funciones::post('id');

try {
	$lote = Factory::getInstance()->getLoteDeProduccion($id);
    foreach ($lote->ordenesDeFabricacion as $orden) {
        if ($orden->confirmada()) {
            throw new FactoryExceptionCustomException('No se puede borrar un lote con órdenes de fabricación confirmadas (las tareas ya están lanzadas)');
        }
        Factory::getInstance()->marcarParaBorrar($orden);
    }
	$lote->borrar()->notificar('produccion/gestion_produccion/lotes_produccion/borrar/');

	Html::jsonSuccess('El lote fue borrado correctamente');
} catch (FactoryExceptionRegistroNoExistente $e) {
	Html::jsonError('El artículo que intentó borrar no existe');
} catch (FactoryExceptionCustomException $ex) {
	Html::jsonError($ex->getMessage());
} catch (Exception $ex){
	Html::jsonError('Ocurrió un error al intentar borrar el artículo');
}

?>
<?php } ?>