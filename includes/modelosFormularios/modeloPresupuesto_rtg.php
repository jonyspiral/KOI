<?php

function relativeTop($top, $pagina){
	return $top + $pagina * 1450;
}

$id = Funciones::post('form_id');
$fecha = explode('/', Funciones::post('form_fecha'));
$idProveedor = Funciones::post('form_proveedor_id');
$nombreProveedor = Funciones::post('form_proveedor_nombre');
$detalle = $_POST['form_detalle'];
$observaciones = Funciones::post('form_observaciones');
$j = 0;
$cantidadItems = count($detalle);
$restanCantidad = $cantidadItems;

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
	right: 40px;
}
.observaciones{
	width: 900px;
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
		if($cantidadItems < 24){
			$cantidadPaginas = 1;
		} else{
			$cantidadPaginas = 1 + Funciones::roundUp(($cantidadItems - 23) / 25);
		}

		for($i = 0; $i < $cantidadPaginas; $i++){
			$html .= '<div style="top: ' . relativeTop(40, $i) . 'px" class="spiralSa absolute textoSpiral">
				<?php echo Config::RAZON_RTG; ?>
				</div>

				<div style="top: ' . relativeTop(30, $i) . 'px" class="logo absolute">
				<img src="../../img/logos/logo_rtg.png" style="width: 190px; height: 190px">
				</div>

                <div style="top: ' . relativeTop(110, $i) . 'px" class="infoEmpresa absolute aLeft">
                    Herrera 1761<br/>
                    Ciudad Autónoma de Buenos Aires<br/>
                    Tel./Fax: 0810-362-7747
                </div>

				<div style="top: ' . relativeTop(75, $i) . 'px" class="titulo absolute">PEDIDO DE COTIZACIÓN</div>

				<div style="top: ' . relativeTop(105, $i) . 'px" class="nroPresupuesto absolute">Nº ' . Funciones::padLeft($id, 8, '0') . '</div>

				<div style="top: ' . relativeTop(170, $i) . 'px" class="numerosEmpresa absolute aRight">
					C.U.I.T Nº ' . Funciones::ponerGuionesAlCuit(Config::CUIT_RTG) . '<br/>
					ING. BRUTOS Nº ' . Funciones::ponerGuionesAlCuit(Config::CUIT_RTG) . '<br/>
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

			if($i == 0){
				$html .= '<div class="proveedor absolute s18 aLeft">
				<span class="bold">Proveedor: </span>[' . $idProveedor . '] ' . $nombreProveedor .
				'</div>

				<div class="observaciones absolute s18 aLeft">
					<span class="bold">Observaciones: </span>' . (is_null($observaciones) ? '-' : $observaciones) .
				'</div>';
			}

			$html .= '<div style="top: ' . relativeTop(($i == 0 ? 320 : 245), $i) . 'px" class="detalle absolute">
				<table class="oddTr w100p">
					<thead class="tableHead">
						<tr>
							<th>Mat. (cod. prov.)</th>
							<th>Mat. (cod. interno)</th>
							<th>F. entrega</th>
							<th>Cantidad</th>
						</tr>
					</thead>
					<tbody>';

			for($k = 0; $restanCantidad > 0 && $k < ($i == 0 ? 23 : 25) ; $restanCantidad--, $j++, $k++){
				$item = $detalle[$j];
				$proveedorMateriaPrima = Factory::getInstance()->getProveedorMateriaPrima($idProveedor, $item->colorMateriaPrima->material->id, $item->colorMateriaPrima->idColor);
				/** @var PresupuestoItem $item */
				$html .= '<tr>';
				$html .= '<td class="w10p s12 aCenter">' . ($proveedorMateriaPrima->codigoInterno ? $proveedorMateriaPrima->codigoInterno : '-') . '</td>';
				$html .= '<td class="w25p s12 aLeft">' . $proveedorMateriaPrima->codigoPropio . '</td>';
				$html .= '<td class="w10p s13 aCenter">'. $item->fechaEntrega . '</td>';
				$html .= '<td class="w55p aCenter">';

				if($item->colorMateriaPrima->material->usaRango()){
					$head = '<tr>';
					$rowCantidad = '<tr>';
					for($l = 1; $l < 11; $l++){
						$talle = $item->colorMateriaPrima->material->rango->posicion[$l];
						$head .= '<th class=" s12">' . (empty($talle) ? '---' : $talle) . '</th>';
						$rowCantidad .= '<td class="w10p aCenter s12">' . $item->cantidades[$l] . '</td>';
					}
					$head .= '</th>';
					$rowCantidad .= '</tr>';

					$html .= '<table class="w100p"><thead class="tableHead">' . $head . '</thead><tbody>' . $rowCantidad . '</tbody></table>';
				} else{
					$html .= Funciones::formatearDecimales($item->cantidad, 4) . ($item->material->unidadDeMedidaCompra->id ? ' [' . $item->material->unidadDeMedidaCompra->nombre . ']' : '');
				}

				$html .= '</td></tr>';
			}

			$html .= '</tbody></table></div>';
			$html .= '<div style="top: ' . relativeTop(1390, $i) . 'px" class="pagina absolute s22"><span class="bold">Página ' . ($i + 1) . ' de ' . $cantidadPaginas . '</span></div>';
		}

		echo $html;
	?>
</body>
</html>