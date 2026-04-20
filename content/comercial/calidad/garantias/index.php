<?php

?>

<style>
	#divGarantiasBuscar, #divGarantiasAgregar, #divGarantiasEditar {
		height: 490px;
	}

	.tabladinamica-header>tr>th, .tabladinamica-header>th {
		font: bold 14px Calibri !important;
		background-color: #B6B6BF !important;
		padding: 1px 0 !important;
	}
</style>

<script type='text/javascript'>
	var ecommerce = false,
		idGarantiaClasificando = false;

	$(document).ready(function(){
		tituloPrograma = 'Garantías';
		$('#tablaDinamicaAgregar').tablaDinamica(
			{
				width: '100%',
				height: 'auto',
				scrollbar: false,
				addButtonInHeader: true,
				buttons: ['Q'],
				columnsConfig: [
					{
						id: 'idArticulo',
						name: 'Articulo',
						width: '200px',
						css: {textAlign: 'center'},
						cellType: 'A',
						template: '<input class="textbox obligatorio autoSuggestBox w180" name="Articulo" />'
					},
					{
						id: 'idColor',
						name: 'Color',
						width: '140px',
						css: {textAlign: 'center'},
						cellType: 'A',
						template: '<input class="textbox obligatorio autoSuggestBox w120" name="ColorPorArticulo" />',
						focus: focusColor
					},
					{
						id: 'cantidades',
						name: 'Cantidades',
						width: 'auto',
						css: {textAlign: 'center'},
						cellType: 'G',
						getJson: function(o) {
							var i = 1,
								cantidades = {},
								inputsCantidades = o.tableCell.tablaDinamica('getSibling', 'cantidades').tableCell.find('input');

							inputsCantidades.each(
								function(k, v){
									cantidades[i++] = $(v).val();
								}
							);

							return cantidades;
						}
					},
					{
						id: 'total',
						name: 'Total',
						width: '50px',
						css: {textAlign: 'center'},
						cellType: 'L',
						template: '0'
					}/*, Se manda a un almacén por defecto ("Devoluciones a clientes")
					 {
					 id: 'idAlmacen',
					 name: 'Mover a...',
					 width: '140px',
					 css: {textAlign: 'center'},
					 cellType: 'A',
					 template: '<input class="textbox obligatorio autoSuggestBox w220" name="UsuarioPorAlmacen" alt="&idUsuario=<?php //echo Usuario::logueado()->id; ?>" />'
					 }*/
				],
				notEmpty: true
			}
		);
		$('#divGarantiasBuscar, #divGarantiasAgregar, #divGarantiasEditar').fixedHeader({target: 'table'});
		cambiarModo('buscar');
		buscar();
	});

	function limpiarScreen(){
		ecommerce = false;
		garantia = false;
		$('.tabladinamica').tablaDinamica('clean');
		$('#tablaEditar tbody').html('');
	}

	function buscar() {
		funciones.limpiarScreen();
		var url = funciones.controllerUrl('buscar'),
			msgError = 'Ocurrió un error al buscar las garantías pendientes',
			cbSuccess = function(json) {
				$('#tablaBuscarEcommerce tbody, #tablaBuscarGarantias tbody').html('');
				llenarTablaBuscar(json.ecommerce, 'tablaBuscarEcommerce', divDatosEcommerce, divBotonesEcommerce);
				llenarTablaBuscar(json.garantias, 'tablaBuscarGarantias', divDatosGarantias, divBotonesGarantias);
			};
		funciones.buscar(url, cbSuccess, msgError);
	}

	function llenarTablaBuscar(json, idTabla, fnDatos, fnBotones) {
		var body = $('#' + idTabla).find('tbody').eq(0);
		for (var i = 0; i < json.length; i++) {
			body.append(
				$('<tr>').attr('id', 'tr_' + json[i].id).append(
					$('<td>').append(fnDatos(json[i])),
					$('<td>').append(fnBotones(json[i]))
				)
			)
		}
	}

	function divDatosEcommerce(o) {
		var table = $('<table>').attr('class', 'w100p').attr('border', '0').append($('<tbody>'));
		table.append(
			$('<tr>').addClass('tableRow').append(
				$('<td>').addClass('bold aLeft').append(
					$('<label>').text('Nº ' + o.idEcommerce + ' - PERSONA: ' + o.customer.firstname + ' ' + o.customer.lastname)
				)
			),
			$('<tr>').addClass('tableRow').append(
				$('<td>').addClass('aLeft').append(
					$('<label>').text('Fecha: ' + o.fechaAlta + ' - Pares pedido: ' + o.cantidadPares),
					$('<label>').addClass('fRight').text('Tipo: ' + (o.idStatus == 10 ? 'CAMBIO' : 'DEVOLUCIÓN'))
				)
			)
		);
		return table;
	}

	function divDatosGarantias(o) {
		var cliente = (o.idOrder ? 'PERSONA: ' + o.order.customer.firstname + ' ' + o.order.customer.lastname : 'CLIENTE: ' + o.cliente.razonSocial);
		var observaciones =  (o.observaciones ? ' - OBSERVACIONES: ' + o.observaciones : '');
		var table = $('<table>').attr('class', 'w100p').attr('border', '0').append($('<tbody>'));
		table.append(
			$('<tr>').addClass('tableRow').append(
				$('<td>').addClass('bold aLeft').append(
					$('<label>').text(cliente + observaciones),
					$('<label>').addClass('fRight bold' + (o.movimientos ? ' indicador-amarillo' : ' indicador-verde')).text((o.movimientos ? 'Esperando confirmación' : 'Por clasificar')),
					$('<label>').addClass('fRight bold' + (o.derivada ? ' indicador-gris mRight5' : '')).text((o.derivada ? 'Derivada' : ''))
				)
			),
			$('<tr>').addClass('tableRow').append(
				$('<td>').addClass('aLeft').append(
					$('<label>').text('Fecha: ' + o.fechaAlta + (o.idOrder ? ' - ' : '')),
					$('<label>').addClass('bold red').text((o.idOrder ? 'Ecommerce Nº ' + o.order.idEcommerce : '')),
					$('<label>').addClass('fRight').text('Total: ' + o.cantidadPares + ' pares por ' + funciones.formatearMoneda(o.totalNcr))
				)
			)
		);
		return table;
	}

	function divBotonesEcommerce(o) {
		var btn;
		var div = $('<div>').addClass('aCenter');
		btn = $('<a>').addClass('boton').attr('href', '#').attr('title', 'Agregar garantía')
			.click($.proxy(function() {agregarGarantiaEcommerce(this);}, o))
			.append($('<img>').attr('src', '/img/botones/25/agregar.gif'));
		div.append(btn);
		return div;
	}

	function divBotonesGarantias(o) {
		var div = $('<div>').addClass('aCenter');
		if (!o.movimientos) {
			var btn = $('<a>').addClass('boton').attr('href', '#').attr('title', 'Clasificar garantía')
				.click($.proxy(function() {clasificarGarantia(this);}, o))
				.append($('<img>').attr('src', '/img/botones/40/editar.gif'));
			div.append(btn);
			if (!o.idOrder) {
				btn = $('<a>').addClass('boton').attr('href', '#').attr('title', 'Devolver al cliente')
					.click($.proxy(function() {devolverAlCliente(this);}, o))
					.append($('<img>').attr('src', '/img/botones/40/download.gif'));
				div.append(btn);
			}
			btn = $('<a>').addClass('boton').attr('href', '#').attr('title', 'Borrar garantía')
				.click($.proxy(function() {borrarGarantia(this);}, o))
				.append($('<img>').attr('src', '/img/botones/40/borrar.gif'));
			div.append(btn);
		} else {
			btn = $('<a>').addClass('boton').attr('href', '#').attr('title', 'Imprimir detalle')
				.click($.proxy(function() {pdfClickGarantia(this);}, o))
				.append($('<img>').attr('src', '/img/botones/40/pdf.gif'));
			div.append(btn);
		}
		return div;
	}

	function focusColor() {
		this.tablaDinamica('getMe').valueElement.val('').attr('alt', 'idArticulo=' + this.tablaDinamica('getSibling', 'idArticulo').getValue());

		var that = this,
			objCantidades = this.tablaDinamica('getSibling', 'cantidades'),
			idArticulo = this.tablaDinamica('getSibling', 'idArticulo').getValue();

		if (this.data('idArticulo') != idArticulo){
			if (idArticulo){
				$.postJSON(funciones.controllerUrl('getInfoArticulo', {idArticulo: idArticulo}), function(json){
					objCantidades.setValue('');
					var rango = json.data.rangoTalle,
						table = $('<table>').addClass('w100p'),
						thead = $('<thead>'),
						tbody = $('<tbody>'),
						trh = $('<tr>').addClass('bDarkGray aCenter bold bRightWhite white'),
						tri = $('<tr>');

					for (var i = 1; i <= 8; i++) {
						var input = $('<input class="textbox aCenter w25" type="text" validate="EnteroPositivo" />');
						input.blur(function(){
							var total = 0,
								obj = that.tablaDinamica('getSibling', 'cantidades').tableCell.find('input');

							obj.each(
								function(k, v){
									if ($(v).hasClass('talleValido')) {
										if ($.validateEnteroPositivo($(v))) {
											total += funciones.toInt($(v).val());
										}
									} else {
										$(v).val('');
									}
								});

							that.tablaDinamica('getSibling', 'total').setValue(total);
						});
						(rango[i]) ? input.addClass('talleValido') : input.disable();

						trh.append($('<th>').text(rango[i] ? rango[i] : '--'));
						tri.append($('<td>').append(input));
					}
					objCantidades.setValue(table.append(thead.append(trh), tbody.append(tri)));
				});
				this.data('idArticulo', idArticulo);
				this.tablaDinamica('getSibling', 'idArticulo').disable();
			}
		}
	}

	function hayErrorGuardar(){
		var items = $('#tablaDinamicaAgregar').tablaDinamica('getJson');
		if (items.length == 0)
			return 'Debe ingresar al menos un artículo en la garantía';
		return false;
	}

	function guardar(){
		var error = hayErrorGuardar();
		if (error) {
			$.error(error);
		} else {
			var div = '<div class="h100 vaMiddle table-cell aLeft p10">' +
					  '<table><tbody>' +
					  ((ecommerce === false) ? '<tr><td class="w100"><label for="inputCliente">Cliente:</label></td><td><input id="inputCliente" class="textbox obligatorio autoSuggestBox w190" name="Cliente" /></td></tr>' : '') +
					  '<tr><td class="w100"><label for="inputMotivo">Motivo:</label></td><td><input id="inputMotivo" class="textbox obligatorio autoSuggestBox w190" name="Motivo" alt="tipo=<?php echo Motivos::agregarGarantia; ?>" /></td></tr>' +
					  '<tr><td><label for="inputObservaciones">Observaciones:</label></td><td><textarea id="inputObservaciones" class="textbox w190"></textarea></td></tr>' +
					  '<input id="inputOrderId" class="hidden" rel="id" />' +
					  '</tbody></table>' +
					  '</div>';
			var botones = [{value: 'Guardar', action: function() {agregarGarantia();}}, {value: 'Cancelar', action: function(){$.jPopUp.close();}}];
			$.jPopUp.show(div, botones);
			$('#inputCliente').focus();
			$('#inputOrderId').val(ecommerce);
		}
	}

	function agregarGarantia() {
		var obj = armoObjetoGuardar();
		$.jPopUp.close();
		funciones.guardar(funciones.controllerUrl('agregar'), obj);
	}

	function armoObjetoGuardar(){
		return {
			orderId: $('#inputOrderId').val(),
			idCliente: $('#inputCliente_selectedValue').val(),
			idMotivo: $('#inputMotivo_selectedValue').val(),
			observaciones: $('#inputObservaciones').val(),
			items: $('#tablaDinamicaAgregar').tablaDinamica('getJson')
		};
	}

	function clasificarGarantia(garantia) {
		idGarantiaClasificando = garantia.id;
		cambiarModo('editar');
		var body = $('#tablaEditar').find('tbody').eq(0);
		for (var i = 0; i < garantia.detalle.length; i++) {
			var item = garantia.detalle[i];
			body.append(
				$('<tr>').addClass('tableRow').attr('id', 'tr_' + item.id).append(
					$('<td>').text('[' + item.almacen.id + '] ' + item.almacen.nombre),
					$('<td>').text('[' + item.articulo.id + '-' + item.colorPorArticulo.id + '] ' + item.articulo.nombre + ' - ' + item.colorPorArticulo.nombre),
					$('<td>').addClass('aCenter').text(item.cantidadTotal),
					$('<td>').attr('id', 'detalle_' + item.id).append(
						$('<table>').attr('id', 'tablaDinamica_detalle_' + item.id).addClass('tabladinamica registrosAlternados').data('itemid', item.id)
					),
					$('<td>').attr('id', 'clasificados_' + item.id).addClass('aCenter indicador-gris').text('0')
				)
			);

			var rango = item.articulo.rangoTalle.posicion;
			var columns = [];
			for (var j = 1; j <= 8; j++) {
				if (rango[j]) {
					columns.push(
						{
							id: j,
							name: rango[j] + '<br>| <span id="restante_' + item.id + '_' + j + '" class="restantes_' + item.id + '" data-maxcol="' + item.cantidad[j] + '">' + item.cantidad[j] + '</span> |',
							width: '55px',
							css: {textAlign: 'center'},
							cellType: 'I',
							template: '<input class="textbox aCenter w25 clasificados_' + item.id + '_' + j + '" type="text" validate="EnteroPositivo" data-itemid="' + item.id + '" data-posicion="' + j + '" />',
							blur: function() {
								var input = this.tablaDinamica('getMe').valueElement;
								var itemid = input.data('itemid');
								var pos = input.data('posicion');
								var span = $('#restante_' + itemid + '_' + pos);
								var restantes = span.data('maxcol');

								if (input.val() > restantes) {
									input.val('').focus();
								} else {
									$('.clasificados_' + itemid + '_' + pos).each(function(k, v) {
										restantes -= funciones.toInt($(v).val());
									});
									if (restantes < 0) {
										input.val('').focus();
									} else {
										span.text(restantes);

										var max_clasificar = 0,
											por_clasificar = 0;
										$('.restantes_' + itemid).each(function(k, v) {
											max_clasificar += funciones.toInt($(v).data('maxcol'));
											por_clasificar += funciones.toInt($(v).text());
										});
										//noinspection JSReferencingMutableVariableFromClosure
										$('#clasificados_' + itemid).text(max_clasificar - por_clasificar);

										//noinspection JSReferencingMutableVariableFromClosure
										(!por_clasificar) ? $('#clasificados_' + itemid).removeClass('indicador-gris').addClass('indicador-verde') : $('#clasificados_' + i).removeClass('indicador-verde').addClass('indicador-gris');
									}
								}
							}
						}
					);
				} else {
					columns.push(
						{
							id: j,
							name: '-',
							width: '55px',
							css: {textAlign: 'center'},
							cellType: 'G',
							template: ''
						}
					)
				}
			}
			columns.push({
							 id: 'idAlmacen',
							 name: 'Mover a...',
							 width: 'auto',
							 css: {textAlign: 'center'},
							 cellType: 'A',
							 template: '<input class="textbox obligatorio autoSuggestBox w80p" name="Almacen" />'
						 });

			$('#tablaDinamica_detalle_' + item.id).tablaDinamica(
				{
					width: '100%',
					height: 'auto',
					scrollbar: false,
					addButtonInHeader: true,
					buttons: ['Q'],
					columnsConfig: columns,
					notEmpty: true
				}
			);
		}
	}

	function clasificarClick() {
		var obj = {idGarantia: idGarantiaClasificando, items: []};
		$('#tablaEditar').find('.tabladinamica').each(function(k, v) {
			obj.items.push(
				{
					idItem: $(this).data('itemid'),
					detalle: $(v).tablaDinamica('getJson')
				}
			)
		});
		funciones.guardar(funciones.controllerUrl('editar'), obj);
	}

	function borrarGarantia(o) {
		funciones.borrar('¿Está seguro que desea borrar la garantía Nº ' + o.id + ' por ' + o.cantidadPares + ' pares?', funciones.controllerUrl('borrar'), {idGarantia: o.id})
	}

	function agregarGarantiaEcommerce(order) {
		cambiarModo('agregar');
		ecommerce = order.id;
	}

	function devolverAlCliente(o) {
		var div = '<div class="h100 vaMiddle table-cell aLeft p10">' +
				  '<table><tbody>' +
				  '<tr><td class="w100"><label for="inputSucursal">Sucursal:</label></td><td><input id="inputSucursal" class="textbox obligatorio autoSuggestBox w190" name="Sucursal" alt="idCliente=' + o.idCliente + '" /></td></tr>' +
				  '<tr><td><label for="inputObservaciones">Observaciones:</label></td><td><textarea id="inputObservaciones" class="textbox w190"></textarea></td></tr>' +
				  '<input id="inputId" class="hidden" />' +
				  '</tbody></table>' +
				  '</div>';
		var botones = [{value: 'Guardar', action: function() {goDevolverAlCliente();}}, {value: 'Cancelar', action: function(){$.jPopUp.close();}}];
		$.jPopUp.show(div, botones);
		$('#inputSucursal').focus();
		$('#inputId').val(o.id);
	}

	function goDevolverAlCliente() {
		var obj = {idGarantia: $('#inputId').val(), idSucursal: $('#inputSucursal_selectedValue').val(), observaciones: $('#inputObservaciones').val()};
		funciones.guardar(funciones.controllerUrl('devolver'), obj);
	}

	function pdfClickGarantia(o) {
		var url = funciones.controllerUrl('getPdf', {idGarantia: o.id});
		funciones.pdfClick(url);
	}

	function cambiarModo(modo){
		funciones.cambiarModo(modo);
		switch (modo){
			case 'inicio':
				cambiarModo('buscar');
				break;
			case 'buscar':
				limpiarScreen();
				funciones.cambiarTitulo(tituloPrograma);
				$('#btnAgregar').show();
				$('#btnClasificar').hide();
				$('#divGarantiasBuscar').show();
				$('#btnCancelarBuscar').hide();
				$('#divGarantiasAgregar').hide();
				$('#divGarantiasEditar').hide();
				break;
			case 'editar':
				$('#btnClasificar').show();
				$('#btnGuardar').hide();
				$('#divGarantiasEditar').show();
				$('#divGarantiasBuscar').hide();
				$('#divGarantiasAgregar').hide();
				break;
			case 'agregar':
				$('#divGarantiasAgregar').show();
				$('#btnClasificar').hide();
				$('#divGarantiasBuscar').hide();
				$('#divGarantiasEditar').hide();
				break;
		}
	}
