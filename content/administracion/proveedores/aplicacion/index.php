<?php
?>

<style>
#divAplicadorWrapper {
	float: left;
	height: 490px;
	padding-bottom: 10px;
}
#divDesaplicadorWrapper {
	height: 490px;
}
.box span {
	height: 120px;
	width: 130px;
}
tr.selected {
	color: white;
	background-color: #C55A29 !important;
}
</style>

<script type='text/javascript'>
	$(document).ready(function(){
		tituloPrograma = 'Aplicador/desaplicador de documentos proveedor';
		cambiarModo('inicio');
		$('#btnAplicar').click(function() {
			aplicar();
		});
		$('#btnAplicarAutomatico').click(function() {
			aplicarAutomatico();
		});
		<?php if (Funciones::get('idProveedor')) { ?>
		$('#inputBuscarProveedor, #inputBuscarProveedor_selectedValue').val(<?php echo Funciones::get('idProveedor'); ?>).blur();
		buscar();
		<?php } ?>
	});

	function limpiarScreen(){
		$('#tablaDebe, #tablaHaber').find('tbody').html('');
		$('#tablaDesaplicar').find('tbody').html('');
	}

	function buscar() {
		funciones.limpiarScreen();
		if ($('#inputBuscarProveedor_selectedValue').val() == '')
			return $('#inputBuscarProveedor').val('');
		var url = '/content/administracion/proveedores/aplicacion/buscar.php?';
			url += 'desde=' + $('#inputBuscarDesde').val();
			url += '&hasta=' + $('#inputBuscarHasta').val();
			url += '&modo=' + $('#inputModo').val();
			url += '&proveedor=' + $('#inputBuscarProveedor_selectedValue').val();
		var msgError = 'Ocurrió un error al buscar los documentos por ' + ($('#inputModo').val() == '2' ? 'des' : '') + 'aplicar',
			cbSuccess = function(json){
				$.showLoading();
				if ($('#inputModo').val() == '2') {
					//DESAPLICAR
					$('.aplicador').hide();
					$('.desaplicador').show();
					$(json.hijas).each(function(i, item) {
						var docDebe = (item.debe.tipoDocumento ? item.debe.tipoDocumento : '-') + ' - ' +
									  (item.debe.nroDocumento ? item.debe.nroDocumento : '-') + ' - "' +
									  (item.debe.letra ? item.debe.letra : '-') + '"';
						var docHaber = (item.haber.tipoDocumento ? item.haber.tipoDocumento : '-') + ' - ' +
									   (item.haber.nroDocumento ? item.haber.nroDocumento : '-') +
									   (item.haber.letra ? ' - "' + item.haber.letra + '"' : '');
						$('#tablaDesaplicar tbody').append(
							$('<tr>').attr('id', item.id)
								.append($('<td>').addClass('aCenter').text(item.fecha_debe ? item.fecha_debe : '-'))
								.append($('<td>').addClass('aCenter').text(docDebe))
								.append($('<td>').addClass('aCenter').text(item.fecha_haber ? item.fecha_haber : '-'))
								.append($('<td>').addClass('aCenter').text(docHaber))
								.append($('<td>').addClass('aRight pRight10').text(funciones.formatearMoneda(item.importe)))
								.append($('<td>').addClass('cPointer').addClass('aCenter').append(
									$('<span>').addClass('underline cPointer blue').text('Desaplicar').data('item', item).click(desaplicar)
								))
						)
					});
					$('#tablaHaber tbody tr').click(function(){
						$(this).addClass("selected").siblings().removeClass("selected");
					});
				} else {
					//APLICAR
					$('.desaplicador').hide();
					$('.aplicador').show();
					var pendienteHaber = 0;
					$(json.haber).each(function(i, item) {
						var combinado = item.id + '_' + item.empresa;
						pendienteHaber += funciones.toFloat(item.importePendiente);
						$('#tablaHaber tbody').append(
							$('<tr>').addClass('cPointer').attr('id', combinado).data('item', item)
								.append($('<td>').addClass('aCenter').text(item.fecha ? item.fecha : '-'))
								.append($('<td>').addClass('aCenter').text(item.tipoDocumento ? item.tipoDocumento : '-'))
								.append($('<td>').addClass('aRight pRight10').text(item.nroDocumento ? item.nroDocumento : '-'))
								.append($('<td>').addClass('aCenter').text(item.letra ? item.letra : '-'))
								.append($('<td>').addClass('aRight pRight10').text(funciones.formatearMoneda(item.importeTotal)))
								.append($('<td class="importePendiente">').addClass('aRight pRight10').text(funciones.formatearMoneda(item.importePendiente)))
						)
					});
					$('#saldoHaber').text(funciones.formatearMoneda(pendienteHaber));
					$('#tablaHaber tbody tr').click(function(){
						$(this).addClass("selected").siblings().removeClass("selected");
					});
					$('#tablaHaber tbody tr:first').click();
					var pendienteDebe = 0;
					$(json.debe).each(function(i, item) {
						var combinado = item.id + '_' + item.empresa;
						pendienteDebe += funciones.toFloat(item.importePendiente);
						$('#tablaDebe tbody').append(
							$('<tr>').addClass('cPointer').attr('id', combinado).data('item', item)
								.append($('<td>').addClass('aCenter').text(item.fecha ? item.fecha : '-'))
								.append($('<td>').addClass('aCenter').text(item.tipoDocumento ? item.tipoDocumento : '-'))
								.append($('<td>').addClass('aRight pRight10').text(item.nroDocumento ? item.nroDocumento : '-'))
								.append($('<td>').addClass('aCenter').text(item.letra ? item.letra : '-'))
								.append($('<td>').addClass('aRight pRight10').text(funciones.formatearMoneda(item.importeTotal)))
								.append($('<td class="importePendiente">').addClass('aRight pRight10').text(funciones.formatearMoneda(item.importePendiente)))
						)
					});
					$('#saldoDebe').text(funciones.formatearMoneda(pendienteDebe));
					$('#tablaDebe tbody tr').click(function(){
						$(this).addClass("selected").siblings().removeClass("selected");
					});
					$('#tablaDebe tbody tr:first').click();
					$('#saldoTotal').text(funciones.formatearMoneda(pendienteDebe - pendienteHaber));
					$.hideLoading();
				}
			};
		funciones.buscar(url, cbSuccess, msgError);
	}

	function aplicar() {
		if ($('#tablaDebe tbody tr.selected').length && $('#tablaHaber tbody tr.selected').length) {
			var url = '/content/administracion/proveedores/aplicacion/agregar.php?';
			$.showLoading();
			$.postJSON(url, armoObjetoGuardar(), function(json){
				$.hideLoading();
				switch (funciones.getJSONType(json)){
					case funciones.jsonNull:
					case funciones.jsonEmpty:
						$.error('Ocurrió un error al intentar aplicar');
						break;
					case funciones.jsonError:
						$.error(funciones.getJSONMsg(json));
						break;
					case funciones.jsonSuccess:
					case funciones.jsonObject:
						var data, item, difDebe, difHaber, combinado, row;
						item = json.data.debe;
						combinado = item.id + '_' + item.empresa;
						difDebe = $('#tablaDebe tbody tr.selected').data('item').importePendiente - item.importePendiente;
						row = $('#tablaDebe tbody tr#' + combinado);
						if (json.data.debe.importePendiente == 0) {
							row.remove();
							$('#tablaDebe tbody tr:first').click();
						} else {
							data = row.data('item');
							data.importePendiente = item.importePendiente;
							row.find('.importePendiente').data('item', data);
							row.find('.importePendiente').text(funciones.formatearMoneda(item.importePendiente));
						}
						item = json.data.haber;
						combinado = item.id + '_' + item.empresa;
						difHaber = $('#tablaHaber tbody tr.selected').data('item').importePendiente - item.importePendiente;
						row = $('#tablaHaber tbody tr#' + combinado);
						if (json.data.haber.importePendiente == 0) {
							row.remove();
							$('#tablaHaber tbody tr:first').click();
						} else {
							data = row.data('item');
							data.importePendiente = item.importePendiente;
							row.find('.importePendiente').data('item', data);
							row.find('.importePendiente').text(funciones.formatearMoneda(item.importePendiente));
						}
						var sd = funciones.limpiarNumero($('#saldoDebe').text());
						var sh = funciones.limpiarNumero($('#saldoHaber').text());
						$('#saldoDebe').text(funciones.formatearMoneda(sd - difDebe));
						$('#saldoHaber').text(funciones.formatearMoneda(sh - difHaber));
						$('#saldoTotal').text(funciones.formatearMoneda((sd - difDebe) - (sh - difHaber)));
						break;
				}
			});
		} else {
			$.alert('Para aplicar debe seleccionar un elemento del grupo DEBE y otro del grupo HABER');
		}
	}

	function aplicarAutomatico() {
		if ($('#tablaDebe tbody tr').length && $('#tablaHaber tbody tr').length) {
			var url = '/content/administracion/proveedores/aplicacion/editar.php?';
			$.showLoading();
			$.postJSON(url, armoObjectoGuardarAutomatico(), function(json){
				$.hideLoading();
				switch (funciones.getJSONType(json)){
					case funciones.jsonNull:
					case funciones.jsonEmpty:
						$.hideLoading();
						$.error('Ocurrió un error al intentar aplicar automáticamente');
						break;
					case funciones.jsonError:
						$.hideLoading();
						$.error(funciones.getJSONMsg(json));
						break;
					case funciones.jsonSuccess:
					case funciones.jsonObject:
						buscar();
				}
			});
		} else {
			$.alert('Para aplicar debe haber elementos en ambos grupos (DEBE y HABER)');
		}
	}

	function desaplicar(e) {
		var item = $(e.target).data('item');
		var url = '/content/administracion/proveedores/aplicacion/borrar.php?';
		$.showLoading();
		$.postJSON(url, {id: item.id}, function(json){
			$.hideLoading();
			switch (funciones.getJSONType(json)){
				case funciones.jsonNull:
				case funciones.jsonEmpty:
					$.error('Ocurrió un error al intentar desaplicar');
					break;
				case funciones.jsonError:
					$.error(funciones.getJSONMsg(json));
					break;
				case funciones.jsonSuccess:
				case funciones.jsonObject:
					$('#tablaDesaplicar tbody tr#' + json.data.hija.id).remove();
					break;
			}
		});
	}

	function armoObjetoGuardar() {
		var debe = $('#tablaDebe tbody tr.selected').data('item');
		var haber = $('#tablaHaber tbody tr.selected').data('item');
		return {
			debeId: debe.id,
			haberId: haber.id,
			tipoHaber: haber.tipoDocumento
		};
	}

	function armoObjectoGuardarAutomatico() {
		return {
			proveedor: $('#inputBuscarProveedor_selectedValue').val(),
			desde: $('#inputBuscarDesde').val(),
			hasta: $('#inputBuscarHasta').val()
		}
	}

	function cambiarModo(modo){
		funciones.cambiarModo(modo);
		switch (modo){
			case 'inicio':
				$('.pantalla').hide();
				break;
			case 'buscar':
				funciones.cambiarTitulo(tituloPrograma + ' - ' + $('#inputBuscarProveedor_selectedName').val());
				break;
		}
	}
