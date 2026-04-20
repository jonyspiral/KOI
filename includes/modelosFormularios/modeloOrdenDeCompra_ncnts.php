<?php

function relativeTop($top, $pagina){
	return $top + $pagina * 1450;
}

$id = Funciones::post('form_id');
$fecha = explode('/', Funciones::post('form_fecha'));
$idProveedor = Funciones::post('form_proveedor_id');
$nombreProveedor = Funciones::post('form_proveedor_nombre');
$telefono = Funciones::post('form_telefono');
$detalle = $_POST['form_detalle'];
$montoTotal = Funciones::post('form_monto_total');
$observaciones = Funciones::post('form_observaciones');
$j = 0;
$cantidadItems = count($detalle);
$restanCantidad = $cantidadItems;
$arrayImpuestos = array();

?>
<head>
<link rel="styleheet" type="text/css" href="../../../../css/styles.css" media="screen"/>
<style>
.fuenteArial{
	font-family: Arial,serif;
}
.oddTr>tbody>tr:nth-child(odd){
	background-color: #CCCCCC;
}
.spiralSa{
	left: 220px;
}
.textoSpiral{
	font-size: 1.5em;
	font-weight: bold;
}
.logo{
	left: 20px;
}
.infoEmpresa{
	font-size: 0.85em;
	left: 220px;
	line-height:20px;
}
.titulo{
	font-weight: bold;
	font-size: 1.5em;
	left: 510px;
}
.nroPresupuesto{
	font-size: 1.5em;
	left: 510px;
}
.fecha{
	font-weight: bold;
	font-size: 1.5em;
	right: 78px;
}
.dia{
	font-size: 1.5em;
	right: 120px;
}
.mes{
	font-size: 1.5em;
	right: 85px;
}
.anio{
	font-size: 1.5em;
	right: 20px;
}
.numerosEmpresa{
	font-size: 0.85em;
	right: 20px;
}
.division1{
	width: 100%;
	height: 2px;
	background-color: black;
}
.proveedor{
	width: 900px;
	height: 42px;
	top: 245px;
	left: 50px;
}
.total{
	top: 245px;
	right: 200px;
}
.totalDetalle{
	top: 245px;
	right: 50px;
}
.observaciones{
	width: 650px;
	height: 42px;
	top: 270px;
	left: 50px;
}
.detalle{
	left: 25px;
}
.tableHead{
	background-color: black;
	color: white;
}
.pagina{
	right: 40px;
}
</style>
</head>
<body>
	<?php
		$html = '';
		if($cantidadItems < 10){
			$cantidadPaginas = 1;
		} else{
			$cantidadPaginas = 1 + Funciones::roundUp(($cantidadItems - 10) / 11);
		}

		for($i = 0; $i < $cantidadPaginas; $i++){
			$html .= '<div style="top: ' . relativeTop(40, $i) . 'px" class="spiralSa absolute textoSpiral">
				<?php echo Config::RAZON_NCNTS; ?>
				</div>

				<div style="top: ' . relativeTop(30, $i) . 'px" class="logo absolute">
				<img src="../../img/logos/logo_ncnts.png" style="width: 190px; height: 190px">
				</div>

                <div style="top: ' . relativeTop(110, $i) . 'px" class="infoEmpresa absolute aLeft">
                    Herrera 1761<br/>
                    Ciudad Autónoma de Buenos Aires<br/>
                    Tel./Fax: 0810-362-7747
                </div>

				<div style="top: ' . relativeTop(75, $i) . 'px" class="titulo absolute">ORDEN DE COMPRA</div>

				<div style="top: ' . relativeTop(105, $i) . 'px" class="nroPresupuesto absolute">Nº ' . Funciones::padLeft($id, 8, '0') . '</div>

				<div style="top: ' . relativeTop(170, $i) . 'px" class="numerosEmpresa absolute aRight">
					C.U.I.T Nº ' . Funciones::ponerGuionesAlCuit(Config::CUIT_NCNTS) . '<br/>
					ING. BRUTOS Nº ' . Funciones::ponerGuionesAlCuit(Config::CUIT_NCNTS) . '<br/>
					INICIO DE ACTIVIDADES 02/2018
				</div>

				<div style="top: ' . relativeTop(75, $i) . 'px" class="fecha absolute">
					FECHA
				</div>

				<div class="fuenteArial">
					<div style="top: ' . relativeTop(105, $i) . 'px" class="dia absolute">' . $fecha[0] .'</div>
					<div style="top: ' . relativeTop(105, $i) . 'px" class="mes absolute">' . $fecha[1] . '</div>
					<div style="top: ' . relativeTop(105, $i) . 'px" class="anio absolute">' . $fecha[2] .'</div>
				</div>

				<div style="top: ' . relativeTop(228, $i) . 'px" class="division1 absolute">
				</div>';

			$html .= '<div style="top: ' . relativeTop(($i == 0 ? 355 : 245), $i) . 'px" class="detalle absolute">
				<table class="oddTr">
					<thead class="tableHead">
						<tr>
							<th>Mat. (cod. prov.)</th>
							<th>Mat. (cod. interno)</th>
							<th>F. entrega</th>
							<th>Cantidad</th>
							<th>Impuesto</th>
							<th>Total</th>
						</tr>
					</thead>
					<tbody>';

			for($k = 0; $restanCantidad > 0 && $k < ($i == 0 ? 10 : 11) ; $restanCantidad--, $j++, $k++){
				$item = $detalle[$j];
				$proveedorMateriaPrima = Factory::getInstance()->getProveedorMateriaPrima($idProveedor, $item->colorMateriaPrima->material->id, $item->colorMateriaPrima->idColor);
				/** @var OrdenDeCompraItem $item */
				$html .= '<tr>';
				$html .= '<td class="w10p s12 aCenter">' . ($proveedorMateriaPrima->codigoInterno ? $proveedorMateriaPrima->codigoInterno : '-') . '</td>';
				$html .= '<td class="w25p s12 aLeft">' . $proveedorMateriaPrima->codigoPropio . '</td>';
				$html .= '<td class="w9p s13 aCenter">'. $item->fechaEntrega . '</td>';
				$html .= '<td class="w40p s14 aCenter">';

				if($item->colorMateriaPrima->material->usaRango()){
					$head = '<tr>';
					$rowCantidad = '<tr>';
					$rowPrecio = '<tr>';
					for($l = 1; $l < 11; $l++){
						$talle = $item->colorMateriaPrima->material->rango->posicion[$l];
						$head .= '<th class="w10p s12">' . (empty($talle) ? '---' : $talle) . '</th>';
						$rowCantidad .= '<td class="aCenter s12">' . $item->cantidades[$l] . '</td>';
						$rowPrecio .= '<td class="aCenter s12">' . (empty($item->precios[$l]) ? '' : Funciones::formatearMoneda($item->precios[$l]/(1 + ($item->impuesto->porcentaje/100)))) . '</td>';
					}
					$head .= '</th>';
					$rowCantidad .= '</tr>';
					$rowPrecio .= '</tr>';

					$html .= '<table class="w100p"><thead class="tableHead">' . $head . '</thead><tbody>' . $rowCantidad . $rowPrecio . '</tbody></table>';
				} else{
					$html .= '<span class="bold">Cantidad: </span>' . Funciones::formatearDecimales($item->cantidad, 4) . ($item->material->unidadDeMedidaCompra->id ? ' [' . $item->material->unidadDeMedidaCompra->nombre . ']' : '');
					$html .= '<span class="bold"> - Precio unitario: </span>' . Funciones::formatearMoneda($item->precioUnitario/(1 + ($item->impuesto->porcentaje/100)));
				}

				if($item->impuesto->id){
					$arrayImpuestos[$item->impuesto->id]['importe'] += $item->importeImpuesto;
					$arrayImpuestos[$item->impuesto->id]['nombre'] = $item->impuesto->nombre;
					$tdImpuesto = Funciones::toLower($item->impuesto->nombre) . '<br>' . Funciones::formatearMoneda($item->importeImpuesto);
				}else {
					$tdImpuesto = 's/ impuestos';
				}

				$html .= '</td>';
				$html .= '<td class="w8p aCenter s13">' . $tdImpuesto . '</td>';
				$html .= '<td class="w8p aCenter s13">' . Funciones::formatearMoneda($item->importe) . '</td>';
				$html .= '</tr>';
			}

			$html .= '</tbody></table></div>';
			$html .= '<div style="top: ' . relativeTop(1390, $i) . 'px" class="pagina absolute s22"><span class="bold">Página ' . ($i + 1) . ' de ' . $cantidadPaginas . '</span></div>';
		}

		$totalImpuestos = 0;
		$htmlImpuestosTotalesTitulo = '<br>';
		$htmlImpuestosTotalesContenido = '<br>';
		foreach($arrayImpuestos as $impuesto){
			$htmlImpuestosTotalesTitulo .= '<span class="bold">' . $impuesto['nombre'] . ': </span><br>';
			$htmlImpuestosTotalesContenido .= '<span>' . Funciones::formatearMoneda($impuesto['importe']) . '</span><br>';
			$totalImpuestos += $impuesto['importe'];
		}

		$subtotal = $montoTotal - $totalImpuestos;

		$html .= '<div class="proveedor absolute s18 aLeft">
					<span class="bold">Proveedor: </span>[' . $idProveedor . '] ' . $nombreProveedor . (empty($telefono) ? '' : ' (' . $telefono . ')') .
				 '</div>

				<div class="total absolute aLeft s18">
					<span class="bold">Subtotal: </span>' .
					$htmlImpuestosTotalesTitulo .
					'<span class="bold">Total: </span>' .
				'</div>

				<div class="totalDetalle absolute aRight s18">'
					. Funciones::formatearMoneda($subtotal) . $htmlImpuestosTotalesContenido . Funciones::formatearMoneda($montoTotal) .
				'</div>

				<div class="observaciones absolute s18 aLeft">
					<span class="bold">Observaciones: </span>' . (is_null($observaciones) ? '-' : $observaciones) .
				'</div>';
		echo $html;
	?>
</body>
</html>