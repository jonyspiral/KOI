<?php

?>

<style>
	.tabladinamica-header>tr>th, .tabladinamica-header>th {
		background-color: #B6B6BF !important;
	}
	.tableRow > td {
		font-size: 14px;
	}
</style>

<script type='text/javascript'>
	$(document).ready(function(){
		tituloPrograma = 'Evaluación de garantías de ecommerce';
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
		var cliente = 'PERSONA: ' + o.order.customer.firstname + ' ' + o.order.customer.lastname;
		var motivo =  (o.idMotivo ? ' - MOTIVO: ' + o.motivo.nombre : '');
		var table = $('<table>').attr('class', 'w100p').attr('border', '0').append($('<tbody>'));
		table.append(
			$('<tr>').addClass('tableRow').append(
				$('<td>').addClass('bold aLeft').append(
					$('<label>').text(cliente + motivo),
					$('<label>').addClass('fRight bold red').text('Ecommerce Nº ' + o.order.idEcommerce)
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
		var div = $('<div>').addClass('aCenter');
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
		return $('<tr>').attr('id', 'tr_' + o.id).append(
			$('<td>').append(divDatos(o)),
			$('<td>').append(divDetalle(o)),
			$('<td>').append(divBotones(o))
		);
	}

	function verDetalle(o){
		var table = $('#tablaDetalles tbody');
		table.html('');
		$.each(o.detalle, function(k, v) {
			var trh = $('<tr>').addClass('aCenter bDarkGray white'),
				trd = $('<tr>').addClass('aCenter');
			for (var i = 1; i <= 8; i++) {
				trh.append($('<th>').text(v.articulo.rangoTalle.posicion[i] ? v.articulo.rangoTalle.posicion[i] : '-'));
				trd.append($('<td>').text(v.articulo.rangoTalle.posicion[i] ? v.cantidad[i] : '-'));
			}
			table.append(
				$('<tr>').attr('id', 'tr_' + o.id).addClass('tableRow').append(
					$('<td>').text('[' + v.articulo.id + '-' + v.colorPorArticulo.id + '] ' + v.articulo.nombre + v.colorPorArticulo.nombre),
					$('<td>').append(
						$('<table>').addClass('w100p').append(
							$('<thead>').append(trh),
							$('<tbody>').append(trd)
						)
					),
					$('<td>').addClass('aCenter').append(funciones.formatearMoneda(v.importeNcr / v.cantidadTotal)),
					$('<td>').addClass('aCenter').append(funciones.formatearMoneda(v.importeNcr))
				)
			);
		});
		$('#tablaDetalles').show();
		$('#tablaDetallesTotal').text(funciones.formatearMoneda(o.totalNcr));
	}

	function aprobar(o){
		var url = funciones.controllerUrl('agregar');
		funciones.guardar(url, {idGarantia: o.id});
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
	<div id='divEvaluacionDeGarantias' class='w60p customScroll fLeft'>
		<table id="tablaGarantias" class="registrosAlternados w100p">
			<thead class='tableHeader'>
				<tr>
					<th class="w70p">Información de la garantía</th>
					<th class="w10p" title="Ver detalle de la garantía">Detalle</th>
					<th class="w20p" title="Aprobar o denegar garantía">Aprobar</th>
				</tr>
			</thead>
			<tbody></tbody>
		</table>
	</div>
	<div id='divDetallesGarantias' class='w40p customScroll fRight'>
		<table id="tablaDetalles" class="registrosAlternados w100p">
			<thead class="tabladinamica-header">
				<tr>
					<th class="w35p">Artículo</th>
					<th class="w38p">Cantidades</th>
					<th class="w12p" title="Precio unitario">P. Unit</th>
					<th class="w15p" title="Subtotal de la linea">Subtotal</th>
				</tr>
			</thead>
			<tbody></tbody>
			<tfoot>
				<tr>
					<td colspan="3"></td>
					<td id="tablaDetallesTotal" class="aCenter bold bTop"></td>
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
