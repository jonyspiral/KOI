<?php

function relativeTop($top, $pagina) {
	return $top + $pagina * 1450;
}

$id = $_POST['form_id'];
$cliente = $_POST['form_cliente'];
$order = $_POST['form_order'];
$fecha = explode('/', $_POST['form_fecha']);
$detalle = $_POST['form_detalle'];
$cantidadItems = count($detalle);
$restanCantidad = $cantidadItems;
$motivo = $_POST['form_motivo'];
$observaciones = $_POST['form_observaciones'];
$esEcommerce = !$cliente->id;
$j = 0;

/**
 * @var Cliente         	$cliente
 * @var Ecommerce_Order 	$order
 */

?>
<head>
<link href='/css/styles.css' rel='stylesheet' type='text/css' />
<style>
.fuenteArial{
	font-family: Arial,serif;
}
.oddTr>tbody>tr:nth-child(odd) {
	background-color: #CCCCCC;
}
.razonSocial {
	font-weight: bold;
	font-size: 1.5em;
	left: 30px;
	top: 20px;
}
.observaciones {
	width: 1000px;
	height: 42px;
	top: 110px;
	left: 30px;
}
.motivo {
	top: 90px;
	left: 30px;
}
.garantiaTitulo {
	font-weight: bold;
	font-size: 1.5em;
	top: 20px;
	right: 30px;
}
.dia {
	font-size: 1.5em;
	top: 50px;
	right: 130px;
}
.mes {
	font-size: 1.5em;
	top: 50px;
	right: 95px;
}
.anio {
	font-size: 1.5em;
	top: 50px;
	right: 30px;
}
.detalle {
	left: 25px;
}
.tableHead {
	background-color: black;
	color: white;
}
.pagina {
	right: 40px;
}
</style>
</head>
<body>
	<?php
		$html = '';
		if($cantidadItems < 8) {
			$cantidadPaginas = 1;
		} else {
			$cantidadPaginas = 1 + Funciones::roundUp(($cantidadItems - 8) / 10);
		}

	if ($esEcommerce) {
		$entidad = '[' . $order->customer->id . '] ' . $order->customer->fullname() . ' (E-commerce)';
	} else {
		$entidad = $cliente->getIdNombre();
	}

	$html .=
		'<div class="razonSocial absolute">' . $entidad . '</div>

		<div class="motivo absolute s18 aLeft">
		<span class="bold">Motivo: </span>' . (is_null($motivo) ? '-' : $motivo) . '</div>

		<div class="observaciones absolute s18 aLeft">
		<span class="bold">Observaciones: </span>' . (is_null($observaciones) ? '-' : $observaciones) . '</div>

		<div class="garantiaTitulo absolute">GARANTÍA Nº ' . $id . '</div>

		<div class="fuenteArial">
			<div class="dia absolute">' . $fecha[0] .'</div>
			<div class="mes absolute">' . $fecha[1] . '</div>
			<div class="anio absolute">' . $fecha[2] .'</div>
		</div>';

	for($i = 0; $i < $cantidadPaginas; $i++) {
		$html .= '<div style="top: ' . relativeTop(($i == 0 ? 160 : 50), $i) . 'px" class="detalle absolute">';
		for ($k = 0; $restanCantidad > 0 && $k < ($i == 0 ? 8 : 10) ; $restanCantidad--, $j++, $k++) {
			/** @var Predespacho $item */
			$item = $detalle[$j];
			$cantidades = $item['cantidad'];

			$colorPorArticulo = Factory::getInstance()->getColorPorArticulo($item['idArticulo'], $item['idColorPorArticulo']);
			$articulo = $colorPorArticulo->articulo;
			$almacen = Factory::getInstance()->getAlmacen($item['idAlmacenDestino']);

			$html .= '<table class="oddTr" style="width: 97%">
					<caption class="aLeft s20">' . $colorPorArticulo->articulo->getIdNombre() . '</caption>
					<thead class="tableHead">
						<tr>
							<th>Color</th>
							<th>Almacen</th>
							<th>Cantidad</th>
						</tr>
					</thead>
					<tbody>';

			$html .= '<tr>';
			$html .= '<td class="w5p s20 aCenter">'. $colorPorArticulo->id . '</td>';
			$html .= '<td class="w15p s16 aCenter">'. $almacen->getIdNombre() . '</td>';
			$html .= '<td class="w80p aCenter">';

			$head = '<tr>';
			$rowCantidad = '<tr>';
			$total = 0;
			for ($l = 1; !empty($articulo->rangoTalle->posicion[$l]); $l++) {
				$talle = $articulo->rangoTalle->posicion[$l];
				$head .= '<th class="w10p s16">' . $talle . '</th>';
				$rowCantidad .= '<td class="bAll bBottom bLeft bBottom aCenter s22">' . $cantidades[$l] . '</td>';
				$total += $cantidades[$l];
			}
			$head .= '<th class="w10p s16">Total</th>';
			$rowCantidad .= '<td class="bAll bBottom bLeft bBottom bRight aCenter s22">' . $total . '</td>';

			$head .= '</th>';
			$rowCantidad .= '</tr>';

			$html .= '<table class="w100p"><thead class="tableHead">' . $head . '</thead><tbody>' . $rowCantidad . '</tbody></table>';

			$html .= '</tbody></table>';
		}

		$html .= '</div>';
		$html .= '<div style="top: ' . relativeTop(1390, $i) . 'px" class="pagina absolute s22"><span class="bold">Página ' . ($i + 1) . ' de ' . $cantidadPaginas . '</span></div>';
	}

	echo $html;
	?>
</body>
</html>