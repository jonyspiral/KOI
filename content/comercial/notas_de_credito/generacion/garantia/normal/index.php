<?php

?>

<style>
	.tabladinamica-header>tr>th, .tabladinamica-header>th {
		background-color: #B6B6BF !important;
	}
	.tableRow > td {
		font-size: 14px;
	}
	.trs:not(.active) {
		opacity: 0.6;
	}
	.trs:not(.active) .botonesGrandes {
		display: none;
	}
</style>

<script type='text/javascript'>
	$(document).ready(function(){
		tituloPrograma = 'Evaluación de garantías';
		$('.subtotal1_cantidad').livequery(function() {
			$(this).change(selectChange);
		});
		cambiarModo('buscar');
		buscar();
	});

	function limpiarScreen() {
		$('#tablaGarantias tbody').html('');

		$('#tablaDetalles tbody').html('');
		$('#tablaDetalles').hide();
	}

	function buscar() {
		funciones.limpiarScreen();
		funciones.buscar(funciones.controllerUrl('buscar'), llenarPantalla);
	}

	function llenarPantalla(json) {
		var table = $('#tablaGarantias tbody');
		for (var i = 0; i < json.length; i++) {
			table.append(returnTr(json[i]));
		}
	}

	function divDatos(o) {
		var cliente = 'CLIENTE: ' + o.cliente.razonSocial;
		var motivo =  (o.idMotivo ? ' - MOTIVO: ' + o.motivo.nombre : '');
		var table = $('<table>').attr('class', 'w100p').attr('border', '0').append($('<tbody>'));
		table.append(
			$('<tr>').addClass('tableRow').append(
				$('<td>').addClass('bold aLeft').append(
					$('<label>').text(cliente + motivo)
				)
			),
			$('<tr>').addClass('tableRow').append(
				$('<td>').addClass('aLeft').append(
					$('<label>').text('Fecha: ' + o.fechaAlta),
					$('<label>').addClass('fRight').text('Total: ' + o.cantidadPares + ' pares por ' + funciones.formatearMoneda(o.totalNcr))
				)
			)
		);
		return table;
	}

	function divDetalle(o) {
		var div = $('<div>').addClass('aCenter');
		btn = $('<a>').addClass('boton').attr('href', '#').attr('title', 'Ver detalle')
			.click($.proxy(function() {verDetalle(this);}, o))
			.append($('<img>').attr('src', '/img/botones/25/buscar.gif'));
		div.append(btn);
		btn = $('<a>').addClass('boton').attr('href', '#').attr('title', 'Imprimir detalle')
			.click($.proxy(function() {pdfClickGarantia(this);}, o))
			.append($('<img>').attr('src', '/img/botones/25/pdf.gif'));
		div.append(btn);
		return div;
	}

	function divBotones(o) {
		var div = $('<div>').addClass('aCenter botonesGrandes');
		btn = $('<a>').addClass('boton').attr('href', '#').attr('title', 'Aprobar Nota de Crédito')
			.click($.proxy(function() {aprobar(this);}, o))
			.append($('<img>').attr('src', '/img/botones/40/aceptar.gif'));
		div.append(btn);
		btn = $('<a>').addClass('boton').attr('href', '#').attr('title', 'Denegar Nota de Crédito')
			.click($.proxy(function() {denegar(this);}, o))
			.append($('<img>').attr('src', '/img/botones/40/cancelar.gif'));
		div.append(btn);
		return div;
	}

	function returnTr(o) {
		return $('<tr>').attr('id', 'tr_' + o.id).addClass('trs active').append(
			$('<td>').append(divDatos(o)),
			$('<td>').append(divDetalle(o)),
			$('<td>').append(divBotones(o))
		);
	}

	function verDetalle(o){
		$('.trs').removeClass('active');
		$('#tr_' + o.id).addClass('active');
		var table = $('#tablaDetalles tbody');
		table.html('');
		$.each(o.detalle, function(k, v) {
			var trh = $('<tr>').addClass('aCenter bDarkGray white'),
				trd = $('<tr>').addClass('aCenter');
			for (var i = 1; i <= 8; i++) {
				trh.append($('<th>').text(v.articulo.rangoTalle.posicion[i] ? v.articulo.rangoTalle.posicion[i] : '-'));
				trd.append($('<td>').text(v.articulo.rangoTalle.posicion[i] ? v.cantidad[i] : '-'));
			}
			var precioUnitario = v.importeNcr / v.cantidadTotal;
			var select = $('<select>').addClass('aCenter subtotal1_cantidad').attr('id', 'select_' + v.id).data('id', v.id).data('max', v.cantidadTotal).data('precioUnitario', precioUnitario);
			for (var j = 0; j <= v.cantidadTotal; j++) {
				select.append(
					$('<option>').attr('value', j).text(j)
				)
			}
			table.append(
				$('<tr>').attr('id', 'tr_detalle_' + v.id).addClass('tableRow').append(
					$('<td>').text('[' + v.articulo.id + '-' + v.colorPorArticulo.id + '] ' + v.articulo.nombre + v.colorPorArticulo.nombre),
					$('<td>').append(
						$('<table>').addClass('w100p').append(
							$('<thead>').append(trh),
							$('<tbody>').append(trd)
						)
					),
					$('<td>').addClass('aCenter').text(funciones.formatearMoneda(precioUnitario)),
					$('<td>').addClass('aCenter').append(
						$('<div>').append(
							select
						),
						$('<div>').append(
							$('<label>').addClass('aCenter bold subtotal1').attr('id', 'subtotal1_' + v.id).text(funciones.formatearMoneda(v.importeNcr))
						)
					),
					$('<td>').addClass('aCenter').append(
						$('<div>').append(
							$('<label>').addClass('aCenter subtotal2_cantidad').attr('id', 'subtotal2_cantidad_' + v.id).text(0)
						),
						$('<div>').append(
							$('<label>').addClass('aCenter bold subtotal2').attr('id', 'subtotal2_' + v.id).text(funciones.formatearMoneda(0))
						)
					)
				)
			);
			$('#select_' + v.id).val(j - 1);
		});
		$('#tablaDetalles').show();
		recalcularTotales();
	}

	function recalcularTotales() {
		var subtotal1 = 0, subtotal2 = 0;
		$('.subtotal1').each(function(k, v) {
			subtotal1 += funciones.toFloat(funciones.limpiarNumero($(v).text()));
		});
		$('.subtotal2').each(function(k, v) {
			subtotal2 += funciones.toFloat(funciones.limpiarNumero($(v).text()));
		});

		$('#tablaDetallesTotal1').text(funciones.formatearMoneda(subtotal1));
		$('#tablaDetallesTotal2').text(funciones.formatearMoneda(subtotal2));
	}

	function selectChange() {
		var id = $(this).data('id');
		var precioUnitario = $(this).data('precioUnitario');
		var cantidad1 = $(this).val();
		var cantidad2 = $(this).data('max') - cantidad1;
		$('#subtotal2_cantidad_' + id).text(cantidad2);
		$('#subtotal1_' + id).text(funciones.formatearMoneda(precioUnitario * cantidad1));
		$('#subtotal2_' + id).text(funciones.formatearMoneda(precioUnitario * cantidad2));

		recalcularTotales();
	}

	function aprobar(o){
		var cantidades = {};
		if ($('#tr_' + o.id).hasClass('active')) {
			$('.subtotal1_cantidad').each(function(k, v) {
				cantidades[$(v).data('id')] = $(v).val();
			});
		}
		var url = funciones.controllerUrl('agregar');
		funciones.guardar(url, {idGarantia: o.id, cantidades: cantidades});
	}

	function denegar(o){
		var msg = '¿Está seguro que desea denegar la Nota de Crédito por ' + funciones.formatearMoneda(o.totalNcr) + ' de la garantía Nº ' + o.id + '?';
		funciones.borrar(msg, funciones.controllerUrl('borrar'), {idGarantia: o.id});
	}

	function pdfClickGarantia(o) {
		var url = funciones.controllerUrl('getPdf', {idGarantia: o.id});
		funciones.pdfClick(url);
	}

	function cambiarModo(modo){
		funciones.cambiarModo(modo);
		switch (modo){
			case 'inicio':
				break;
			case 'buscar':
				funciones.cambiarTitulo(tituloPrograma);
				break;
			case 'editar':
				break;
			case 'agregar':
				break;
		}
	}
