<?php require_once('../../../premaster.php'); if (Usuario::logueado()->puede('abm/seccion_produccion/editar/')) { ?>
<?php

$id = Funciones::post('id');
$nombre = Funciones::post('nombre');
$nombreCorto = Funciones::post('nombreCorto');
$imprimeStickers = Funciones::post('imprimeStickers') == 'S' ? 'S' : 'N';
$jerarquiaSeccion = Funciones::post('jerarquiaSeccion') == 'P' ? 'P' : 'S';
$idSeccionSuperior = Funciones::post('idSeccionSuperior');
$ingresaAlStock = Funciones::post('ingresaAlStock') == 'S' ? 'S' : 'N';
$interrumpible = Funciones::post('interrumpible') == 'S' ? 'S' : 'N';
$idUnidadDeMedida = Funciones::post('idUnidadDeMedida') == 'M' ? 'M' : 'P';
$idAlmacenDefault = Funciones::post('idAlmacenDefault');

$almacenes = Funciones::post('almacenes');

try {
    if (!isset($id)) {
        throw new FactoryExceptionCustomException();
    }
    $seccion = Factory::getInstance()->getSeccionProduccion($id);

    $seccion->nombre = $nombre;
    $seccion->nombreCorto = $nombreCorto;
    $seccion->imprimeStickers = $imprimeStickers;
    $seccion->jerarquiaSeccion = $jerarquiaSeccion;
    $seccion->seccionSuperior = Factory::getInstance()->getSeccionProduccion($idSeccionSuperior);
    $seccion->ingresaAlStock = $ingresaAlStock;
    $seccion->interrumpible = $interrumpible;
    $seccion->unidadDeMedida = Factory::getInstance()->getUnidadDeMedida($idUnidadDeMedida);
    $seccion->almacenDefault = Factory::getInstance()->getAlmacen($idAlmacenDefault);

    $auxArray = array();
    foreach ($almacenes as $alm) {
        $auxArray[$alm] = array('idAlmacen' => $alm, 'existente' => false);
    }

    foreach ($seccion->almacenes as $almacen) {
        if (isset($auxArray[$almacen->id])) {
            $auxArray[$almacen->id]['existente'] = true;
        } elseif (!$almacen->anulado()) {
            Factory::getInstance()->marcarParaBorrar($almacen);
        }
    }

    foreach ($auxArray as $alm) {
        if (!$alm['existente']) {
            $seccion->addAlmacen(Factory::getInstance()->getAlmacen($alm['idAlmacen']));
        }
    }

    $seccion->guardar()->notificar('abm/seccion_produccion/editar/');

    Html::jsonSuccess('La sección fue guardada correctamente');
} catch (FactoryExceptionCustomException $e) {
    Html::jsonError($ex->getMessage());
} catch (FactoryExceptionRegistroNoExistente $e) {
    Html::jsonError('La sección que intentó editar no existe');
} catch (Exception $ex){
    Html::jsonError('Ocurrió un error al intentar guardar la sección');
}

?>
<?php } ?>