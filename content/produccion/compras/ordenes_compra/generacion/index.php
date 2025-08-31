<?php
$iva21 = Impuestos::iva21;
?>
<script type='text/javascript'>
	$(document).ready(function(){
		detalle = [];
		arrayIva = [];
		idProveedor = '';
		tituloPrograma = 'Generación de órden de compra';

		$('#inputTieneIva').change(
			function(){
				$('.precioUnitario').blur();
			}
		);

		cambiarModo('inicio');
	});

	function limpiarScreen() {
	}

	function buscar() {
		var mensaje;
		if($('#inputBuscarTipo').val() == '')
			mensaje = 'Debe seleccionar un tipo de presupuesto';

		if($('#inputBuscarProveedor_selectedValue').val() == '')
			mensaje = 'Debe seleccionar un proveedor';

		if(mensaje){
			$.error(mensaje);
		}else {
			var url = funciones.controllerUrl('buscar', {
					idProveedor: $('#inputBuscarProveedor_selectedValue').val(),
					idLoteDeProduccion: $('#inputBuscarLoteDeProduccion_selectedValue').val(),
					desde: $('#inputFechaDesde').val(),
					hasta: $('#inputFechaHasta').val(),
					productiva: $('#inputBuscarTipo').val()
				}),
				msgError = 'No hay presupuestos con ese filtro',
				cbSuccess = function(json){
					var proveedor = ' - [' + $('#inputBuscarProveedor_selectedValue').val() + '] ' +
									$('#inputBuscarProveedor_selectedName').val();
					llenarPantalla(json);
					idProveedor = $('#inputBuscarProveedor_selectedValue').val();
					setTimeout(function(){
						cambiarModo('agregar');
						funciones.cambiarTitulo(tituloPrograma + proveedor);
					}, 100);
				};
			$('#tablaPresupuestos > tbody').html('');
			funciones.buscar(url, cbSuccess, msgError);
		}
	}

	function llenarPantalla(json) {
		var div = $('#divGeneracionOrdenesDeCompra'),
			tbody = $('<tbody>'),
			table = $('<table>').attr('id', 'tablaPresupuestos').addClass('registrosAlternados w100p').append(
				$('<thead>').addClass('tableHeader').append(
					$('<tr>').append(
						$('<th>').addClass('w4p').text('Nº').attr('title', 'Nº presupuesto'),
						$('<th>').addClass('w6p').text('F. entr.').attr('title', 'Fecha entrega'),
						$('<th>').addClass('w4p').text('Lote'),
						$('<th>').addClass('w13p').text('Material'),
						$('<th>').addClass('w6p').text('Color'),
						$('<th>').addClass('w4p').text('Cant.'),
						$('<th>').addClass('w7p').text('Iva'),
						$('<th>').addClass('w6p').text('PU').attr('title', 'Precio unitario'),
						$('<th>').addClass('w36p').text('Cantidades/$'),
						$('<th>').addClass('w11p').text('Camb. costo.').attr('title', '¿Cambiar el costo de referencia unitario?'),
						$('<th>').addClass('w3p').append($('<input>')
															 .attr('type', 'checkbox')
															 .attr('id', 'checkUncheckAll')
															 .addClass('textbox koiCheckbox')
															 .click(function() {
																		$('#checkUncheckAll').isChecked() ? $('#tablaPresupuestos > tbody').find('[type="checkbox"]').check() : $('#tablaPresupuestos > tbody').find('[type="checkbox"]').uncheck();
																	})
						)
					)
				)
			);
		for (var i = 0; i < json.length; i++) {
			tbody.append(returnTr(json[i]));
		}

		div.append(table.append(tbody));
	}

	function returnTr(o) {
		return $('<tr>').addClass('s11').attr('id', 'tr_' + o.idPresupuesto + '_' + o.nroItem).append(
			$('<td>').addClass('aCenter').append($('<label>').text(o.idPresupuesto)),
			$('<td>').addClass('aCenter').append($('<label>').text(o.fechaEntrega)),
			$('<td>').addClass('aCenter').append($('<label>').text(o.idLoteDeProduccion)),
			$('<td>').append($('<label>').text('[' + o.idMaterial + '] ' + o.nombreMaterial)),
			$('<td>').append($('<label>').text('[' + o.idColor + '] ' + o.nombreColor)),
			$('<td>').addClass('aCenter').append($('<label>').text(o.cantidad)),
			$('<td>').addClass('aCenter').append(divIva(o)),
			$('<td>').addClass('aCenter').append(divPrecio(o)),
			$('<td>').addClass('aCenter').append(divCantidades(o)),
			$('<td>').append(divSelectPrecio(o)),
			$('<td>').append(divCheckBox(o))
		);
	}

	function divSelectPrecio(o) {
		var div = $('<div>').addClass('aCenter s11 bold');

		div.append('P. Costo: $ ' + funciones.formatearDecimales(o.precioCabeceraMaterial, 4, '.'), '<br>');
		div.append('P. Prov.: $ ' + funciones.formatearDecimales(o.precioProveedorMaterial, 4, '.'), '<br>');

		div.append(
			$('<select>').addClass('w95p textbox obligatorio')
				.attr('id', 'inputCambiarPrecio_' + o.idPresupuesto + '_' + o.nroItem)
				.append(
					$('<option>').text('S/ cambio en costo').attr('value', '0'),
					$('<option>').text('Este color en costo').attr('value', '1'),
					$('<option>').text('Todos los colores en costo').attr('value', '2')
				)
		);

		div.append('<br>');

		div.append(
			$('<select>').addClass('w95p textbox obligatorio')
				.attr('id', 'inputCambiarPrecioProveedor_' + o.idPresupuesto + '_' + o.nroItem)
				.append(
					$('<option>').text('S/ cambio proveedor').attr('value', '0'),
					$('<option>').text('Este color proveedor').attr('value', '1'),
					$('<option>').text('Todos los colores proveedor').attr('value', '2')
				)
		);

		return div;
	}

	function divIva(o) {
		var div = $('<div>');

		div.append(
			$('<input>').addClass('w60p textbox obligatorio autoSuggestBox impuesto')
						.attr('id', 'inputImpuesto_' + o.idPresupuesto + '_' + o.nroItem)
						.attr('name', 'Impuesto')
						.attr('alt', '&tipo=1')
						.blur(function(){$('#inputPrecio_' + o.idPresupuesto + '_' + o.nroItem).blur();})
		);

		return div;
	}

	function divPrecio(o) {
		var div = $('<div>');

		div.append(
			$('<input>').addClass('w70p textbox')
				.attr('id', 'inputPrecio_' + o.idPresupuesto + '_' + o.nroItem)
				.val(o.precioProveedorMaterial)
				.addClass('precioUnitario')
				.attr('validate', 'DecimalPositivo')
				.blur(function(obj){
					var id = o.idPresupuesto + '_' + o.nroItem,
						idImpuesto = $('#inputImpuesto_' + id + '_selectedValue').val(),
						precio = $(obj.target).val(),
						inputs = $('.inputTalle_' + id),
						precioUnitario = '';

					if(!arrayIva[id] || arrayIva[id].id != idImpuesto){
						$.postJSON(funciones.controllerUrl('getImpuesto', {idImpuesto: idImpuesto}), function(json){
							arrayIva[id] = {id: json.data.id, porcentaje: json.data.porcentaje};
							setearPrecios(precio, id, inputs);
						});
					}else {
						setearPrecios(precio, id, inputs);
					}
				})
		);

		return div;
	}

	function setearPrecios(precio, id, inputs) {
		if($('#inputTieneIva').val() == 'N') {
			var precioUnitario = funciones.toFloat(precio) + (arrayIva[id].id ? (precio * arrayIva[id].porcentaje)/100 : 0);
			inputs.val(funciones.formatearDecimales(precioUnitario, 2, '.'));
		}else {
			inputs.val(funciones.formatearDecimales(precio, 2, '.'));
		}
	}

	function divCantidades(o) {
		var tr = $('<tr>').addClass('s12'),
			cantidades,
			tablaTalles = $('<table>').addClass('w100p'),
			tablaTallesBody = $('<tbody>'),
			trTalles = $('<tr>').addClass('bDarkGray'),
			trCantidades = $('<tr>'),
			trPrecios = $('<tr>');

		if(o.usaRango == 'S'){
			for(var i = 1; i < 11; i++){
				var tdCantidad = $('<td>').addClass('aCenter w10p').text(o.cantidades[i].cantidad),
					tdTalles = $('<td>').addClass('aCenter bold bRightWhite white w10p').text(o.cantidades[i].talle),
					tdPrecios = $('<td>').addClass('aCenter bold bRightWhite white w10p'),
					input = $('<input>').addClass('textbox w60p')
										.attr('validate', 'DecimalPositivo');
				if(o.cantidades[i].cantidad == ''){
					input.attr('disabled', 'disabled');
				}else {
					input.addClass('inputTalle_' + o.idPresupuesto + '_' + o.nroItem);
				}

				tdPrecios.append(input);
				if(i != 10){
					tdCantidad.addClass('bRightDarkGray');
					tdTalles.addClass('pLeft5 pRight5');
					tdPrecios.addClass('bRightDarkGray');
				}
				trTalles.append(tdTalles);
				trCantidades.append(tdCantidad);
				trPrecios.append(tdPrecios);
			}
			tablaTallesBody.append(trTalles, trCantidades, trPrecios);
			tablaTalles.append(tablaTallesBody);
			cantidades = tablaTalles;
		} else{
			cantidades = $('<div>').append(
				$('<label>').text('Precio unitario: '),
				$('<input>').addClass('inputTalle_' + o.idPresupuesto + '_' + o.nroItem + ' textbox w50').attr('validate', 'DecimalPositivo')
			);
		}

		return cantidades;
	}

	function divCheckBox(o) {
		var div = $('<div>').addClass('aCenter');

		div.append($('<input>')
					   .attr('type', 'checkbox')
					   .attr('id', o.idPresupuesto + o.nroItem)
					   .data('data', {idPresupuesto: o.idPresupuesto, nroItem: o.nroItem, usaRango: o.usaRango})
					   .addClass('textbox koiCheckbox')
					   .click(function() {
								  var todos = true;
								  $('#tablaPresupuestos > tbody').find('[type="checkbox"]').each(function(i, item) {
									  if (!$(item).isChecked()) {
										  todos = false;
									  }
								  });
								  todos ? $('#checkUncheckAll').check() : $('#checkUncheckAll').uncheck();
							  })
		);

		return div;
	}

	function hayErrorGuardar(){
		detalle = [];

		$('#tablaPresupuestos > tbody').find('[type="checkbox"]').each(function(i, item) {
			if ($(item).isChecked()) {
				var precios = {},
					inputs,
					j = 1,
					cambiarPrecio,
					cambiarPrecioProveedor,
					id = $(item).data('data').idPresupuesto + '_' + $(item).data('data').nroItem;

				inputs = $(item).parents('tr').find('.inputTalle_' + id);
				cambiarPrecio = $('#inputCambiarPrecio_' + id).val();
				cambiarPrecioProveedor = $('#inputCambiarPrecioProveedor_' + id).val();

				if($(item).data('data').usaRango == 'S'){
					for(var k = 0; k < inputs.length; k++){
						precios[j] = $(inputs[k]).val();
						j++;
					}
				} else{
					precios = $(inputs[0]).val();
				}

				detalle.push(
					{
						id: $(item).data('data'),
						precios: precios,
						cambiarPrecio: cambiarPrecio,
						cambiarPrecioProveedor: cambiarPrecioProveedor,
						idImpuesto: (arrayIva[id] ? arrayIva[id].id : '')
					});
			}
		});

		if(detalle.length == 0){
			return 'Debe seleccionar al menos un detalle de presupuesto para generar la órden de compra';
		}

		return false;
	}

	function guardar(){
		funciones.guardar(funciones.controllerUrl('agregar'),{
				detalle: detalle,
				idProveedor: idProveedor,
				tieneIva: $('#inputTieneIva').val(),
				observaciones: $('#inputObservaciones').val()
		}, function(){
			funciones.pdfClick(funciones.pathBase + '/produccion/compras/ordenes_compra/reimpresion/getPdf.php?id=' + this.data.id);
			funciones.reload();
		});
	}

	function cancelarBuscarClick(){
		$('#divGeneracionOrdenesDeCompra').html('');
		funciones.cancelarBuscarClick()
	}

	function cambiarModo(modo){
		funciones.cambiarModo(modo);
		switch (modo){
			case 'inicio':
				$('#divDatos').hide();
				break;
			case 'buscar':
				$('#divDatos').show();
				break;
			case 'editar':
				break;
			case 'agregar':
				$('.impuesto').val(<?php echo $iva21; ?>).blur();
				break;
		}
	}