</script>

<div id='programaTitulo'></div>
<div id='programaContenido'>
	<div class="pantalla">
		<div id='divAplicadorWrapper' class='aplicador w80p'>
			<div class='w100p'>
				<div id="divHaber">
					<h2 class="aLeft">Haber (OP / NCR)</h2>
					<div class="h180 customScroll">
						<table id="tablaHaber" class="registrosAlternados w100p">
							<thead class="tableHeader">
								<tr class="tableRow">
									<th class="w15p cornerL5">Fecha</th>
									<th class="w15p bLeftWhite">Tipo doc</th>
									<th class="w20p bLeftWhite">Número</th>
									<th class="w10p bLeftWhite">Letra</th>
									<th class="w20p bLeftWhite">Total</th>
									<th class="cornerR5 w20p bLeftWhite">Pendiente</th>
								</tr>
							</thead>
							<tbody>
							</tbody>
						</table>
					</div>
				</div>
				<div id="divDebe">
					<h2 class="aLeft">Debe (FAC / NDB)</h2>
					<div class="h180 customScroll">
						<table id="tablaDebe" class="registrosAlternados w100p">
							<thead class="tableHeader">
								<tr class="tableRow">
									<th class="w15p cornerL5">Fecha</th>
									<th class="w15p bLeftWhite">Tipo doc</th>
									<th class="w20p bLeftWhite">Número</th>
									<th class="w10p bLeftWhite">Letra</th>
									<th class="w20p bLeftWhite">Total</th>
									<th class="w20p bLeftWhite cornerR5">Pendiente</th>
								</tr>
							</thead>
							<tbody>
							</tbody>
						</table>
					</div>
				</div>
			</div>
		</div>
		<div class='aplicador w18p fRight'>
			<div id="btnAplicar" class="box fLeft corner5 p10 calibri s16 bold m10 aCenter white cPointer bOrange">
				<span class="vaMiddle table-cell">Aplicar</span>
			</div>
			<div id="btnAplicarAutomatico" class="box fLeft corner5 p10 calibri s16 bold m10 aCenter white cPointer bOrange">
				<span class="vaMiddle table-cell">Aplicación automática</span>
			</div>
			<div class="w100p fLeft s17">
				<p>Haber: <span id="saldoHaber" class="bold fRight"></span></p>
				<p>Debe: <span id="saldoDebe" class="bold fRight"></span></p>
				<p>Saldo: <span id="saldoTotal" class="bold fRight"></span></p>
			</div>
		</div>
		<div id="divDesaplicadorWrapper" class='desaplicador w100p customScroll'>
			<div>
				<table id="tablaDesaplicar" class="registrosAlternados w100p">
					<thead class="tableHeader">
						<tr class="tableRow">
							<th class="w15p cornerL5">Fecha debe</th>
							<th class="w25p bLeftWhite">Documento debe</th>
							<th class="w15p bLeftWhite">Fecha haber</th>
							<th class="w25p bLeftWhite">Documento haber</th>
							<th class="w15p bLeftWhite">Importe aplicado</th>
							<th class="w5p bLeftWhite cornerR5">Desaplicar</th>
						</tr>
					</thead>
					<tbody>
					</tbody>
				</table>
			</div>
		</div>
	</div>