</script>

<div id='programaTitulo'></div>
<div id='programaContenido' class='customScroll'>
	<div id='divEvaluacionDeGarantias' class='w40p customScroll fLeft'>
		<table id="tablaGarantias" class="registrosAlternados w100p">
			<thead class='tableHeader'>
				<tr>
					<th class="w60p">Información de la garantía</th>
					<th class="w10p" title="Ver detalle de la garantía">Detalle</th>
					<th class="w20p" title="Aprobar o denegar garantía">Aprobar</th>
				</tr>
			</thead>
			<tbody></tbody>
		</table>
	</div>
	<div id='divDetallesGarantias' class='w60p customScroll fRight'>
		<table id="tablaDetalles" class="registrosAlternados w100p">
			<thead class="tabladinamica-header">
				<tr>
					<th class="w30p">Artículo</th>
					<th class="w34p">Cantidades</th>
					<th class="w12p" title="Precio unitario">P. Unit</th>
					<th class="w12p" title="Subtotal empresa 1">Subt. 1</th>
					<th class="w12p" title="Subtotal empresa 2">Subt. 2</th>
				</tr>
			</thead>
			<tbody></tbody>
			<tfoot>
				<tr>
					<td colspan="3"></td>
					<td id="tablaDetallesTotal1" class="aCenter bold bTop"></td>
					<td id="tablaDetallesTotal2" class="aCenter bold bTop"></td>
				</tr>
			</tfoot>
		</table>
	</div>
</div>
<div id='programaPie'>
	<div class='botonera'>
		<?php Html::echoBotonera(array('boton' => 'actualizar', 'accion' => 'buscar();')); ?>
	</div>
</div>