</script>

<div id='programaTitulo'></div>
<div id='programaContenido' class='customScroll'>
	<div id='divDatos'>
		<div id='divDatos1' class='fLeft pantalla'>
		<?php
		$tabla = new HtmlTable(array('cantRows' => 2, 'cantCols' => 2, 'id' => 'tablaDatos', 'cellSpacing' => 2));
		$tabla->getRowCellArray($rows, $cells);

		$cells[0][0]->content = '<label>Precios c/ IVA:</label>';
		$cells[0][0]->style->width = '150px';
		$cells[0][1]->content = '<select id="inputTieneIva" class="textbox obligatorio inputForm no-editable w200" rel="productiva" >';
		$cells[0][1]->content .= '<option value="S">Si</option>';
		$cells[0][1]->content .= '<option value="N">No</option>';
		$cells[0][1]->content .= '</select>';
		$cells[0][1]->style->width = '210px';

		$cells[1][0]->content = '<label>Lote:</label>';
		$cells[1][1]->content = '<input id="inputLoteDeProduccion" class="textbox autoSuggestBox inputForm w200" name="LoteDeProduccion" rel="loteDeProduccion" />';

		$tabla->create();
		?>
		</div>

		<div id='divDatos2' class='fLeft pantalla'>
		<?php
		$tabla = new HtmlTable(array('cantRows' => 1, 'cantCols' => 2, 'id' => 'tablaDatos', 'cellSpacing' => 2));
		$tabla->getRowCellArray($rows, $cells);

		$cells[0][0]->content = '<label>Observaciones:</label>';
		$cells[0][0]->style->width = '150px';
		$cells[0][1]->content = '<textarea id="inputObservaciones" class="textbox inputForm w200" rel="observaciones"></textarea>';
		$cells[0][1]->style->width = '210px';

		$tabla->create();
		?>
		</div>
	</div>
	<div id='divGeneracionOrdenesDeCompra' class='w100p customScroll acordeon h420'>
		<?php //TABLOTA ?>
	</div>
