<?php require_once('../../../premaster.php'); if (Usuario::logueado()->puede('abm/articulos/buscar/')) { ?>
<?php

$id = Funciones::get('id');

try {
	$articulo = Factory::getInstance()->getArticulo($id);
	foreach ($articulo->colores as $color) {
		$color->expand();
        $curvas = array();
        foreach ($color->curvas as $curva) {
            $curvas[] = $curva->curva;
        }
        $color->curvas = $curvas;
	}
	Html::jsonEncode('', $articulo->expand());
} catch (FactoryExceptionCustomException $ex) {
	Html::jsonInfo($ex->getMessage());
} catch (FactoryExceptionRegistroNoExistente $ex) {
	Html::jsonError('El artículo "' . $id . '" no existe o no tiene permiso para visualizarlo');
} catch (Exception $ex) {
	Html::jsonNull();
}

?>
<?php } ?>