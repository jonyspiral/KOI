<style>
	.letra{
		margin-left: 8px;
		border: solid 1px rgb(173, 173, 173);
		padding: 0 6px;
		border-radius: 5px;
		color: rgb(92, 92, 92);
	}
</style>

<script type='text/javascript'>
	var detallesComunes = {},
		impuestos = {},
		impuestosAEnviar,
		maxImpuesto,
		aplicaciones = [],
		checkNoAplica = false,
		idUiDetalle = 1,
		idUiImpuestos = 0,
		nroAutorizacion = 1,
		imputacionProveedor,
		diferenciaAdmisibleTotalDocumento = 0.1,//Centavos que puede diferir el impuesto de la factura con respecto al calculado.
		diferenciaAdmisiblePrecioUnitario = 0.05,//Centavos que puede diferir el precio unitario sin tirar advertencia.
		tituloPrograma;

	$(document).ready(function(){
		tituloPrograma = 'Documento gastos';
		$('#liDetalle').click(function(){popUpAgregarEditarDetalle(true);});
		$('#inputProveedor').blur(function(){//noinspection JSUnresolvedVariable
			funciones.delay('llenarLetraProveedor();');});
		$('#inputFechaDocumento').blur(function(){funciones.delay('blurFechaDocumento();');});
		$('#inputNetoGravado').blur(function(){funciones.delay('blurImporteEnCero(' + 'inputNetoGravado' + ');');});
		$('#inputNetoNoGravado').blur(function(){funciones.delay('blurImporteEnCero(' + 'inputNetoNoGravado' + ');');});
		cambiarModo('inicio');
		$('#divTablaDetalles').append($('<table>').attr('id', 'tablaDetalles').addClass('registrosAlternados overflowhidden w100p'));
		$('#divTablaImpuestos').append($('<table>').attr('id', 'tablaImpuestos').addClass('registrosAlternados overflowhidden w100p'));
		$('#tablaImpuestos').append($('<thead>').addClass('tableHeader').append($('<tr>')
																					.append($('<th>').addClass('w60p').append('Tipo'))
																					.append($('<th>').addClass('w17p').append('Porc.'))
																					.append($('<th>').addClass('w17p').append('Imp.'))
																					.append($('<th>').addClass('w6p'))))
			.append($('<tbody>'));

		$('#tablaDetalles').append($('<thead>').addClass('tableHeader').append($('<tr>')
																				   .append($('<th>').addClass('w8p').append('Cant'))
																				   .append($('<th>').addClass('w50p').append('Desc'))
																				   .append($('<th>').addClass('w15p').append('P unit'))
																				   .append($('<th>').addClass('w15p').append('Sub.'))
																				   .append($('<th>').addClass('w6p'))
																				   .append($('<th>').addClass('w6p'))))
			.append($('<tbody>'));

		$('#btnMiniAgregarImpuesto').click(
			agregarTrImpuesto
		);

		$('#btnMiniActualizarImpuestos').click(actualizarImpuestos);

		$('#inputEsProveedor').change(function(){
			if($(this).val() == 'S'){
				$('.trProveedor').show();
				$('.trRandom').hide();
			}else{
				$('.trRandom').show();
				$('.trProveedor').hide();
			}
		});

		$('#inputTipoDocumento2').change(function(){
			if($(this).val() == 'FAC'){
				tituloPrograma = 'Factura';
				$('.trFactura').hide();
			}else{
				if($(this).val() == 'NCR'){
					tituloPrograma = 'Nota de crédito proveedor';
					$('.trFactura').show();
				}else{
					tituloPrograma = 'Nota de débito proveedor';
					$('.trFactura').hide();
				}
			}
			funciones.cambiarTitulo(tituloPrograma);
		});

		$(document).keydown(function(e) {
			var tag = e.target.tagName.toLowerCase();
			if (tag != 'input' && tag != 'textarea') {
				switch (e.which) {
					case 68:
						$('#liDetalle').isVisible() && $('#liDetalle').click();
						break;
				}
			}
		});
	});

	function actualizarImpuestos(){
		$.each(impuestos, function(key, value){
			$('#inputImporteImpuesto' + key).val(calcularImpuesto(value.porcentaje.val()));
		});
		calcularSugeridosEImpuestos();
	}

	function blurImporteEnCero(elemento){
		if($(elemento).val() == '')
			$(elemento).val(0);
		actualizarImpuestos();
	}

	function agregarTrImpuesto(){
		idUiImpuestos = idUiImpuestos + 1;
		var id = idUiImpuestos,
			tr = $('<tr>').attr('id', 'tr_I_' + idUiImpuestos)
				.append($('<td>').addClass('w65p').append($('<div>').append('<input id="inputImpuesto' + id + '" class="textbox obligatorio autoSuggestBox w269 field-handler" name="Impuesto"/>')))
				.append($('<td>').addClass('w15p').append($('<div>').append('<input id="inputPorcentajeImpuesto' + id + '" class="textbox obligatorio aRight w64" validate="Decimal" name="Impuesto"/>')))
				.append($('<td>').addClass('w15p').append($('<div>').append('<input id="inputImporteImpuesto' + id + '" class="textbox obligatorio aRight w64 field-handler" name="ImporteImpuesto" validate="Decimal"/>')))
				.append($('<td>').addClass('w5p btn-handler').append($('<div>').addClass('aCenter').append(
					$('<a>').addClass('boton').attr('href', '#').attr('title', 'Quitar')
						.append($('<img>').attr('src', '/img/botones/25/menos.gif'))
						.click(function(){borrarImpuesto(id);})
				)));
		$('#tablaImpuestos tbody').append(tr);
		$('#inputPorcentajeImpuesto' + id).change(actualizarImpuestoMasAlto);
		$('#inputImporteImpuesto' + id).blur(function(){
			funciones.delay('calcularSugeridosEImpuestos();');});
		$('#inputImpuesto' + id).blur(function(){
			funciones.delay('llenarPorcentajeImporteImpuesto("' + $('#inputImpuesto' + id + '_selectedValue').val() + '","' + id + '");');});
		impuestos[idUiImpuestos] = {id: id, tipo: $('#inputImpuesto' + id), porcentaje: $('#inputPorcentajeImpuesto' + id), importe: $('#inputImporteImpuesto' + id)};
	}

	function quitarDeArray(array, id){
		delete array[id];
	}

	function llenarLetraProveedor(){
		if($('#inputProveedor_selectedValue').val() == ''){
			$('#labelLetraFactura').html('_');
		}else{
			$.postJSON('/content/administracion/tesoreria/gastos/documento_gastos/getInfoProveedor.php?idProveedor=' + $('#inputProveedor_selectedValue').val(), function(json){
				$('#labelLetraFactura').html(json.data.letra);
				imputacionProveedor = (json.data.imputacionProveedor ? json.data.imputacionProveedor.id : '');
			});
		}
	}

	function llenarPorcentajeImporteImpuesto(idInput, id){
		var impuesto = $('#inputImpuesto' + id + '_selectedValue'),
			flag = true;
		$.each(impuestos, function(k, value){
			if(value.tipo.next().val() == impuesto.val() && value.tipo.next().attr('id') != impuesto.attr('id') && !value.borrar){
				$.error('No puede ingresar dos veces el mismo impuesto.');
				flag = false;
			}
		});

		if(flag){
			if($('#inputImpuesto' + id + '_selectedValue').val() == ''){
				$('#inputPorcentajeImpuesto' + id).val('');
				$('#inputImporteImpuesto' + id).val('');
			}else {
				$.postJSON('/content/administracion/tesoreria/gastos/documento_gastos/getPorcentajeImpuesto.php?idImpuesto=' + idInput, function(json){
					$('#inputPorcentajeImpuesto' + id).val(json.data.porcentaje);
					$('#inputImporteImpuesto' + id).val(calcularImpuesto(json.data.porcentaje));
					calcularSugeridosEImpuestos();
					actualizarImpuestoMasAlto();
				});
			}
		}
	}

	function actualizarImpuestoMasAlto(){
		var actual,
			primero = true;

		for(var pos in impuestos){
			if(primero){
				maxImpuesto = impuestos[pos].porcentaje.val();
				primero = false;
			}

			actual = impuestos[pos].porcentaje.val();

			if(funciones.toInt(actual) > funciones.toInt(maxImpuesto))
				maxImpuesto = actual;
		}
	}

	function calcularImpuesto(porcentaje){
		return funciones.formatearDecimales((porcentaje * $('#inputNetoGravado').val())/100, 2, '.');
	}

	function blurFechaDocumento(){
		if($('#inputProveedor_selectedValue').val() != '' && $('#inputEsProveedor').val() == 'S'){
			$.postJSON('/content/administracion/tesoreria/gastos/documento_gastos/getInfoProveedor.php?idProveedor=' + $('#inputProveedor_selectedValue').val() + '&fechaDocumento=' + $('#inputFechaDocumento').val(), function(json){
				$('#inputFechaVencimiento').val(json.data.fechaVto);
			});
		}
	}

	function calcularSugeridosEImpuestos(){
		totalNetoGravado = 0;
		totalNetoNoGravado = 0;
		totalImpuestos = 0;
		total = 0;

		$.each(detallesComunes, function(k, value){
			if(value.borrar != 'S'){
				if(value.gravado == 'S'){
					totalNetoGravado += funciones.toFloat(value.precioUnitario) * funciones.toFloat(value.cantidad);
				}else{
					totalNetoNoGravado += funciones.toFloat(value.precioUnitario) * funciones.toFloat(value.cantidad);
				}
			}
		});

		$.each(impuestos, function(k, value){
			totalImpuestos += funciones.toFloat(value.importe.val());
		});

		totalNetoGravado = funciones.toFloat(totalNetoGravado);
		totalNetoNoGravado = funciones.toFloat(totalNetoNoGravado);
		totalImpuestos = funciones.toFloat(totalImpuestos);

		total = totalNetoGravado + totalNetoNoGravado + totalImpuestos;

		$('#spanNetoGravadoSugerido').html('Sugerido:  ' + funciones.formatearMoneda(totalNetoGravado));
		$('#spanNetoNoGravadoSugerido').html('Sugerido:  ' + funciones.formatearMoneda(totalNetoNoGravado));
		$('#spanImporteTotalSugerido').html('Sugerido:  ' + funciones.formatearMoneda(total));
		$('#inputImporteTotal').val(funciones.formatearDecimales(total, 2, '.'));
		$('#divTotalImpuestos').html(funciones.formatearMoneda(totalImpuestos));
	}

	function limpiarScreen(){
		funciones.cambiarTitulo();
		impuestos = {};
		detallesComunes = {};
		$('#tablaDetalles tbody').html('');
		$('#tablaImpuestos tbody').html('');
	}

	function validarCamposBuscar() {
		return $('#inputBuscarNumeroDocumento_selectedValue').val() != '';
	}

	function buscar() {
		if(validarCamposBuscar()){
			funciones.limpiarScreen();
			var url = '/content/administracion/tesoreria/gastos/documento_gastos/buscar.php?idDocumentoProveedor=' + $('#inputBuscarNumeroDocumento_selectedValue').val(),
				msgError = 'El deocumento número "' + $('#inputBuscarNumeroDocumento_selectedName').val() + '" no existe o no tiene permiso para visualizarlo.',
				cbSuccess = function(json){
					setTimeout(function(){
						$('#inputEsProveedor').val((json.documentoGastoDatos.id == null ? 'S' : 'N'));
						$('#inputEsProveedor').change();

						if(json.documentoGastoDatos.id != null){
							$('#inputRazonSocial').val(json.documentoGastoDatos.razonSocial);
							$('#inputCuit').val(json.documentoGastoDatos.cuit);
							$('#inputCondicionIva').val(json.documentoGastoDatos.idCondicionIva).autoComplete();
							$('#inputLetra').val(json.letra);
							$('#inputCalle').val(json.documentoGastoDatos.direccion.calle);
							$('#inputNumeroCalle').val(json.documentoGastoDatos.direccion.numero);
							$('#inputPiso').val(json.documentoGastoDatos.direccion.piso);
							$('#inputDpto').val(json.documentoGastoDatos.direccion.departamento);
							$('#inputPais').val(json.documentoGastoDatos.direccion.idPais).autoComplete();
							$('#inputProvincia').val(json.documentoGastoDatos.direccion.idProvincia).autoComplete();
							$('#inputLocalidad').val(json.documentoGastoDatos.direccion.idLocalidad).autoComplete();
							$('#inputCodPostal').val(json.documentoGastoDatos.direccion.codigoPostal);
							$('#inputImputacionProveedor').val(json.documentoGastoDatos.imputacion.id).autoComplete();
						}

						$('#inputProveedor').val(json.proveedor.id).autoComplete();
						$('#inputTipoDocumento2').val(json.tipoDocum);
						$('#inputTipoDocumento2').change();
						$('#inputPuntoDeVenta').val(json.puntoVenta);
						$('#inputNumeroDocumento').val(json.nroDocumento);
						$('#inputFechaDocumento').val(json.fecha);
						$('#inputFechaVencimiento').val(json.fechaVencimiento);
						$('#inputFechaPeriodoFiscal').val(json.fechaPeriodoFiscal);
						$('#inputNetoGravado').val(json.netoGravado);
						$('#inputNetoNoGravado').val(json.netoNoGravado);
						$('#inputImporteTotal').val(json.importeTotal);
						$('#inputObservaciones').val(json.observaciones);
						json.documentoEnConflicto == 'S' ? $('#inputDocumentoEnConflicto').check() : $('#inputDocumentoEnConflicto').uncheck();
						llenarLetraProveedor();
						tituloPrograma = tituloPrograma + ' - ' + json.tipoDocum + ' ' + json.id;

						detallesComunes = {};
						$.each(json.detalle, function(k, value){
							value.id = value.idDocumentoProveedor + '' + value.nroItem;
							detallesComunes[value.id] = value;
						});

						impuestos = {};
						$.each(json.impuestos, function(k, value){
							agregarTrImpuesto();
							var id = idUiImpuestos;
							$('#inputImpuesto' + idUiImpuestos).autoSuggestBox().val(value.idImpuesto).autoComplete();
							setTimeout(function(){
								$('#inputPorcentajeImpuesto' + id).val(value.porcentaje);
								$('#inputImporteImpuesto' + id).val(value.importe);
							}, 100);
							impuestos[id].idDocumentoProveedor = value.idDocumentoProveedor;
							impuestos[id].porcentaje = $('#inputPorcentajeImpuesto' + id);
						});

						funciones.cambiarTitulo(tituloPrograma);
						llenarPantalla(detallesComunes, returnTrDetalle);
						$('.btn-handler').hide();
						$('.field-handler').disable();
					}, 150);
					setTimeout(function(){
						calcularSugeridosEImpuestos();
					}, 500);
				};
			funciones.buscar(url, cbSuccess, msgError);
		}else{
			$.error('Debe especificar un número de documento para realizar la búsqueda.');
		}
	}

	function divBotonEditarDetalleComun(o) {
		var div = $('<div>').addClass('aCenter');
		var btn = $('<a>').addClass('boton').attr('href', '#').attr('title', 'Editar')
			.click($.proxy(function() {editarDetalle(this);}, o))
			.append($('<img>').attr('src', '/img/botones/25/editar.gif'));
		div.append(btn);
		return div;
	}

	function divBotonBorrarDetalle(o) {
		var div = $('<div>').addClass('aCenter');
		var btn = $('<a>').addClass('boton').attr('href', '#').attr('title', 'Eliminar')
			.click($.proxy(function() {borrarDetalle(o.id, o.tipo);}, o))
			.append($('<img>').attr('src', '/img/botones/25/menos.gif'));
		div.append(btn);
		return div;
	}

	function returnTrDetalle(o) {
		o.tipo = 'D';
		detallesComunes[o.id] = o;
		return returnTr($('<tr>').attr('id', 'tr_D_' + o.id), o, divBotonEditarDetalleComun, divBotonBorrarDetalle);
	}

	function returnTr(tr, o, divBotonEditar, divBotonBorrar, divCheckCambioPrecio){
		return tr.addClass('s13').append(
			$('<td>').append(o.cantidad),
			$('<td>').append(o.descripcion),
			$('<td>').append(funciones.formatearMoneda(o.precioUnitario)),
			$('<td>').append(funciones.formatearMoneda(o.cantidad * o.precioUnitario)),
			$('<td>').addClass('btn-handler').append(divBotonEditar(o)),
			$('<td>').addClass('btn-handler').append(divBotonBorrar(o)));
	}

	function llenarPantalla(json, funcion, esGravado){
		var table = $('#tablaDetalles');
		$.each(json, function(k, value){
			if(esGravado){
				value.gravado = 'S';
			}
			table.append(funcion(value));
		});
		calcularSugeridosEImpuestos();
	}

	function refrescarDetalle(id) {
		if(detallesComunes[id].idDocumentoProveedor){
			detallesComunes[id].borrar = 'S';
		}else{
			quitarDeArray(detallesComunes, id);
		}
		borrarUnDetalle('#tr_D_' + id);
	}

	function refrescarImpuesto(id) {
		if(impuestos[id].idDocumentoProveedor){
			impuestos[id].borrar = 'S';
		}else{
			quitarDeArray(impuestos, id);
		}
		borrarUnDetalle('#tr_I_' + id);
	}

	function borrarUnDetalle(tr) {
		var before = $(tr).prev();

		if (before.length < 1)
			$(tr).remove();
		else
			before.next().remove();
	}

	function borrarDetalle(id, tipo) {
		var msg = '¿Está seguro que desea borrar el detalle?';

		$.confirm(msg, function(r){
			if (r == funciones.si){
				$.showLoading();
				refrescarDetalle(id);
				calcularSugeridosEImpuestos();
				$.hideLoading();
			}
		});
		calcularSugeridosEImpuestos();
	}

	function borrarImpuesto(id) {
		$.showLoading();
		refrescarImpuesto(id);
		actualizarImpuestoMasAlto();
		$.hideLoading();
	}

	function popUpAgregarEditarDetalle(nuevo){
		nuevo = nuevo || false;
		var div = '<div class="h100 vaMiddle table-cell aLeft p10">' +
				  '<table><tbody>' +
				  '<tr><td class="w90"><label for="inputCantidadDetalle">Cantidad:</label></td><td><input id="inputCantidadDetalle" class="textbox obligatorio aRight w190" validate="DecimalPositivo" rel="cantidadRemito" /></td></tr>' +
				  '<tr><td><label for="inputPrecioUnitarioDetalle">Importe:</label></td><td><input id="inputPrecioUnitarioDetalle" class="textbox obligatorio aRight w190" validate="DecimalPositivo" rel="importe" /></td></tr>' +
				  '<tr><td><label for="inputImporteTotalDetalle">Total:</label></td><td><input id="inputImporteTotalDetalle" class="textbox obligatorio aRight w190" validate="DecimalPositivo" rel="importe" /></td></tr>' +
				  '<tr><td><label for="inputImputacion">Imputación:</label></td><td><input id="inputImputacion" class="textbox obligatorio autoSuggestBox w190" name="Imputacion" /></td></tr>' +
				  '<tr><td><label>Gravado:</label></td><td><input type="checkbox" class="textbox koiCheckbox" id="inputGravadoDetalle"></td></tr>' +
				  '<tr><td><label for="inputDescripcion">Descripción:</label></td><td><textarea id="inputDescripcion" class="textbox obligatorio w190" rel="descripcion" /></td></tr>' +
				  '<tr><td><input id="inputIdUi" class="hidden" rel="id" /></tr></td>' +
				  '</tbody></table>' +
				  '</div>';
		var botones = [{value: 'Guardar', action: function() {doAgregarEditarDetalle();}}, {value: 'Cancelar', action: function(){$.jPopUp.close();}}];
		$.jPopUp.show(div, botones);
		$('#inputCantidadDetalle').blur(function(){funciones.delay('blurImporteDetalle();');});
		$('#inputPrecioUnitarioDetalle').blur(function(){funciones.delay('blurImporteDetalle();');});
		$('#inputPrecioUnitarioDetalle').change(function(){funciones.delay('changeImporteDetalle();');});
		$('#inputImporteTotalDetalle').change(function(){funciones.delay('changeImporteTotalDetalles();');});
		$('#inputImporteTotalDetalle').blur(function(){funciones.delay('blurTotalDetalle();');});

		setTimeout(function(){
			if (nuevo) {
				$('#inputGravadoDetalle').check();
			}
			blurImporteDetalle();
			if($('#inputImputacion_selectedValue').val() == '')
				$('#inputImputacion').val(imputacionProveedor).autoComplete();
		}, 100);
	}

	function blurImporteDetalle(){
		var nuevoValorTotal = $('#inputCantidadDetalle').val() * $('#inputPrecioUnitarioDetalle').data('importeUnitario');
		if (nuevoValorTotal || nuevoValorTotal === 0) {
			$('#inputImporteTotalDetalle').data('importeTotal', nuevoValorTotal);
			$('#inputImporteTotalDetalle').val(funciones.formatearDecimales(nuevoValorTotal, 4, '.'));
		}
	}

	function blurTotalDetalle(){
		var nuevoValorPrecioUnitario = $('#inputImporteTotalDetalle').data('importeTotal') / $('#inputCantidadDetalle').val();
		$('#inputPrecioUnitarioDetalle').data('importeUnitario', nuevoValorPrecioUnitario);
		$('#inputPrecioUnitarioDetalle').val(funciones.formatearDecimales(nuevoValorPrecioUnitario, 4, '.'));
	}

	function changeImporteDetalle(){
		$('#inputPrecioUnitarioDetalle').data('importeUnitario', $('#inputPrecioUnitarioDetalle').val());
	}

	function changeImporteTotalDetalles(){
		$('#inputImporteTotalDetalle').data('importeTotal', $('#inputImporteTotalDetalle').val());
	}

	function doAgregarEditarDetalle(){
		var precioUnitario = $('#inputPrecioUnitarioDetalle').data('importeUnitario'),
			idImputacion = $('#inputImputacion_selectedValue').val(),
			imputacion = $('#inputImputacion_selectedName').val(),
			cantidad = $('#inputCantidadDetalle').val(),
			descripcion = $('#inputDescripcion').val(),
			total = $('#inputImporteTotalDetalle').val(),
			gravado = $('#inputGravadoDetalle').isChecked() ? 'S' : 'N',
			id = $('#inputIdUi').val(),
			objeto = {total: total, cantidad: cantidad, precioUnitario: precioUnitario, idImputacion: idImputacion, imputacion: imputacion, gravado: gravado, descripcion: descripcion, id: id, tipo: 'D'};

		if(precioUnitario && idImputacion && descripcion){
			if(id == ''){
				objeto.id = idUiDetalle++;
				llenarPantalla([objeto], returnTrDetalle);
			}else{
				detallesComunes[id].id = id;
				detallesComunes[id].total = total;
				detallesComunes[id].cantidad = cantidad;
				detallesComunes[id].precioUnitario = precioUnitario;
				detallesComunes[id].idImputacion = idImputacion;
				detallesComunes[id].imputacion = imputacion;
				detallesComunes[id].gravado = gravado;
				detallesComunes[id].descripcion = descripcion;
				returnTr($('#tr_D_' + id).html(''), objeto, divBotonEditarDetalleComun, divBotonBorrarDetalle);
			}
			calcularSugeridosEImpuestos();
			$.jPopUp.close();
		}else{
			$.error('Todos los campos son obligatorios.');
		}
	}

	function editarDetalle(o){
		popUpAgregarEditarDetalle();
		setTimeout(function() {
			$('#inputImputacion').val(o.idImputacion).autoComplete();
			$('#inputCantidadDetalle').val(o.cantidad);
			$('#inputPrecioUnitarioDetalle').data('importeUnitario', o.precioUnitario);
			$('#inputPrecioUnitarioDetalle').val(funciones.formatearDecimales(o.precioUnitario, 4, '.'));
			$('#inputImporteTotalDetalle').val(o.total);
			o.gravado == 'S' ? $('#inputGravadoDetalle').check() : '';
			$('#inputDescripcion').val(o.descripcion);
			$('#inputIdUi').val(o.id);
		}, 25);
	}

	function hayErrorGuardar(){
		if($('#inputEsProveedor').val() == 'S'){
			if($('#inputProveedor_selectedValue').val() == '')
				return 'Debe seleccionar un proveedor.';
		}else {
			if($('#inputImputacionProveedor').val() == '')
				return 'Debe especificar una imputación para el proveedor.';
		}
		if($('#inputTipoDocumento2').val() == null)
			return 'Debe especificar un tipo de documento.';
		if($('#inputPuntoDeVenta').val() == '' || $('#inputNumeroDocumento').val() == '')
			return 'Debe especificar el numero de documento.';
		if($('#inputFechaDocumento').val() == '')
			return 'Debe ingresar la fecha del documento.';
		if($('#inputFechaVencimiento').val() == '')
			return 'Debe ingresar la fecha de vencimiento.';
		if($('#inputFechaPeriodoFiscal').val() == '')
			return 'Debe ingresar la fecha del período fiscal.';
		if($('#inputNetoGravado').val() == '')
			return 'Debe ingresar el neto gravado.';
		if($('#inputNetoNoGravado').val() == '')
			return 'Debe ingresar el neto no gravado.';
		if($('#inputImporteTotal').val() == '')
			return 'Debe ingresar el importe total.';
		if(Object.keys(detallesComunes).length == 0)
			return 'El documento debe contener al menos un detalle valido.';

		var totalGravado = 0,
			totalNoGravado = 0;

		$.each(detallesComunes, function(k, value){
			if(value.borrar != 'S'){
				if(value.gravado == 'S'){
					totalGravado += funciones.toFloat(value.precioUnitario) * funciones.toFloat(value.cantidad);
				}else{
					totalNoGravado += funciones.toFloat(value.precioUnitario) * funciones.toFloat(value.cantidad);
				}
			}
		});

		totalGravado = funciones.formatearDecimales((totalGravado), 2);
		totalNoGravado = funciones.formatearDecimales(totalNoGravado, 2);

		if(totalGravado != funciones.formatearDecimales($('#inputNetoGravado').val(), 2))
			return 'El neto gravado ingresado no coincide con los detalles gravados';

		if(totalNoGravado != funciones.formatearDecimales($('#inputNetoNoGravado').val(), 2))
			return 'El neto no gravado ingresado no coincide con los detalles no gravados';

		impuestosAEnviar = {};

		if(totalGravado == 0 && impuestos.length > 0)
			return 'No puede agregar impuestos sobre un documento sin neto gravado.';

		if(!$.isEmptyObject(impuestos)){
			var totalImpuestos = 0,
				i = 0;

			for(var pos in impuestos){
				var value = impuestos[pos];
				i++;
				if(value.tipo.next().val() == '' || (value.importe.val() == '' || value.importe.val() == 0))
					return 'Todos los campos de los impuestos son obligatorios.';

				impuestosAEnviar[i] = {};
				impuestosAEnviar[i].idImpuesto = value.tipo.next().val();
				impuestosAEnviar[i].importe = value.importe.val();
				impuestosAEnviar[i].idDocumentoProveedor = value.idDocumentoProveedor;
				impuestosAEnviar[i].porcentaje = value.porcentaje.val();
				totalImpuestos = funciones.toFloat(totalImpuestos) + funciones.toFloat(value.importe.val());
				totalImpuestos = funciones.formatearDecimales(totalImpuestos, 2);
			}

			var diferencia = funciones.toFloat($('#inputImporteTotal').val()) - (funciones.toFloat(totalGravado) + funciones.toFloat(totalNoGravado) + funciones.toFloat(totalImpuestos));
			if(Math.abs(diferencia) > diferenciaAdmisibleTotalDocumento)
				return 'El importe total ingresado difiere del importe calculado a partir de los parciales';
		}

		return false;
	}

	function guardar(){
		var aux = ($('#inputBuscarNumeroDocumento_selectedValue').val() != '' ? 'editar' : 'agregar');
		var url = '/content/administracion/tesoreria/gastos/documento_gastos/' + aux + '.php?';
		try {
			funciones.guardar(url, armoObjetoGuardar());
		} catch (ex) {
			$.error(ex);
		}
	}

	function borrar(){
		var msg = '¿Está seguro que desea borrar el documento nro. "' + $('#inputBuscarNumeroDocumento_selectedName').val() + '"?',
			url = '/content/administracion/tesoreria/gastos/documento_gastos/borrar.php?';
		funciones.borrar(msg, url, armoObjetoBorrar());
	}

	function armoObjetoBorrar(){
		return {idDocumentoProveedor: $('#inputBuscarNumeroDocumento_selectedValue').val()};
	}

	function armoObjetoGuardar(){
		return {
			idDocumentoProveedor: $('#inputBuscarNumeroDocumento_selectedValue').val(),
			tipoDocumento: $('#inputTipoDocumento2').val(),
			facturaCancelatoria: $('#inputFacturaCancelatoria_selectedValue').val(),
			idProveedor: $('#inputProveedor_selectedValue').val(),
			puntoDeVenta: $('#inputPuntoDeVenta').val(),
			numero: $('#inputNumeroDocumento').val(),
			fechaDocumento: $('#inputFechaDocumento').val(),
			fechaVencimiento: $('#inputFechaVencimiento').val(),
			fechaPeriodoFiscal: $('#inputFechaPeriodoFiscal').val(),
			netoGravado: $('#inputNetoGravado').val(),
			netoNoGravado: $('#inputNetoNoGravado').val(),
			importeTotal: $('#inputImporteTotal').val(),
			observaciones: $('#inputObservaciones').val(),
			documentoEnConflicto: ($('#inputDocumentoEnConflicto').isChecked() ? 'S' : 'N'),
			detallesComunes: detallesComunes,
			impuestos: impuestosAEnviar,
			nroAutorizacion: nroAutorizacion,
			esProveedor: $('#inputEsProveedor').val(),
			razonSocial: $('#inputRazonSocial').val(),
			cuit: $('#inputCuit').val(),
			condicionIva: $('#inputCondicionIva_selectedValue').val(),
			idImputacion: $('#inputImputacionProveedor_selectedValue').val(),
			letra: $('#inputLetra').val(),
			calle: $('#inputCalle').val(),
			numeroCalle: $('#inputNumeroCalle').val(),
			piso: $('#inputPiso').val(),
			dpto: $('#inputDpto').val(),
			pais: $('#inputPais_selectedValue').val(),
			provincia: $('#inputProvincia_selectedValue').val(),
			localidad: $('#inputLocalidad_selectedValue').val(),
			codPostal: $('#inputCodPostal').val()
		};
	}

	function cancelarEditar(){
		$('.btn-handler').hide();
		funciones.cancelarEditarClick();
	}

	function cambiarModo(modo){
		funciones.cambiarModo(modo);
		switch (modo){
			case 'inicio':
				funciones.cambiarTitulo('Documento gastos');
				calcularSugeridosEImpuestos();
				break;
			case 'buscar':
				$('.field-handler').disable();
				break;
			case 'editar':
				$('.btn-handler').show();
				$('.field-handler').enable();
				funciones.cambiarTitulo(tituloPrograma);
				$('.no-editable').disable();
				$('#inputProveedor').focus();
				break;
			case 'agregar':
				agregarTrImpuesto();
				$('.trRandom').hide();
				$('.trProveedor').show();
				$('.trFactura').hide();
				setTimeout(function(){
					var id = '#inputImpuesto' + idUiImpuestos;
					$(id).val(1);
					$(id).autoComplete();
					$(id).focus();
					$('#inputProveedor').focus();
				}, 100);
				$('#inputNetoGravado').val(0);
				$('#inputNetoNoGravado').val(0);
				$('#inputImporteTotal').val(0);
				$('.btn-handler').show();
				$('.field-handler').enable();
				$('#inputFechaDocumento').val(funciones.hoy());
				$('#inputFechaPeriodoFiscal').val(funciones.hoy());
				funciones.cambiarTitulo('Factura');
				$('#inputTipoDocumento2').change();
				break;
		}
	}
