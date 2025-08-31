<?php require_once('../../../premaster.php'); if (Usuario::logueado()->puede('comercial/stock/buscar/')) { ?>
<?php

function armoHead(&$tabla, $rango) {
	$condicion = Usuario::logueado()->esVendedor() || Usuario::logueado()->esCliente() || Usuario::logueado()->tieneRol('gerencia comercial');
	$ths = array();
	$base = 7;
	$widths = ($condicion ? array(13, 5, 5, 15, 12, 6, 7, 7, 4, 4, 4, 4, 4, 4, 4, 4, 5) : array(13, 4, 4, 12, 10, 6, 7, 7, 4, 4, 4, 4, 4, 4, 4, 4, 5));
	$tabla->getHeadArray($ths);
	for ($i = 0; $i < $tabla->cantCols; $i++) {
		$ths[$i]->class = ('w' . $widths[$i] . 'p ') . ($i == 0 ? 'cornerL5' : ($i == $tabla->cantCols ? 'cornerR5 bLeftWhite' : ' bLeftWhite'));
	}
	$tabla->headerClass('tableHeader');
	$ths[0]->content = '';
	$ths[1]->content = '#Art';
	$ths[2]->content = '#Cod';
	$ths[3]->content = 'Denominación';
	$ths[4]->content = 'Linea';
	$ths[5]->content = 'Clasif';
	$ths[6]->content = 'P. lista';
	if (!$condicion) {
		$ths[7]->content = 'P. eshop';
		$base = 8;
	}
	for ($i = 0; $i < 8; $i++) {
		$ths[$i + $base]->content = ($rango->posicion[$i + 1] ? $rango->posicion[$i + 1] : '-');
	}
	$ths[$i + $base]->content = 'Total';
}

function meterItem(&$tabla, ColorPorArticulo $color, $cantidades) {
 	$row = new HtmlTableRow();
	$base = 8;
	for($i = 0; $i < $tabla->cantCols; $i++) {
		$cells[$i] = new HtmlTableCell();
		$cells[$i]->class = 'pRight5 pLeft5 ' . ($i == 3 ? '' : 'aCenter ') . ($i == 0 ? 'bAllDarkGray' : 'bTopDarkGray bBottomDarkGray bRightDarkGray');
	}
	$cells[0]->content = (Funciones::get('pdf')) ? '<img src="" />' : '<img class="cPointer" src="' . $color->getRutaImagen(TiposRutas::imagenMiniatura) . '" onclick="ampliarFoto($(this).data(\'rutaimagen\'))" width="125" height="80" ' . 'data-rutaimagen="' . $color->getRutaImagen(TiposRutas::imagenPrincipal) . '" ' . ManejadorDeImagenes::getImgOnErrorHtml() . ' />';

	$cells[1]->content = $color->idArticulo;
	$cells[2]->content = $color->id;
	$cells[3]->content = $color->articulo->nombre . ' ' . $color->nombre;
	$cells[4]->content = $color->articulo->lineaProducto->nombre;
	$cells[5]->content = $color->clasificacionComercial;

	if (Usuario::logueado()->esVendedor() || Usuario::logueado()->esCliente() || Usuario::logueado()->tieneRol('gerencia comercial')) {
		$cliente = Usuario::logueado()->esVendedor() ? Usuario::logueado()->personal->clientes[0] : Usuario::logueado()->contacto->cliente;
		$cells[6]->content = Funciones::formatearMoneda($color->getPrecioSegunCliente($cliente));
		$base = 7;
	} else {
		$cells[6]->content = Funciones::formatearMoneda($color->precioMayoristaDolar);
		$cells[7]->content = Funciones::formatearMoneda($color->ecommercePrice1);
	}

	for ($i = 0 ; $i < 8; $i++) {
		$cells[$i + $base]->content = $cantidades[$i + 1];
	}
	$cells[$i + $base]->content = Funciones::sumaArray($cantidades);
	for($i = 0; $i < $tabla->cantCols; $i++) {
		$row->addCell($cells[$i]);
	}
	$tabla->addRow($row);
}

$idArticuloBuscado = Funciones::get('idArticulo');
$idColorBuscado = Funciones::get('idColor');
$tipoProductoBuscado = Funciones::get('tipoProducto') == '' ? array() : Funciones::get('tipoProducto');
$lineaProducto = Funciones::get('lineaProducto') == '' ? array() : Funciones::get('lineaProducto');
$clasificacionComercial = Funciones::get('clasificacionComercial') == '' ? array() : Funciones::get('clasificacionComercial');
$tipoStock = Funciones::get('tipoStock');
$idAlmacen = Funciones::get('idAlmacen');

try {
	if(empty($idAlmacen)) {
		throw new FactoryExceptionCustomException('El filtro almacen es obligatorio');
	}

	$stock = ($tipoStock == '1') ? Stock::getStock($idAlmacen, false) : Stock::getStockMenosPendiente($idAlmacen, null, null, false);

	/*
	 * Formato:
	 * $arrayFinal[$codCategoriaUsuarioCalzado][$codRango][$codArt][$codColor][p1, p2, p3]
	 */
	$arrayFinal = array();

	//Sirven para no tener que pedir cada vez la categoría y el artículo
	$auxColores = array();
	$auxCategorias = array();
	$auxRangos = array();

	foreach($stock as $idArt => $arrColores) {
		$idArt = Funciones::toString($idArt);
		foreach($arrColores as $idCol => $arrPosiciones) {
			if ((isset($idArticuloBuscado) && $idArt != $idArticuloBuscado) || (isset($idColorBuscado) && $idCol != $idColorBuscado)) {
				continue;
			} else {
				$cxa = Factory::getInstance()->getColorPorArticulo($idArt, $idCol);
				if (
					($cxa->vigente == 'N') ||
					(
						isset($tipoProductoBuscado) &&
						count($tipoProductoBuscado) > 0 &&
						!in_array($cxa->idTipoProductoStock, $tipoProductoBuscado)
					) ||
					(
						isset($lineaProducto) &&
						count($lineaProducto) > 0 &&
						!in_array($cxa->articulo->idLineaProducto, $lineaProducto)
					) ||
					(
						isset($clasificacionComercial) &&
						count($clasificacionComercial) > 0 &&
						!in_array($cxa->clasificacionComercial, $clasificacionComercial)
					)
				) {
					continue;
				} else {
					//No hay filtros o está to_do bien!
					$cxa->idCategoriaCalzadoUsuario = ($cxa->idCategoriaCalzadoUsuario == ' ' || is_null($cxa->idCategoriaCalzadoUsuario) ? 'D' : $cxa->idCategoriaCalzadoUsuario);
					if (!isset($auxCategorias[$cxa->idCategoriaCalzadoUsuario]))
						$auxCategorias[$cxa->categoriaCalzadoUsuario->id] = $cxa->categoriaCalzadoUsuario;
					if (!isset($auxColores[$cxa->idArticulo]) || !isset($auxColores[$cxa->idArticulo][$cxa->id])) {
						$auxColores[$cxa->articulo->id][$cxa->id] = $cxa;
						if (!isset($auxRangos[$cxa->articulo->idRangoTalle]))
							$auxRangos[$cxa->articulo->rangoTalle->id] = $cxa->articulo->rangoTalle;
					}
					$col = $auxColores[$cxa->idArticulo][$cxa->id];
					$cat = $auxCategorias[$col->idCategoriaCalzadoUsuario];
					$rango = $auxRangos[Funciones::toInt($col->articulo->idRangoTalle)];
					$arrayFinal[$cat->id][$rango->id][$col->idArticulo][$col->id] = $stock[$col->idArticulo][$col->id];
				}
			}
		}
	}

	if (count($arrayFinal) == 0)
		throw new FactoryException('No existen registros con ese filtro');

	//Imprimo la tabla
	$html = '';
	foreach ($arrayFinal as $idCategoria => $rangos){
		$captionSetted = false;
		foreach ($rangos as $idRango => $articulos){
			$tabla = new HtmlTable(array('cantRows' => 1, 'cantCols' => (Usuario::logueado()->esVendedor() || Usuario::logueado()->esCliente() || Usuario::logueado()->tieneRol('gerencia comercial') ? 16 : 17), 'class' => 'pTop10 pBottom10', 'cellSpacing' => 1, 'width' => '99%'));
			if (!$captionSetted) {
				$tabla->caption = $auxCategorias[$idCategoria]->nombre;
				$captionSetted = true;
			}
			armoHead($tabla, $auxRangos[$idRango]);
			foreach ($articulos as $idArticulo => $colores) {
				foreach ($colores as $idColor => $cantidades) {
					meterItem($tabla, $auxColores[$idArticulo][$idColor], Funciones::soloPositivos($cantidades));
				}
			}
			$html .= $tabla->create(true);
		}
	}

	echo $html;

} catch (FactoryException $ex) {
	Html::jsonInfo($ex->getMessage());
} catch (Exception $ex) {
	Html::jsonError('Ocurrió un error al intentar obtener el stock');
}

?>
<?php } ?>