</div>
<div id='programaPie'>
	<div id='filtro' class='draggableDialog hidden'>
		<div>
			<label for='inputBuscarProveedor' class='filtroBuscar'>Proveedor:</label>
			<input id='inputBuscarProveedor' class='textbox obligatorio autoSuggestBox filtroBuscar w220' name='Proveedor' />
		</div>
		<div>
			<label for='inputBuscarLoteDeProduccion' class='filtroBuscar'>Lote:</label>
			<input id='inputBuscarLoteDeProduccion' class='textbox autoSuggestBox filtroBuscar w220' name='LoteDeProduccion' />
		</div>
		<div>
			<label for='inputFechaDesde' class='filtroBuscar' title='Corresponde a la fecha de creación de la órden de compra'>Rango fecha:</label>
			<input id='inputFechaDesde' class='textbox filtroBuscar w80' to='inputFechaHasta' validate='Fecha' />
			<input id='inputFechaHasta' class='textbox filtroBuscar w80' from='inputFechaDesde' validate='Fecha' />
		</div>
		<div>
			<label for='inputBuscarTipo' class='filtroBuscar'>Tipo:</label>
			<select id='inputBuscarTipo' class='textbox obligatorio filtroBuscar w220'>
				<option value='S'>Productiva</option>
				<option value='N'>No productiva</option>
			</select>
		</div>
		<div>
			<a id='btnMiniBuscar' class='boton' href='#' title='Buscar'><img src="/img/botones/25/buscar.gif" /></a>
		</div>
	</div>
	<div class='botonera'>
		<?php Html::echoBotonera(array('boton' => 'buscar', 'accion' => 'funciones.buscarClick();', 'permiso' => 'produccion/compras/ordenes_compra/generacion/buscar/')); ?>
		<?php Html::echoBotonera(array('boton' => 'guardar', 'accion' => 'funciones.guardarClick();', 'permiso' => 'produccion/compras/ordenes_compra/generacion/agregar/')); ?>
		<?php Html::echoBotonera(array('boton' => 'cancelar', 'accion' => 'cancelarBuscarClick();', 'id' => 'btnCancelarBuscar')); ?>
	</div>
</div>
