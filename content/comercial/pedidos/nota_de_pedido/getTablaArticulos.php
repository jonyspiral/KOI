<?php require_once('../../../../premaster.php'); if (Usuario::logueado()->puede('comercial/pedidos/nota_de_pedido/')) { ?>
<?php

/**
 * Este archivo pone en práctica una manera distinta de usar HtmlTable
 * La arma sin setearle cantCols y cantRows, y después les asigna las rows y cols con un loop
 * Ver para entender 
 */

$idCat = Funciones::get('idCategoria');

function serializeCurva($item){
    /** @var ColorPorArticulo $item */
	//Los "-" separan números y las "|" separan curvas
	if ($item->formaDeComercializacion == 'M'){
		$curvas = '';
		foreach($item->curvas as $curva){
			$isZero = true;
			$i = 0;
			$auxCurva = '';
			foreach($curva->curva->cantidad as $cant){
				if ($i > 7)
					continue;
				$i++;
				if ($cant != 0)
					$isZero = false;
				$auxCurva .= Funciones::iIsSet($cant, '0') . '-';
			}
			if (!$isZero)
				$curvas .= $curva->idCurva . '_' . substr($auxCurva, 0, -1) . '|';
		}
		$curvas = substr($curvas, 0, -1);
		$curvas = 'curvas="' . $curvas . '"';
		return $curvas;
	}
	return '';
}

function serializeLibre($item){
	$cant = 0;
	foreach ($item->articulo->rangoTalle->posicion as $pos)
		if (isset($pos))
			$cant++;
	return 'cantPos="' . $cant . '" ';
}

function generarPathFoto(ColorPorArticulo $colorPorArticulo, $agotado = false){
	$return = '';
	$file = $colorPorArticulo->getRutaImagen(TiposRutas::imagenMiniatura);
	if ($agotado) {
		$return .= '<div class="agotado top">AGOTADO</div>';
	}
	$return .= '<img class="cPointer' . ($agotado ? ' notop' : '') . '" onclick="ampliarFoto($(this).data(\'rutaimagen\'))" src="' . $file . '" style="height: 70px; max-width: 130px;" data-rutaimagen="' . $colorPorArticulo->getRutaImagen(TiposRutas::imagenPrincipal) . '" ' . ManejadorDeImagenes::getImgOnErrorHtml() . ' />';

	return $return;
}

if ($idCat == null) {
	$lis = '';
	$divs = '';
	$categorias = Factory::getInstance()->getListObject('CategoriaCalzadoUsuario', '1 = 1 ORDER BY orden ASC');
	foreach($categorias as $categoria){
		$labelPar = in_array($categoria->id, array('A', 'I', 'P')) ? 'unidades' : 'pares';
        $lis .= '<li idCategoria="' . $categoria->id . '">' . $categoria->nombre . ' - ' . '<span id="spanCantidadPares_' . $categoria->id . '" class="spanCantidadPares">0</span> ' . $labelPar . '<br>Total: <span id="spanTotal_' . $categoria->id . '" class="spanTotal">0,00</span></li>';
		$divs .= '<div id="divCategoria_' . $categoria->id . '" idCategoria="' . $categoria->id . '"></div>';
	}
	$lis = '<ul>' . $lis . '</ul>';
	$divs = '<div>' . $divs . '</div>';
	echo $lis . $divs;
} else {
	$categorias = Factory::getInstance()->getListObject('CategoriaCalzadoUsuario', 'cod_categoria = ' . Datos::objectToDB($idCat));
	if (count($categorias) == 1) {
		$idCategoria = $categorias[0]->id;

		//Creo la tabla de la categoría
		$tabla = new HtmlTable(array('cantCols' => 4));
		$tabla->id = 'tablaCategoria_' . $idCategoria;
		$tabla->class = 'tablaCategoria w100p';
		$tabla->cellSpacing = 1;

		/* Agrego los títulos */ {
			$tabla->getHeadArray($heads);
			
			$heads[0]->style->width = '135px';
			$heads[1]->style->width = '470px';
			$heads[2]->style->width = '270px';
			$heads[3]->style->width = '60px';
		}

		$articulos = Factory::getInstance()->getListObject('ColorPorArticulo', 'categoria_usuario = ' . Datos::objectToDB($idCategoria) . ' AND vigente = ' . Datos::objectToDB('S') . ' AND naturaleza = ' . Datos::objectToDB('PT') . ' ORDER BY denom_articulo ASC, cod_articulo ASC, cod_color_articulo ASC');
		$stock = Stock::getStockMenosPendiente('01');
		foreach($articulos as $item){
			/** @var ColorPorArticulo $item */
			$pos = $item->articulo->rangoTalle->posicion;
			$idArticulo = $item->articulo->id;
			$idColor = $item->id;
			$idCombinado = $idArticulo . '_' . $idColor;
			$stockArticuloColor = Funciones::keyIsSet(Funciones::keyIsSet($stock, $idArticulo, array()), $idColor, array());

			//Creo la fila del artículo
			$row = new HtmlTableRow();
			$row->id = 'tr_' . $idCombinado;
			$row->class = 'tableRow';

			/* Agrego las celdas de la fila */ {
				$cell = new HtmlTableCell();
				$cell->id = 'foto_' . $idCombinado;
				$cell->class = 'aCenter';
				$cell->content = generarPathFoto($item, ($item->formaDeComercializacion == 'A'));
				$row->addCell($cell);
					
				$cell = new HtmlTableCell();
				$cell->id = 'tdInfo_' . $idCombinado;
				$cell->class = 'aLeft bBottomDarkGray';
				/* Creo el contenido de la info */ {
					$tablaInfo = new HtmlTable();
					$tablaInfo->class = 'w100p';
					$row1 = new HtmlTableRow();
					$row2 = new HtmlTableRow();
					$row3 = new HtmlTableRow();
					/* Row1 info */ {
						$cell11 = new HtmlTableCell();
						$cell11->colspan = 2;
						$cell11->class = 'bold';
						$cell11->content = '<label id="articulo_' . $idCombinado . '" class="idArticulo">' . $idArticulo . '</label> ';
						$cell11->content .= '<label id="color_' . $idCombinado . '" class="idColor" title="' . $item->nombre . '">' . $idColor . '</label> - ';
						$cell11->content .= '<label id="nombre_' . $idCombinado . '">' . $item->articulo->nombre . '</label>';
						$row1->addCell($cell11);
					}
					/* Row2 info */ {
						$cell21 = new HtmlTableCell();
						$cell21->class = 'w50p';
						$cell21->content = '<label id="precioPub_' . $idCombinado . '" class="precioN">' . Funciones::formatearMoneda($item->precioMinoristaDolar) . ' - </label>';
						$cell21->content .= '<label id="precioPub_' . $idCombinado . '" class="precioD hidden">' . Funciones::formatearMoneda($item->precioDistribuidorMinorista) . ' - </label>';
						$cell21->content .= '<label id="precioFac_' . $idCombinado . '">';
						$cell21->content .= '<label class="precioN">' . Funciones::formatearMoneda($item->precioMayoristaDolar) . '</label>';
						$cell21->content .= '<label class="precioD hidden">' . Funciones::formatearMoneda($item->precioDistribuidor) . '</label>';
						$cell21->content .= '</label>';
						$row2->addCell($cell21);
			
						$cell22 = new HtmlTableCell();
						$cell22->class = 'w50p';
						$cell22->content = '<label class="bold">Stock disp: </label><label id="stockDisp_' . $idCombinado . '"></label>';
						$row2->addCell($cell22);
					}
					/* Row3 info */ {
						$cell31 = new HtmlTableCell();
						$cell31->class = 'w50p';
						$cell31->content = '<label id="curva_' . $idCombinado . '" tipo="' . $item->formaDeComercializacion . '" ' . serializeLibre($item) . serializeCurva($item) . '>' . $item->formaDeComercializacionNombre . '</label>';
						$row3->addCell($cell31);
			
						$cell32 = new HtmlTableCell();
						$cell32->class = 'w50p';
						$cell32->content = '<label class="bold">Fecha disp: </label><label id="fechaDisp_' . $idCombinado . '"></label>';
						$row3->addCell($cell32);
					}
					$tablaInfo->addRow($row1);
					$tablaInfo->addRow($row2);
					$tablaInfo->addRow($row3);
				}
				$cell->content = $tablaInfo->toString();
				$row->addCell($cell);
	
				$cell = new HtmlTableCell();
				$cell->id = 'tdDetalle_' . $idCombinado;
				$cell->class = 'aCenter vaBottom table-cell bBottomDarkGray';
				/* Creo el contenido del detalle */ {
					$tablaDetalle = new HtmlTable();
					$tablaDetalle->id = 'tablaPosiciones_' . $idCombinado;
					$tablaDetalle->cellSpacing = 0;
					$tablaDetalle->class = 'w100p';
					$row1 = new HtmlTableRow();
					$row1->class = 'bDarkGray';
					$row2 = new HtmlTableRow();
					$row2->class = 'bGray';
					$row3 = new HtmlTableRow();
					/* Row1 detalle (talles) */ {
						for ($k = 1; $k <= 10; $k++) {
							$cell1 = new HtmlTableCell();
							$cell1->class = 'aCenter bold  bRightWhite pad white';
							$cell1->content = ($k <= 8 ? '<label id="talle_' . $k . '" class="talle">' . Funciones::keyIsSet($pos, $k, '---') . '</label>' : ($k == 9 ? 'Total' : 'Importe'));
							$row1->addCell($cell1);
						}
					}
					/* Row2 detalle (posiciones) */ {
						for ($k = 1; $k <= 10; $k++) {
							$cell1 = new HtmlTableCell();
							if ($k <= 8){
								$cell1->class = 'aCenter bRightWhite';
								$cell1->content = '<label>' . Funciones::toNatural(Funciones::keyIsSet($stockArticuloColor, $k, 0)) . '</label>';
							} elseif ($k == 9) {
								$cell1->class = 'aCenter bRightWhite';
								$cell1->content = '<label>' . Funciones::sumaArray($stockArticuloColor, true) . '</label>';
							} else {
								$cell1->class = 'aCenter';
								$cell1->content = '<label></label>';
							}
							$row2->addCell($cell1);
						}
					}
					/* Row3 detalle (posiciones) */ {
						for ($k = 1; $k <= 10; $k++) {
							$cell1 = new HtmlTableCell();
							if ($k <= 8){
								$cell1->class = 'aCenter bRightDarkGray';
								$cell1->content = '<label class="posicion posicion_' . $k . '"></label>';
							} elseif ($k == 9) {
								$cell1->class = 'aCenter bRightDarkGray';
								$cell1->content = '<label id="cantidad_' . $idCombinado . '" class="cantidadArt cantidad_' . $idCombinado . '">0</label>';
							} else {
								$cell1->class = 'aCenter';
								$cell1->content = '<label id="total_' . $idCombinado . '" class="totalArt total_' . $idCombinado . '">$ 0,00</label>';
							}
							$row3->addCell($cell1);
						}
					}
					$tablaDetalle->addRow($row1);
					$tablaDetalle->addRow($row2);
					$tablaDetalle->addRow($row3);
				}
				$cell->content = $tablaDetalle->toString();
				$row->addCell($cell);
	
				$cell = new HtmlTableCell();
				$cell->id = 'tdEditar_' . $idCombinado;
				$cell->class = 'aCenter';
				$cell->content = '<label class="underline blue cPointer" onclick="editarFila(\'' . $idCombinado . '\');">Editar</label><br>';
				$cell->content .= '<label class="underline blue cPointer" onclick="vaciarFila(\'' . $idCombinado . '\');">Vaciar</label>';
				$row->addCell($cell);
			}
			$tabla->addRow($row);
		}
		//echo $tabla->toString();
		Html::jsonSuccess('', array('idCategoria' => $idCategoria, 'html' => $tabla->toString()));
	}
}

?>
<?php } ?>