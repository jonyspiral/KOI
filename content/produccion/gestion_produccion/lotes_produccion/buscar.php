<?php require_once('../../../../premaster.php'); if (Usuario::logueado()->puede('produccion/gestion_produccion/lotes_produccion/buscar/')) { ?>
<?php

$id = Funciones::get('id');

try {
    $return = array();
    if ($id) {
        $lote = Factory::getInstance()->getLoteDeProduccion($id);
        /** @var $return LoteDeProduccion */
        foreach ($lote->ordenesDeFabricacion as $orden) {
            foreach ($orden->tareas as $tarea) {
                $tarea->cantidadTotal;
            }
            $orden->expand();
            $orden->articulo->rangoTalle;
            $orden->articulo->curvasDeProduccion;
        }
        $return = $lote->expand();
    } else {
        $return = Factory::getInstance()->getListObject('LoteDeProduccion', 'anulado = ' . Datos::objectToDB('N') . ' ORDER BY nro_plan DESC', 20);
    }
	Html::jsonEncode('', $return);
} catch (FactoryExceptionCustomException $ex) {
	Html::jsonInfo($ex->getMessage());
} catch (FactoryExceptionRegistroNoExistente $ex) {
	Html::jsonError('El lote "' . $id . '" no existe');
} catch (Exception $ex) {
	Html::jsonNull();
}

?>
<?php } ?>