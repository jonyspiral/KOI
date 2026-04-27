<?php require_once('../../../../premaster.php'); if (Usuario::logueado()->puede('produccion/producto/ficha_tecnica/buscar/')) { ?>
<?php

function divImg1(ColorPorArticulo $cxa, $patron) {
	$div =  '<div class="fRight">';
	$div .= '	<table>';
	$div .= '		<tbody>';
	$div .= '			<tr>';
	$div .= '				<td><span class="relative bold">Vista principal</span></td>';
	$div .= '			</tr>';
	$div .= '			<tr>';
	$div .= '				<td><img class="cPointer imageBorder" onclick="ampliarFoto(this)" src="' . $cxa->getRutaImagen(TiposRutas::imagenPrincipal, $patron->version) . '" style="max-width: 300px;" ' . ManejadorDeImagenes::getImgOnErrorHtml() . ' /></td>';
	$div .= '			</tr>';
	$div .= '			<tr>';
	$div .= '				<td><span class="relative">' . $cxa->textoVarios . '</span></td>';
	$div .= '			</tr>';
	$div .= '			<tr>';
	$div .= '				<td><span class="relative bold">Lado interno</span></td>';
	$div .= '			</tr>';
	$div .= '			<tr>';
	$div .= '				<td><img class="cPointer imageBorder" onclick="ampliarFoto(this)" src="' . $cxa->getRutaImagen(TiposRutas::imagenLadoInterno, $patron->version) . '" style="max-width: 300px;" ' . ManejadorDeImagenes::getImgOnErrorHtml() . ' /></td>';
	$div .= '			</tr>';
	$div .= '			<tr>';
	$div .= '				<td><span class="relative s10">' . $cxa->textoLadoInterno . '</span></td>';
	$div .= '			</tr>';
	$div .= '		</tbody>';
	$div .= '	</table>';
	$div .= '</div>';

	return $div;
}

function divImg3($cxa) {
	/** @var $cxa ColorPorArticulo */
	$aux = explode('\\', $cxa->fotos[4]);
	$foto1 = count($aux) && !empty($aux[count($aux) - 1]) ? '/img/zapatillas/kit_bordado/' . $aux[count($aux) - 1] : '';
	$aux = explode('\\', $cxa->fotos[6]);
	$foto2 = count($aux) && !empty($aux[count($aux) - 1]) ? '/img/zapatillas/kit_serigrafia/' . $aux[count($aux) - 1] : '';
	$aux = explode('\\', $cxa->fotos[5]);
	$foto3 = count($aux) && !empty($aux[count($aux) - 1]) ? '/img/zapatillas/kit_frecuencia_1/' . $aux[count($aux) - 1] : '';
	$aux = explode('\\', $cxa->fotos[7]);
	$foto4 = count($aux) && !empty($aux[count($aux) - 1]) ? '/img/zapatillas/kit_frecuencia_2/' . $aux[count($aux) - 1] : '';
	$aux = explode('\\', $cxa->fotos[8]);
	$foto5 = count($aux) && !empty($aux[count($aux) - 1]) ? '/img/zapatillas/kit_frecuencia_3/' . $aux[count($aux) - 1] : '';
	$div = '<div class="relative">';
	$div .= '<span class="absolute">Kit bordado</span>';
	$div .= '<img class="cPointer" onclick="ampliarFoto(this)" src="' . (file_exists(Config::pathBase . $foto1) ? $foto1 : '') . '" style="width: 116px; height: 187px;" />';
	$div .= '<span class="absolute">Kit serigrafia</span>';
	$div .= '<img class="cPointer" onclick="ampliarFoto(this)" src="' . (file_exists(Config::pathBase . $foto2) ? $foto2 : '') . '" style="width: 116px; height: 187px;" />';
	$div .= '<span class="absolute">Kit frecuencia 1</span>';
	$div .= '<img class="cPointer" onclick="ampliarFoto(this)" src="' . (file_exists(Config::pathBase . $foto3) ? $foto3 : '') . '" style="width: 116px; height: 187px;" />';
	$div .= '<span class="absolute">Kit frecuencia 2</span>';
	$div .= '<img class="cPointer" onclick="ampliarFoto(this)" src="' . (file_exists(Config::pathBase . $foto4) ? $foto4 : '') . '" style="width: 116px; height: 187px;" />';
	$div .= '<span class="absolute">Kit frecuencia 3</span>';
	$div .= '<img class="cPointer" onclick="ampliarFoto(this)" src="' . (file_exists(Config::pathBase . $foto5) ? $foto5 : '') . '" style="width: 116px; height: 187px;" />';
	$div .= '</div>';
	return $div;
}

function tablaInstrucciones($cxa) {
	/** @var $cxa ColorPorArticulo */
	$instrucciones = Factory::getInstance()->getListObject('InstruccionArticulo', 'cod_articulo = ' . Datos::objectToDB($cxa->idArticulo));
	if (!count($instrucciones)) {
		return '';
	}
	$tabla = new HtmlTable(array('cantRows' => count($instrucciones), 'cantCols' => 2, 'class' => 'pTop10 pBottom10', 'cellSpacing' => 1, 'width' => '99%'));
	$tabla->getRowCellArray($rows, $cells);
	$tabla->getHeadArray($ths);
	$tabla->headerClass('tableHeader');
	$ths[0]->style->width = '25%';
	$ths[0]->style->font_size = '11px';
	$ths[0]->class = 'cornerL5';
	$ths[0]->content = 'Sección';
	$ths[1]->style->width = '75%';
	$ths[1]->style->font_size = '11px';
	$ths[1]->class = 'cornerR5 bLeftWhite';
	$ths[1]->content = 'Instrucción';

	for ($i = 0; $i < count($instrucciones); $i++) {
		/** @var $instruccion InstruccionArticulo */
		$instruccion = $instrucciones[$i];
		$cells[$i][0]->class .= ' bLeftDarkGray bBottomDarkGray';
		$cells[$i][0]->style->font_size = '11px';
		$cells[$i][0]->content = $instruccion->seccion->nombre;
		$cells[$i][1]->class .= ' bRightDarkGray bBottomDarkGray';
		$cells[$i][1]->style->font_size = '11px';
		$cells[$i][1]->content = $instruccion->instruccion;
	}

	return $tabla->create(true);
}

function tablaPatron($cxa, $version) {
	/** @var $cxa ColorPorArticulo */
	$where = 'cod_articulo = ' . Datos::objectToDB($cxa->idArticulo) . ' AND cod_color_articulo = ' . Datos::objectToDB($cxa->id) . ' AND version = ' . Datos::objectToDB($version);
	$order = ' ORDER BY cod_seccion ASC, conjunto ASC, cod_material ASC, cod_color_material ASC';
	$patrones = Factory::getInstance()->getArrayFromView('ficha_tecnica_patrones_d', $where . $order);
	$tabla = new HtmlTable(array('cantRows' => count($patrones)+1, 'cantCols' => 10, 'id' => 'patron', 'class' => 'pTop10 pBottom10 overflowhidden', 'cellSpacing' => 1, 'width' => '100%'));
	$tabla->getRowCellArray($rows, $cells);
	$tabla->getHeadArray($ths);
	$tabla->headerClass('tableHeader');

	$widths = array(5, 7, 5, 15, 6, 41, 5, 5, 5, 5);
	for ($i = 0; $i < $tabla->cantCols; $i++) {
		$ths[$i]->style->font_size = '11px';
		$ths[$i]->style->width = $widths[$i] . '%';
		$ths[$i]->class = ($i == 0 ? 'cornerL5' : ($i == $tabla->cantCols - 1 ? 'cornerR5 bLeftWhite' : 'bLeftWhite'));
	}
	for($i = 0; $i < $tabla->cantRows; $i++) {
		for ($j = 0; $j < $tabla->cantCols; $j++) {
			$cells[$i][$j]->class = ($j == $tabla->cantCols - 1 ? ' bRightDarkGray bBottomDarkGray' : ' bLeftDarkGray bBottomDarkGray');
		}
	}

	$ths[0]->content = 'C. Sec';
	$ths[0]->dataType = 'Center';
	$ths[1]->content = 'Sección';
	$ths[2]->content = 'C. Con';
	$ths[2]->dataType = 'Center';
	$ths[3]->content = 'Conjunto';
	$ths[4]->content = 'C. Mat';
	$ths[4]->dataType = 'Center';
	$ths[5]->content = 'Material';
	$ths[6]->content = 'Col';
	$ths[6]->dataType = 'Center';
	$ths[7]->content = 'Cons';
	$ths[7]->dataType = 'Center';
	$ths[8]->content = 'UMS';
	$ths[8]->dataType = 'Center';
	$ths[9]->content = 'Costo';
	$ths[9]->dataType = 'Center';

	$costo = 0;

	$seccion = $patrones[0]['cod_seccion'];
	$rowClass = ' bDarkGray bold';
	for ($i = 0; $i < count($patrones); $i++) {
		$patron = $patrones[$i];
		$cells[$i][0]->content = $patron['cod_seccion'];
		$cells[$i][1]->content = Factory::getInstance()->getSeccionProduccion($patron['cod_seccion'])->nombre;
		$cells[$i][2]->content = trim($patron['conjunto']);
		$cells[$i][3]->content = $patron['denom_conjunto'];
		$cells[$i][4]->content = trim($patron['cod_material']);
		$cells[$i][5]->content = $patron['denominacion_material'];
		$cells[$i][6]->content = $patron['cod_color_material'];
		$cells[$i][7]->content = Funciones::formatearDecimales($patron['consumo_par'], 4);
		$cells[$i][8]->content = $patron['ums'];
		$cells[$i][9]->content = Funciones::formatearDecimales($patron['costo'], 4);

		$costo += $patron['costo'];

		if ($seccion != $patron['cod_seccion']) {
			$rowClass = ($rowClass == ' bLightGray' ? ' bDarkGray bold' : ' bLightGray');
		}

		$rows[$i]->class .= $rowClass;

		for ($j = 0; $j < $tabla->cantCols; $j++) {
			$cells[$i][$j]->style->font_size = '11px';
		}

		$seccion = $patron['cod_seccion'];
	}

	for($i=0;$i<10;$i++){
		$cells[count($patrones)][$i]->content = '';
		$cells[count($patrones)][$i]->class = $cells[count($patrones)-1][$i]->class;
		$cells[count($patrones)][$i]->colspan = $cells[count($patrones)-1][$i]->colspan;
		$cells[count($patrones)][$i]->style = $cells[count($patrones)-1][$i]->style;
		$cells[count($patrones)][$i]->baseclass = $cells[count($patrones)-1][$i]->baseclass;
		$cells[count($patrones)][$i]->baseclasslast = $cells[count($patrones)-1][$i]->baseclasslast;
	}
	$cells[count($patrones)][7]->content = 'Costo Total: ';
	$cells[count($patrones)][7]->colspan = 2;
	$cells[count($patrones)][9]->content = Funciones::formatearDecimales($costo, 4);

	return $tabla->create(true);
}


//Ficha
function divFichaEspecificaciones($cxa, $patron) {
	global $tablaCompartida;

	/** @var ColorPorArticulo $cxa */
	$div .= '<div class="aCenter w100p">';
	$div .= $tablaCompartida;
	$div .= '	<table>';
	$div .= '		<tbody>';
	$div .= '			<tr>';
	$div .= '				<td class="w30p bold"><span class="relative">Zoom puntera</span></td>';
	$div .= '				<td class="w30p bold"><span class="relative">Vista principal</span></td>';
	$div .= '				<td class="w30p bold"><span class="relative">Zoom talon</span></td>';
	$div .= '			</tr>';
	$div .= '			<tr>';
	$div .= '				<td class="w30p"><img class="cPointer imageBorder" onclick="ampliarFoto(this)" src="' . $cxa->getRutaImagen(TiposRutas::imagenPuntera, $patron->version) . '" style="max-height: 240px;" ' . ManejadorDeImagenes::getImgOnErrorHtml() . ' /></td>';
	$div .= '				<td class="w40p">';
	$div .= '					<table>';
	$div .= '						<tbody>';
	$div .= '							<tr>';
	$div .= '								<td rowspan="2"><img class="cPointer imageBorder" onclick="ampliarFoto(this)" src="' . $cxa->getRutaImagen(TiposRutas::imagenPrincipal, $patron->version) . '" style="max-height: 240px;" ' . ManejadorDeImagenes::getImgOnErrorHtml() . ' /></td>';
	$div .= '								<td><img class="cPointer imageBorder" onclick="ampliarFoto(this)" src="' . $cxa->getRutaImagen(TiposRutas::imagenEtiquetaLengua, $patron->version) . '" style="max-height: 115px;" ' . ManejadorDeImagenes::getImgOnErrorHtml() . ' /></td>';
	$div .= '							</tr>';
	$div .= '							<tr>';
	$div .= '								<td><img class="cPointer imageBorder" onclick="ampliarFoto(this)" src="' . $cxa->getRutaImagen(TiposRutas::imagenEtiquetaCania, $patron->version) . '" style="max-height: 115px;" ' . ManejadorDeImagenes::getImgOnErrorHtml() . ' /></td>';
	$div .= '							</tr>';
	$div .= '						</tbody>';
	$div .= '					</table>';
	$div .= '				</td>';
	$div .= '				<td class="w30p"><img class="cPointer imageBorder" onclick="ampliarFoto(this)" src="' . $cxa->getRutaImagen(TiposRutas::imagenTalon, $patron->version) . '" style="max-height: 240px;" ' . ManejadorDeImagenes::getImgOnErrorHtml() . ' /></td>';
	$div .= '			</tr>';
	$div .= '			<tr>';
	$div .= '				<td class="w30p"><span class="relative s10">' . $cxa->textoPuntera . '</span></td>';
	$div .= '				<td class="w40p"><span class="relative s10">' . $cxa->textoVarios . '</span></td>';
	$div .= '				<td class="w30p"><span class="relative s10">' . $cxa->textoTalon . '</span></td>';
	$div .= '			</tr>';
	$div .= '			<tr></tr>';
	$div .= '			<tr>';
	$div .= '				<td class="w30p bold"><span class="relative">Lengua</span></td>';
	$div .= '				<td class="w40p bold"><span class="relative">Lado interno</span></td>';
	$div .= '				<td class="w30p bold"><span class="relative">Zoom caña</span></td>';
	$div .= '			</tr>';
	$div .= '			<tr>';
	$div .= '				<td><img class="cPointer imageBorder" onclick="ampliarFoto(this)" src="' . $cxa->getRutaImagen(TiposRutas::imagenLengua, $patron->version) . '" style="max-height: 190px;" ' . ManejadorDeImagenes::getImgOnErrorHtml() . ' /></td>';
	$div .= '				<td><img class="cPointer imageBorder" onclick="ampliarFoto(this)" src="' . $cxa->getRutaImagen(TiposRutas::imagenLadoInterno, $patron->version) . '" style="max-height: 190px;" ' . ManejadorDeImagenes::getImgOnErrorHtml() . ' /></td>';
	$div .= '				<td><img class="cPointer imageBorder" onclick="ampliarFoto(this)" src="' . $cxa->getRutaImagen(TiposRutas::imagenCania, $patron->version) . '" style="max-height: 190px;" ' . ManejadorDeImagenes::getImgOnErrorHtml() . ' /></td>';
	$div .= '			</tr>';
	$div .= '			<tr>';
	$div .= '				<td class="w30p"><span class="relative">' . $cxa->textoLengua . '</span></td>';
	$div .= '				<td class="w40p"><span class="relative s10">' . $cxa->textoLadoInterno . '</span></td>';
	$div .= '				<td class="w30p"><span class="relative s10">' . $cxa->textoCania . '</span></td>';
	$div .= '			</tr>';
	$div .= '			<tr>';
	$div .= '				<td colspan="3">' . divImg3($cxa) . '</td>';
	$div .= '			</tr>';
	$div .= '		</tbody>';
	$div .= '	</table>';
	$div .= '</div>';

	return $div;
}

$idArticulo = Funciones::get('idArticulo');
$idColor = Funciones::get('idColorPorArticulo');
$version = Funciones::get('idVersion');
$pdf = !!Funciones::get('pdf');

try {
	if(empty($idArticulo) || empty($idColor)) {
		throw new FactoryExceptionCustomException('Debe elegir un articulo y un color');
	}
	$cxa = Factory::getInstance()->getColorPorArticulo($idArticulo, $idColor);

	$html = '<div class="w100p customScroll solapas">
			 <ul class="titulos printHidden">
				<li id="liTabFichaBase">Ficha base</li>
				<li id="liTabFichaEspecificaciones">Ficha especificaciones</li>
			 </ul>
			 <div class="divContenido">
			 	<div>';

	/** @var $cxa ColorPorArticulo */
	$where = 'cod_articulo = ' . Datos::objectToDB($cxa->idArticulo) . ' AND cod_color_articulo = ' . Datos::objectToDB($cxa->id);
	$where .=  ($version ? ' AND version = ' . Datos::objectToDB($version) : ' AND version_actual = ' . Datos::objectToDB('S'));
	$order = ' ORDER BY version DESC';
	$patrones = Factory::getInstance()->getListObject('Patron', $where . $order, 1);
	if (!count($patrones)) {
		throw new FactoryExceptionCustomException('No se encontró ningun patrón para el artículo/color/versión ingresado');
	}
	/** @var $patron Patron */
	$patron = $patrones[0];
	$version = $patron->version;

	$tablaCompartida =  '<table class="aCenter w100p">';
	$tablaCompartida .= '	<thead class="tableHeader">';
	$tablaCompartida .= '		<tr>';
	$tablaCompartida .= '			<th>Artículo</th>';
	$tablaCompartida .= '			<th>Color</th>';
	$tablaCompartida .= '			<th>Horma</th>';
	$tablaCompartida .= '			<th>Patron</th>';
	$tablaCompartida .= '			<th>Confirm.</th>';
	$tablaCompartida .= '			<th>Borrador</th>';
	$tablaCompartida .= '			<th>Versión</th>';
	$tablaCompartida .= '			<th>Vigente</th>';
	$tablaCompartida .= '		</tr>';
	$tablaCompartida .= '	</thead>';
	$tablaCompartida .= '	<tbody>';
	$tablaCompartida .= '		<tr>';
	$tablaCompartida .= '			<td class="bBottomDarkGray bLeftDarkGray">[' . trim($cxa->articulo->id) . '] ' . $cxa->articulo->nombre . '</td>';
	$tablaCompartida .= '			<td class="bBottomDarkGray bLeftDarkGray">' . $cxa->id . '</td>';
	$tablaCompartida .= '			<td class="bBottomDarkGray bLeftDarkGray">' . (empty($cxa->articulo->horma->id) ? '-' : '[' . trim($cxa->articulo->horma->id) . '] ' . $cxa->articulo->horma->nombre) . '</td>';
	$tablaCompartida .= '			<td class="bBottomDarkGray bLeftDarkGray">' . $patron->tipoPatron . '</td>';
	$tablaCompartida .= '			<td class="bBottomDarkGray bLeftDarkGray">' . $patron->confirmado . '</td>';
	$tablaCompartida .= '			<td class="bBottomDarkGray bLeftDarkGray">' . $patron->borrador . '</td>';
	$tablaCompartida .= '			<td class="bBottomDarkGray bLeftDarkGray">' . $patron->version . '</td>';
	$tablaCompartida .= '			<td class="bBottomDarkGray bLeftDarkGray bRightDarkGray">' . $patron->versionActual . '</td>';
	$tablaCompartida .= '		</tr>';
	$tablaCompartida .= '	</tbody>';
	$tablaCompartida .= '</table>';

	//Genero la tabla de datos y las imágenes de lado interno y lengua
	$divImg1 = divImg1($cxa, $patron, $pdf);
	//Genero la tabla de Instrucciones
	$tablaInstrucciones = tablaInstrucciones($cxa);
	//Genero la tabla de patrón
	$tablaPatron = tablaPatron($cxa, $version);

	$html .= '<div>' . $tablaCompartida . '</div>';
	$html .= '<div class="fLeft s11">';
	$html .= '<table class="aCenter w100p">';
	$html .= '	<tbody>';
	$html .= '		<tr>';
	$html .= '			<td>' . $tablaPatron . '</td>';
	$html .= '			<td>' . $divImg1 . '</td>';
	$html .= '		</tr>';
	$html .= '	</tbody>';
	$html .= '</table>';
	$html .= '</div>';
	$html .= '</div>';
	$html .= '<div>';
	$html .= divFichaEspecificaciones($cxa, $patron);
	$html .= '</div></div></div>';

	$anterior = $cxa->getAnterior();
	$siguiente = $cxa->getSiguiente();

	$html .= '<div><table class="w99p"><tr><td><span class="cPointer sgteAnt" onclick="buscarSiguienteAnterior(' . Datos::objectToDB($anterior['idArticulo']) . ', ' . Datos::objectToDB($anterior['idColor']) . ')">< (' . $anterior['idArticulo'] . $anterior['idColor'] . ') Anterior</span></td><td> | </td><td><span class="cPointer sgteAnt" onclick="buscarSiguienteAnterior(' . Datos::objectToDB($siguiente['idArticulo']) . ', ' . Datos::objectToDB($siguiente['idColor']) . ')">Siguiente (' . $siguiente['idArticulo'] . $siguiente['idColor'] . ') ></span></td></tr></table></div>';

	echo $html;
} catch (FactoryExceptionCustomException $ex) {
	Html::jsonInfo($ex->getMessage());
} catch (Exception $ex) {
	Logger::addError($ex);
	Html::jsonError('Ocurrió un error al intentar obtener la ficha técnica');
}

?>
<?php } ?>