</script>

<div id='programaTitulo'></div>
<div id='programaContenido' class='customScroll'>
	<div id='divDatos'>
		<div id='divDatos1' class='fLeft pantalla'>
			<?php
			$tabla = new HtmlTable(array('cantRows' => 19, 'cantCols' => 2, 'id' => 'tablaDatos1', 'cellSpacing' => 3));
			$tabla->getRowCellArray($rows, $cells);

			$rows[1]->class = 'trProveedor';
			$rows[2]->class = 'trRandom';
			$rows[3]->class = 'trRandom';
			$rows[4]->class = 'trRandom';
			$rows[5]->class = 'trRandom';
			$rows[6]->class = 'trRandom';
			$rows[7]->class = 'trRandom';
			$rows[8]->class = 'trRandom';
			$rows[9]->class = 'trRandom';
			$rows[10]->class = 'trRandom';
			$rows[12]->class = 'trFactura';

			$cells[0][0]->style->width = '150px';
			$cells[0][0]->content = '<label>Es proveedor:</label>';
			$cells[0][1]->style->width = '250px';
			$cells[0][1]->content = '<select id="inputEsProveedor" class="textbox obligatorio inputForm no-editable w245" rel="tipoDocum" >';
			$cells[0][1]->content .= '<option value="S">Si</option>';
			$cells[0][1]->content .= '<option value="N">No</option>';
			$cells[0][1]->content .= '</select>';

			$cells[1][0]->content = '<label>Proveedor:</label>';
			$cells[1][1]->content = '<input id="inputProveedor" class="textbox obligatorio autoSuggestBox inputForm no-editable w200" name="Proveedor" rel="proveedor" /><label id="labelLetraFactura" class="letra">_</label>';

			$cells[2][0]->content = '<label>Razón social:</label>';
			$cells[2][1]->content = '<input id="inputRazonSocial" class="textbox obligatorio inputForm w230" rel="razonSocial" />';

			$cells[3][0]->content = '<label>CUIT:</label>';
			$cells[3][1]->content = '<input id="inputCuit" class="textbox obligatorio inputForm w230" validate="Cuit" rel="cuit" />';

			$cells[4][0]->content = '<label>Imputación:</label>';
			$cells[4][1]->content = '<input id="inputImputacionProveedor" class="textbox obligatorio autoSuggestBox inputForm w230" rel="imputacion" name="Imputacion" />';

			$cells[5][0]->content = '<label>Condición iva:</label>';
			$cells[5][1]->content = '<input id="inputCondicionIva" class="textbox obligatorio autoSuggestBox inputForm w160" name="CondicionIva" rel="condicionIva" />';
			$cells[5][1]->content .= ' <select id="inputLetra" class="textbox obligatorio inputForm w65" rel="tipoDocum" >';
			$cells[5][1]->content .= '<option value="A">A</option>';
			$cells[5][1]->content .= '<option value="B">B</option>';
			$cells[5][1]->content .= '<option value="C">C</option>';
			$cells[5][1]->content .= '</select>';

			$cells[6][0]->content = '<label>Calle:</label>';
			$cells[6][1]->content = '<input id="inputCalle" class="textbox inputForm w230" rel="calle" />';

			$cells[7][0]->content = '<label>Número calle:</label>';
			$cells[7][1]->content = '<input id="inputNumeroCalle" class="textbox inputForm w65" maxlength="5" validate="EnteroPositivo" rel="numero" />
											<label>Piso:</label>
											<input id="inputPiso" class="textbox inputForm w25" maxlength="3" rel="piso" />
											<label>Dpto:</label>
											<input id="inputDpto" class="textbox inputForm w25" maxlength="3" rel="dpto" />';

			$cells[8][0]->content = '<label>País:</label>';
			$cells[8][1]->content = '<input id="inputPais" class="textbox autoSuggestBox inputForm w230" name="Pais" rel="pais" />';

			$cells[9][0]->content = '<label>Provincia:</label>';
			$cells[9][1]->content = '<input id="inputProvincia" class="textbox autoSuggestBox inputForm w230" name="Provincia" linkedTo="inputPais,Pais" rel="provincia" />';

			$cells[10][0]->content = '<label>Localidad:</label>';
			$cells[10][1]->content = '<input id="inputLocalidad" class="textbox autoSuggestBox inputForm w135" name="Localidad" linkedTo="inputPais,Pais;inputProvincia,Provincia" rel="localidad" />
											<label>CP:</label>
											<input id="inputCodPostal" class="textbox inputForm w50" />';

			$cells[11][0]->content = '<label>Tipo Documento:</label>';
			$cells[11][1]->content = '<select id="inputTipoDocumento2" class="textbox obligatorio inputForm no-editable w245" rel="tipoDocum" >';
			$cells[11][1]->content .= '<option value="' . TiposDocumento::factura . '">Factura</option>';
			$cells[11][1]->content .= '<option value="' . TiposDocumento::notaDeCredito . '">Nota de Crédito</option>';
			$cells[11][1]->content .= '<option value="' . TiposDocumento::notaDeDebito . '">Nota de Débito</option>';
			$cells[11][1]->content .= '</select>';

			$cells[12][0]->content = '<label>Fac. a cancel.:</label>';
			$cells[12][1]->content = '<input id="inputFacturaCancelatoria" class="textbox autoSuggestBox inputForm no-editable w230" rel="facturaCancelatoria" name="FacturaProveedorEnConflicto"  />';

			$cells[13][0]->content = '<label>Número doc.:</label>';
			$cells[13][1]->content = '<input id="inputPuntoDeVenta" class="textbox obligatorio inputForm aRight w35" rel="puntoVenta" validate="Entero" maxlength="4" />  -
										<input id="inputNumeroDocumento" class="textbox obligatorio inputForm aRight w167" rel="nroDocumento" validate="Entero" maxlength="8" />';

			$cells[14][0]->content = '<label>Fecha documento:</label>';
			$cells[14][1]->content = '<input id="inputFechaDocumento" class="textbox obligatorio inputForm aRight w210" rel="fecha" validate="Fecha" />';

			$cells[15][0]->content = '<label>Fecha vencimiento:</label>';
			$cells[15][1]->content = '<input id="inputFechaVencimiento" class="textbox obligatorio inputForm aRight w210" rel="fechaVencimiento" validate="Fecha" />';

			$cells[16][0]->content = '<label>Fecha período fiscal:</label>';
			$cells[16][1]->content = '<input id="inputFechaPeriodoFiscal" class="textbox obligatorio inputForm aRight w210" rel="fechaPeriodoFiscal" validate="Fecha" />';

			$cells[17][0]->content = '<label>Observaciones:</label>';
			$cells[17][1]->content = '<textarea id="inputObservaciones" class="textbox inputForm w230" rel="observaciones" ></textarea>';

			$cells[18][0]->content = '<label>En conflicto:</label>';
			$cells[18][1]->content = '<input type="checkbox" id="inputDocumentoEnConflicto" class="textbox koiCheckbox inputForm" rel="documentoEnConflicto" >';

			$tabla->create();
			?>
		</div>
		<div id='divDetallesImpuestos' class='fRight pantalla w55p'>
			<?php
			$tabla = new HtmlTable(array('cantRows' => 4, 'cantCols' => 2, 'id' => 'tablaDatos1', 'cellSpacing' => 10));
			$tabla->getRowCellArray($rows, $cells);

			$cells[0][0]->content = '<label>Neto gravado:</label>';
			$cells[0][1]->content = '<input id="inputNetoGravado" class="textbox obligatorio inputForm aRight w74" rel="netoGravado" validate="DecimalPositivo" />
									<label><span id="spanNetoGravadoSugerido" class="s16">Sugerido: $ 0.00</span></label>';

			$cells[1][0]->content = '<label>Neto no gravado:</label>';
			$cells[1][1]->content = '<input id="inputNetoNoGravado" class="textbox obligatorio inputForm aRight w74" rel="netoNoGravado" validate="DecimalPositivo" />
									<label><span id="spanNetoNoGravadoSugerido" class="s16">Sugerido: $ 0.00</span></label>';

			$cells[2][0]->content = '<label>Total impuestos:</label>';
			$cells[2][1]->content = '<div id="divTotalImpuestos" class="inline s16" style="margin-left: 157px">$ 0.00</div>';

			$cells[3][0]->content = '<label>Importe total:</label>';
			$cells[3][1]->content = '<input id="inputImporteTotal" class="textbox obligatorio inputForm aRight w74" rel="importeTotal" validate="DecimalPositivo" />
									<label><span id="spanImporteTotalSugerido" class="s16">Sugerido: $ 0.00</span></label>';

			$tabla->create();
			?>
			<div id='divDetalles' class='well'>
				<div class='h25'>
					<label class='pull-left bold'>Detalle</label>
					<div class='btn-dropdown pull-right btn-handler'>
						<a id="liDetalle" class='btn' href='#'>
							<span class='btn-icon'>+</span>
							<span class='btn-text'>Nuevo detalle</span>
						</a>
					</div>
				</div>
				<div id='divTablaDetalles' class='h140 customScroll mTop10'></div>
			</div>
			<div id='divImpuestos' class='well mTop5'>
				<div class='h25'>
					<label class='pull-left bold'>Impuestos</label>
					<a id='btnMiniActualizarImpuestos' class='boton ayudaArticulos btn-handler' style='float: right' href='#' title='Actualizar impuestos'><img src="/img/botones/25/actualizar.gif" /></a>
					<a id='btnMiniAgregarImpuesto' class='boton ayudaArticulos btn-handler' style='float: right' href='#' title='Buscar'><img src="/img/botones/25/agregar.gif" /></a>
				</div>
				<div id='divTablaImpuestos' class='h90 customScroll mTop10'></div>
			</div>
		</div>
	</div>
