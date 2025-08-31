<?php require_once('../premaster.php'); ?>
<?php

try {
	$arrCambios = array();
	$stock = Stock::getStockMenosPendiente('01');
	//$stock->arrayStock[$codArt][$codColor]
	$categorias = Factory::getInstance()->getListObject('CategoriaCalzadoUsuario', '1 = 1 ORDER BY orden ASC');
	foreach($categorias as $categoria){
		$articulos = Factory::getInstance()->getListObject('ColorPorArticulo', 'categoria_usuario = ' . Datos::objectToDB($categoria->id) . ' AND vigente = \'S\' AND naturaleza = \'PT\' ORDER BY cod_articulo ASC, cod_color_articulo ASC');
		foreach ($articulos as $item) {
			if ($item->formaDeComercializacion == 'A') {
				if ($stock->getStockArticulo($item->idArticulo, $item->id) > 0) {
					$colorPorArticulo = Factory::getInstance()->getColorPorArticulo($item->idArticulo, $item->id);
					$colorPorArticulo->formaDeComercializacion = 'T';
					Factory::getInstance()->persistir($colorPorArticulo);
					$arrCambios[] = array('ART' => $colorPorArticulo->idArticulo, 'COL' => $colorPorArticulo->id);
				}
			} elseif ($item->formaDeComercializacion == 'T') {
				if ($stock->getStockArticulo($item->idArticulo, $item->id) < 1) {
					$colorPorArticulo = Factory::getInstance()->getColorPorArticulo($item->idArticulo, $item->id);
					$colorPorArticulo->formaDeComercializacion = 'A';
					Factory::getInstance()->persistir($colorPorArticulo);
					$arrCambios[] = array('ART' => $colorPorArticulo->idArticulo, 'COL' => $colorPorArticulo->id);
				}
			}
		}
	}
	Html::jsonSuccess('', $arrCambios);
} catch (Exception $ex) {
	Html::jsonError($ex->getMessage());
}

?>