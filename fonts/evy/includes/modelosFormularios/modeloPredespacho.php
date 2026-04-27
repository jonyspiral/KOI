<?php

function relativeTop($top, $pagina) {
	return $top + $pagina * 1450;
}

$idCliente = $_POST['form_id_cliente'];
$idSucursal = $_POST['form_id_sucursal'];
$predespachos = $_POST['form_detalle'];
$esPedido = $_POST['form_es_pedido'];
$idPedido = $_POST['form_id_pedido'];
$cantidadItems = count($predespachos);
$restanCantidad = $cantidadItems;
$fecha = explode('/', Funciones::Hoy());
$montoTotalPredespachado = 0;
$j = 0;

?>
<head>
<link href='/css/styles.css' rel='stylesheet' type='text/css' />
<style>
.fuenteArial{
	font-family: Arial,serif;
}
.oddTr>tbody>tr:nth-child(odd) {
	background-color: #f5f5f5;
}
.razonSocial {
	font-weight: bold;
	font-size: 1.5em;
	left: 30px;
	top: 20px;
}
.nombreSucursal {
	font-size: 1.5em;
	left: 30px;
	top: 50px;
}
.saldoCliente {
	font-size: 1.5em;
	left: 30px;
	top: 80px;
}
.predespachoTitulo {
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
.predespachoMonto {
	font-size: 1.5em;
	top: 80px;
	right: 30px;
}
.vendedor {
	top: 125px;
	left: 50px;
}
.transporte {
	top: 145px;
	left: 50px;
}
.primeraEntrega {
	top: 125px;
	right: 50px;
}
.totalPredespacho {
	font-size: 20px;
	width: 45px;
	height: 35px;
	top: 145px;
	right: 100px;
	border-style: solid;
}
.totalPredespachoLabel {
	top: 145px;
	right: 155px;
}
.totalPredespachoCompletar {
	width: 45px;
	height: 35px;
	top: 145px;
	right: 50px;
	border-style: solid;
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
		if ($esPedido) {
			$pedido = Factory::getInstance()->getPedido($idPedido);
			$cliente = $pedido->cliente;
			$sucursal = Factory::getInstance()->getSucursal($pedido->idCliente, $pedido->idSucursal);
		} else {
			$sucursal = Factory::getInstance()->getSucursal($idCliente, $idSucursal);
			$cliente = $sucursal->cliente;

			$cliente->id;
			$cliente->razonSocial;
			$cliente->calificacion;
			$saldo = 0;
			$cc = Factory::getInstance()->getCuentaCorrienteHistorica($cliente->id);
			foreach ($cc->documentosPorFecha as $item) {
				/** @var CuentaCorrienteHistoricaDocumento $item */
				$saldo += Funciones::toFloat($item->importeTotal);
			}
		}

		$html = '';
		if($cantidadItems < 8) {
			$cantidadPaginas = 1;
		} else {
			$cantidadPaginas = 1 + Funciones::roundUp(($cantidadItems - 8) / 9);
		}

	$html .=
		'<div class="razonSocial absolute">' . $cliente->getIdNombre() . ' (Calificación: ' . $cliente->calificacion . ')</div>
		<div class="nombreSucursal absolute">[' . $sucursal->id . '] ' . $sucursal->nombre . '</div>

		<div class="saldoCliente absolute">' . ($esPedido ? 'Pedido: ' . $pedido->numero : 'Saldo Cta. Cte.: ' . Funciones::formatearMoneda($saldo)) . '</div>

		<div class="absolute vendedor">Vendedor: ' . ($cliente->vendedor->nombreApellido ? $cliente->vendedor->nombreApellido : '') . '</div>
		<div class="absolute transporte">Transporte: ' . ($sucursal->transporte->nombre ? $sucursal->transporte->nombre .
		' (' . $sucursal->transporte->armarDireccion() . ($sucursal->transporte->telefono ? ' - ' . $sucursal->transporte->telefono . '' : '') . ')' : '-') . '</div>

		<div class="absolute primeraEntrega">Primera entrega: ' . $cliente->creditoPrimeraEntrega . '</div>
		<div class="absolute totalPredespachoLabel">Total sucursales: </div><div class="totalPredespacho aCenter absolute">' . $cliente->totalAPredespachar . '</div>
		<div class="totalPredespachoCompletar absolute"></div>

		<div class="predespachoTitulo absolute">PRE-DESPACHO</div>

		<div class="fuenteArial">
			<div class="dia absolute">' . $fecha[0] .'</div>
			<div class="mes absolute">' . $fecha[1] . '</div>
			<div class="anio absolute">' . $fecha[2] .'</div>
		</div>';

	for($i = 0; $i < $cantidadPaginas; $i++) {
		$html .= '<div style="top: ' . relativeTop(($i == 0 ? 180 : 50), $i) . 'px" class="detalle absolute">';
		for ($k = 0; $restanCantidad > 0 && $k < ($i == 0 ? 8 : 9) ; $restanCantidad--, $j++, $k++) {
			/** @var Predespacho $item */
			$item = $predespachos[$j];

			$html .= '<table class="oddTr" style="width: 97%">
					<caption class="aLeft s20">' . $item->articulo->getIdNombre() . '</caption>
					<thead class="tableHead">
						<tr>
							<th>Color</th>
							<th>Cantidad</th>
							<th>Pedido</th>
						</tr>
					</thead>
					<tbody>';

			$html .= '<tr>';
			$html .= '<td class="w5p s12 aCenter">'. $item->colorPorArticulo->id . '</td>';
			$html .= '<td class="w80p aCenter">';

			$head = '<tr>';
			$rowCantidad = '<tr>';
			$rowStockYPendiente = '<tr>';
			for ($l = 1; !empty($item->articulo->rangoTalle->posicion[$l]); $l++) {
				$stock = $item->colorPorArticulo->getStockAlmacen($item->pedido->almacen->id);
				$pendiente = $item->pedidoItem->pendiente;
				$talle = $item->articulo->rangoTalle->posicion[$l];
				$head .= '<th class="w9p s16">' . $talle . '</th>';
				$rowCantidad .= '<td class="bAll bBottom bLeft bBottom aCenter s22">' . $item->predespachados[$l] . '</td>';
				$rowStockYPendiente .= '<td class="bAll bBottom bLeft bBottom aCenter s12"><table class="w100p"><tr><td class="aRight w50p bRight">' . $stock[$l] . '</td><td class="aRight w50p">' . $pendiente[$l] . '</td></tr></table></td>';
			}
			$head .= '<th class="w10p s16">T. pos.</th>';
			$rowCantidad .= '<td class="bAll bBottom bLeft bBottom bRight aCenter s22">' . $item->getTotalPredespachados() . '</td>';
			$rowStockYPendiente .= '<td class="bAll bBottom bLeft bBottom bRight aCenter s12"><table class="w100p"><tr><td class="aRight w50p bRight">' . Funciones::sumaArray($stock) . '</td><td class="aRight w50p">' . Funciones::sumaArray($pendiente) . '</td></tr></table></td>';
			$montoTotalPredespachado += $item->pedidoItem->precioUnitario * $item->getTotalPredespachados();

			$head .= '</th>';
			$rowCantidad .= '</tr>';

			$html .= '<table class="w100p"><thead class="tableHead">' . $head . '</thead><tbody>' . $rowStockYPendiente . $rowCantidad . '</tbody></table>';
			$html .= '</td>';
			$html .= '<td class="w15p aCenter s13">' . $item->pedidoItem->numero . ' (' . $item->pedidoItem->fechaAlta . ')</td>';
			$html .= '</tr>';

			$html .= '</tbody></table>';
		}

		$html .= '</div>';
		$html .= '<div style="top: ' . relativeTop(1390, $i) . 'px" class="pagina absolute s22"><span class="bold">Página ' . ($i + 1) . ' de ' . $cantidadPaginas . '</span></div>';
	}

	$html .= '<div class="predespachoMonto absolute">A predespachar: ' . Funciones::formatearMoneda($montoTotalPredespachado) . '</div>';

	echo $html;
	?>
</body>
</html>