</div>
<div id='programaPie'>
	<div id='filtro' class='draggableDialog hidden'>
		<div>
			<label for="inputBuscarNumeroDocumento" class='filtroBuscar'>Nro. documento:</label>
			<input id='inputBuscarNumeroDocumento' class='textbox obligatorio autoSuggestBox inputBuscar w230' name='DocumentoGastos' />
		</div>
		<div>
			<a id='btnMiniBuscar' class='boton' href='#' title='Buscar'><img src="/img/botones/25/buscar.gif" /></a>
		</div>
	</div>
	<div class='botonera'>
		<?php Html::echoBotonera(array('boton' => 'buscar', 'accion' => 'funciones.buscarClick();')); ?>
		<?php Html::echoBotonera(array('boton' => 'editar', 'accion' => 'funciones.editarClick();', 'permiso' => 'administracion/tesoreria/gastos/documento_gastos/editar/')); ?>
		<?php Html::echoBotonera(array('boton' => 'agregar', 'accion' => 'funciones.agregarClick();', 'permiso' => 'administracion/tesoreria/gastos/documento_gastos/agregar/')); ?>
		<?php Html::echoBotonera(array('boton' => 'borrar', 'accion' => 'funciones.borrarClick();', 'permiso' => 'administracion/tesoreria/gastos/documento_gastos/borrar/')); ?>
		<?php Html::echoBotonera(array('boton' => 'guardar', 'accion' => 'funciones.guardarClick();')); ?>
		<?php Html::echoBotonera(array('boton' => 'cancelar', 'accion' => 'funciones.cancelarBuscarClick();', 'id' => 'btnCancelarBuscar')); ?>
		<?php Html::echoBotonera(array('boton' => 'cancelar', 'accion' => 'cancelarEditar()', 'id' => 'btnCancelarEditar')); ?>
	</div>
</div>