</script>

<div id='programaTitulo'></div>
<div id='programaContenido'>
	<div id='divGarantiasBuscar' class='w100p'>
		<div class="fLeft customScroll w40p">
			<table id='tablaBuscarEcommerce' class='registrosAlternados w100p'>
				<thead class='tableHeader'>
					<tr>
						<th class="w90p">Devoluciones y cambios pendientes de Ecommerce</th>
						<th class="w10p"></th>
					</tr>
				</thead>
				<tbody></tbody>
			</table>
		</div>
		<div class="fRight customScroll w55p">
			<table id='tablaBuscarGarantias' class='registrosAlternados w100p'>
				<thead class='tableHeader'>
					<tr>
						<th class="w75p">Garantías pendientes de clasificación</th>
						<th class="w25p"></th>
					</tr>
				</thead>
				<tbody></tbody>
			</table>
		</div>
	</div>
	<div id='divGarantiasAgregar' class='w100p customScroll'>
		<table id='tablaDinamicaAgregar' class='tabladinamica registrosAlternados'></table>
	</div>
	<div id='divGarantiasEditar' class='w100p customScroll'>
		<table id='tablaEditar' class='registrosAlternados w100p'>
			<thead class='tableHeader'>
				<tr>
					<th class="w10p" title="Almacén actual">Alm. actual</th>
					<th class="w25p">Artículo</th>
					<th class="w5p">Pares</th>
					<th class="w55p">Detalle</th>
					<th class="w5p" title="Clasificados">Clasif.</th>
				</tr>
			</thead>
			<tbody></tbody>
		</table>
	</div>
</div>
<div id='programaPie'>
	<div class='botonera'>
		<?php Html::echoBotonera(array('boton' => 'buscar', 'accion' => 'funciones.buscarClick();')); ?>
		<?php Html::echoBotonera(array('boton' => 'agregar', 'accion' => 'funciones.agregarClick();', 'permiso' => 'comercial/calidad/garantias/agregar/')); ?>
		<?php Html::echoBotonera(array('boton' => 'guardar', 'accion' => 'funciones.guardarClick();')); ?>
		<?php Html::echoBotonera(array('boton' => 'aceptar', 'accion' => 'clasificarClick();', 'id' => 'btnClasificar', '')); ?>
		<?php Html::echoBotonera(array('boton' => 'cancelar', 'accion' => 'funciones.cancelarBuscarClick();', 'id' => 'btnCancelarBuscar')); ?>
		<?php Html::echoBotonera(array('boton' => 'cancelar', 'accion' => 'funciones.cancelarEditarClick();', 'id' => 'btnCancelarEditar')); ?>
	</div>
</div>