</div>
<div id='programaPie'>
	<div id='filtro' class='hidden draggableDialog'>
		<div>
			<label class='filtroBuscar'>Proveedor:</label>
			<input id='inputBuscarProveedor' class='textbox autoSuggestBox filtroBuscar w190 obligatorio' name='Proveedor' alt='' />
		</div>
		<div>
			<label class='filtroBuscarModo'>Modo:</label>
			<select id='inputModo' class='textbox filtroBuscar w190'>
				<option value='1'>Aplicar</option>
				<option value='2'>Desaplicar</option>
			</select>
		</div>
		<div>
			<label class='filtroBuscar'>Desde:</label>
			<input id='inputBuscarDesde' class='textbox filtroBuscar w170' to='inputBuscarHasta' alt='' validate="Fecha" />
		</div>
		<div>
			<label class='filtroBuscar'>Hasta:</label>
			<input id='inputBuscarHasta' class='textbox filtroBuscar w170' from='inputBuscarHasta' alt='' validate="Fecha"/>
		</div>
		<div>
			<a id='btnMiniBuscar' class='boton' href='#' title='Buscar'><img src="/img/botones/25/buscar.gif" /></a>
		</div>
	</div>
	<div class='botonera'>
		<?php Html::echoBotonera(array('boton' => 'buscar', 'accion' => 'funciones.buscarClick();')); ?>
		<?php Html::echoBotonera(array('boton' => 'cancelar', 'accion' => 'funciones.cancelarBuscarClick();', 'id' => 'btnCancelarBuscar')); ?>
	</div>
</div>