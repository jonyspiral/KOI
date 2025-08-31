<?php require_once('../../../../premaster.php'); if (Usuario::logueado()->puede('produccion/gestion_produccion/lotes_produccion/agregar/')) { ?>
<?php

$ordenesDeFabricacion = Funciones::post('ordenesDeFabricacion');
$nombre = Funciones::post('nombre');
$lanzar = !!Funciones::post('lanzar');

function limpiarDatos(&$datosOrden) {
    foreach ($datosOrden as $key => $dato) {
        if ($dato == 'null' || empty($dato)) {
            $dato = null;
        }
        $datosOrden[$key] = $dato;
    }
}

try {
	$lote = Factory::getInstance()->getLoteDeProduccion();
    $lote->nombre = $nombre;

    $ordenes = array();
    foreach ($ordenesDeFabricacion as $datosOrden) {
        limpiarDatos($datosOrden);
        $orden = Factory::getInstance()->getOrdenDeFabricacion();
        $orden->tipoOrden = 'P';
        $orden->patron = Factory::getInstance()->getPatron($datosOrden['articulo']['id'], $datosOrden['colorPorArticulo']['id'], $datosOrden['patron']['version']);
        $orden->colorPorArticulo = $orden->patron->colorPorArticulo;
        $orden->articulo = $orden->patron->articulo;
        $orden->confirmada = 'N';
        $orden->fechaInicio = $datosOrden['fechaInicio'];

        // Tratamiento de cantidades
        $orden->cantidadTotal = Funciones::toInt($datosOrden['cantidadTotal']);
        if (isset($datosOrden['curvaDeProduccion']['id']) && is_numeric($datosOrden['curvaDeProduccion']['id'])) {
            $orden->curvaDeProduccion = Factory::getInstance()->getCurvaProduccionPorArticulo($datosOrden['curvaDeProduccion']['id']);
            $orden->cantidadOptimaProduccion = $orden->curvaDeProduccion->cantidadTotal;

            $cantidadDeCurvas = floor($orden->cantidadTotal / $orden->curvaDeProduccion->cantidadTotal);
            for ($i = 1; $i <= 10; $i++) {
                $orden->cantidad[$i] = ($cantidadDeCurvas * Funciones::toInt($orden->curvaDeProduccion->cantidad[$i])) + Funciones::toInt($datosOrden['curvaLibre'][$i]);
            }

            if (Funciones::sumaArray($orden->cantidad) != $orden->cantidadTotal) {
                throw new FactoryExceptionCustomException('Alguna de las Ã³rdenes sin confirmar tiene errores en las cantidades (verificar curvas)');
            }
        }

        $orden->lanzar = $lanzar && $datosOrden['lanzar'] == "true";

        $ordenes[] = $orden;
    }

    $lote->ordenesDeFabricacion = $ordenes;

	$lote->guardar()->notificar('produccion/gestion_produccion/lotes_produccion/agregar/');

    $sinError = 0;
    $conError = 0;
    if ($lanzar) {
        try {
            foreach ($lote->ordenesDeFabricacion as $orden) {
                if ($orden->lanzar) {
                    $orden->lanzar();
                    $sinError++;
                }
            }
        } catch (Exception $ex) {
            Logger::addError('Error al lanzar alguna orden: ' . $ex->getMessage(), array('id' => $orden->id));
            $conError++;
        }
    }

    $msg = $lanzar
        ? 'El lote se guardó correctamente' . ($conError ? ', pero no todas las tareas fueron lanzadas' : '')
          . '. Se lanzaron satisfactoriamente ' . $sinError . ' tareas, y ' . $conError . ' tuvieron algún error'
        : 'El lote fue guardado correctamente';

    if ($conError) {
        Html::jsonInfo($msg, array('id' => $lote->id));
    } else {
        Html::jsonSuccess($msg, array('id' => $lote->id));
    }
} catch (FactoryExceptionRegistroNoExistente $ex) {
    Html::jsonError($ex->getMessage());
} catch (Exception $ex){
	Html::jsonError('Ocurrió un error al intentar guardar el lote');
}

?>
<?php } ?>