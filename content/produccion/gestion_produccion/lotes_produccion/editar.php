<?php require_once('../../../../premaster.php'); if (Usuario::logueado()->puede('produccion/gestion_produccion/lotes_produccion/editar/')) { ?>
<?php

$id = Funciones::post('id');
$ordenesDeFabricacion = Funciones::post('ordenesDeFabricacion');
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
    if (!$id) {
        throw new FactoryExceptionCustomException('No se puede editar un lote sin su ID');
    }
    $lote = Factory::getInstance()->getLoteDeProduccion($id);
    //$lote->nombre = '';

    $ordenesNuevas = array();
    foreach ($ordenesDeFabricacion as $datosOrden) {
        limpiarDatos($datosOrden);
        $orden = Factory::getInstance()->getOrdenDeFabricacion();
        if (isset($datosOrden['id'])) {
            $orden = Factory::getInstance()->getOrdenDeFabricacion($datosOrden['id']);
        } else {
            $orden->tipoOrden = 'P';
            $orden->patron = Factory::getInstance()->getPatron($datosOrden['articulo']['id'], $datosOrden['colorPorArticulo']['id'], $datosOrden['patron']['version']);
            $orden->colorPorArticulo = $orden->patron->colorPorArticulo;
            $orden->articulo = $orden->patron->articulo;
            $orden->confirmada = 'N';
        }
        if ($datosOrden['anulado'] == 'S') {
            Factory::getInstance()->marcarParaBorrar($orden);
        } else {
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
                    throw new FactoryExceptionCustomException('Alguna de las órdenes sin confirmar tiene errores en las cantidades (verificar curvas)');
                }
            }
        }

        $orden->lanzar = $lanzar && $datosOrden['lanzar'] == "true";

        $ordenesNuevas[] = $orden;
    }

    $lote->ordenesDeFabricacion = $ordenesNuevas;

    $lote->guardar()->notificar('produccion/gestion_produccion/lotes_produccion/editar/');

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
        ? 'Las modificaciones se guardaron correctamente' . ($conError ? ', pero no todas las tareas fueron lanzadas' : '')
          . '. Se lanzaron satisfactoriamente ' . $sinError . ' tareas, y ' . $conError . ' tuvieron algún error'
        : 'El lote fue editado correctamente';

    if ($conError) {
        Html::jsonInfo($msg, array('id' => $lote->id));
    } else {
        Html::jsonSuccess($msg, array('id' => $lote->id));
    }
} catch (FactoryExceptionRegistroNoExistente $ex) {
    Html::jsonError($ex->getMessage());
} catch (FactoryExceptionCustomException $ex) {
    Html::jsonError($ex->getMessage());
} catch (Exception $ex){
    Html::jsonError('Ocurrió un error al intentar editar el lote');
}

?>
<?php } ?>