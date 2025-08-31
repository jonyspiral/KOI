<?php require_once('../../../../premaster.php'); if (Usuario::logueado()->puede('produccion/gestion_produccion/lotes_produccion/agregar/')) { ?>
<?php

$idForecast = Funciones::post('idForecast');

try {
    if (!$idForecast) {
        throw new FactoryExceptionCustomException('Debe elegir un Forecast para continuar');
    }
	$forecast = Factory::getInstance()->getForecast($idForecast);

    $ordenes = array();
    foreach ($forecast->detalle as $item) {
        $orden = Factory::getInstance()->getOrdenDeFabricacion();
        $orden->tipoOrden = 'P';
        if (!$item->version) {
            throw new FactoryExceptionCustomException('No se pudo importar el Forecast dado que no se ha especificado el patrón para el artículo [' . $item->idArticulo . '-' . $item->idColorPorArticulo . ']');
        }
        $orden->patron = $item->patron;
        $orden->colorPorArticulo = $orden->patron->colorPorArticulo;
        $orden->articulo = $orden->patron->articulo;
        $orden->confirmada = 'N';
        $orden->fechaInicio = $forecast->fechaInicio;
        $orden->fechaFin = $forecast->fechaFin;

        // Tratamiento de cantidades
        $orden->cantidadTotal = Funciones::toInt($item->cantidadTotal);

        /* Esto no sirve porque en el forecast no ponen las cantidades, y eso es necesario para cuando hay una curva seleccionada
        $curvaSeleccionada = false;
        $restoSeleccionada = 0;
        foreach ($orden->articulo->curvasDeProduccion as $curvaEvaluada) {
            if (!$curva || ($orden->cantidadTotal % $curvaEvaluada->cantidadTotal <= $restoSeleccionada)) {
                $curvaSeleccionada = $curvaEvaluada;
                $restoSeleccionada = $orden->cantidadTotal % $curvaEvaluada->cantidadTotal;
            }
        }
        if ($curvaSeleccionada) {
            $orden->curvaDeProduccion = $curvaSeleccionada;
            $orden->cantidadOptimaProduccion = $orden->curvaDeProduccion->cantidadTotal;

            for ($i = 1; $i <= 10; $i++) {
                $orden->cantidad[$i] = Funciones::toInt($item->cantidad[$i]);
            }

            if (Funciones::sumaArray($orden->cantidad) != $orden->cantidadTotal) {
                throw new FactoryExceptionCustomException('La suma de las cantidades del item Nº ' . $item->id . ' es distinta al campo "Cantidad Total". Por favor, corrija el Forecast y vuelva a intentarlo');
            }
        }
        */

        $ordenes[] = $orden;
    }

	$lote = Factory::getInstance()->getLoteDeProduccion();
    $lote->nombre = 'Del Forecast Nº ' . $forecast->id;
    $lote->forecast = $forecast;
    $lote->ordenesDeFabricacion = $ordenes;

    $forecast->importado = 'S';

    Factory::getInstance()->beginTransaction();

    $forecast->guardar();
	$lote->guardar()->notificar('produccion/gestion_produccion/lotes_produccion/agregar/');

    Factory::getInstance()->commitTransaction();

	Html::jsonSuccess('El Forecast ' . $forecast->id . ' fue importado correctamente. Se creó el lote Nº ' . $lote->id, array('id' => $lote->id));
} catch (FactoryExceptionRegistroNoExistente $ex) {
    Html::jsonError($ex->getMessage());
} catch (FactoryExceptionCustomException $ex) {
    Html::jsonError($ex->getMessage());
} catch (Exception $ex){
	Html::jsonError('Ocurrió un error al intentar importar el Forecast');
}

?>
<?php } ?>