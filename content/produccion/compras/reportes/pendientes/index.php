<?php
?>

<script type='text/javascript'>
	$(document).ready(function(){
		tituloPrograma = 'Reporte pendientes';
		$('.fechaEntrega').livequery(function() {
			$(this).click(function () {
				var tr = $(this).parents('tr:first');
				var idOrden = tr.find('td').eq(2).text();
				var numeroItem = tr.find('td:first').attr('rel');
				modificarFechaModal(idOrden, numeroItem);
			});
		});
		cambiarModo('inicio');
	});

	function limpiarScreen() {
		funciones.cambiarTitulo();
		$('#divReportePendientes').html('');
	}

	function getParams() {
		return {
			idProveedor: $('#inputProveedor_selectedValue').val(),
			idMaterial: $('#inputMaterial_selectedValue').val(),
			idColor: $('#inputColor_selectedValue').val(),
			fechaDesde: $('#inputFechaDesde').val(),
			fechaHasta: $('#inputFechaHasta').val(),
			modo: $('#inputModo').val()
		}
	}

	function buscar() {
		funciones.load($('#divReportePendientes'), funciones.controllerUrl('buscar', getParams()));
	}

	function xlsClick(){
		funciones.xlsClick(urlToExport('xls'));
	}

	function urlToExport(tipo){
		return funciones.controllerUrl('get' + (tipo == 'xls' ? 'Xls' : 'Pdf'), getParams());
	}

	function modificarFechaModal(idOrden, numeroItem) {
		var div = '<div class="h100 vaMiddle table-cell aLeft p10">' +
			'<table><tbody>' +
			'<tr><td class="w100"><label for="inputNuevaFecha">Nueva fecha:</label></td><td><input id="inputNuevaFecha" class="textbox obligatorio w190" validate="Fecha" /></td></tr>' +
			'<input id="inputOrderId" class="hidden" rel="id" />' +
			'</tbody></table>' +
			'</div>';
		var botones = [{value: 'Guardar', action: function() {modificarFecha(idOrden, numeroItem, $('#inputNuevaFecha').val());}}, {value: 'Cancelar', action: function(){$.jPopUp.close();}}];
		$.jPopUp.show(div, botones);
		$('#inputNuevaFecha').focus();
	}

	function modificarFecha(idOrden, numeroItem, nuevaFecha){
		var obj = {
			idOrden: idOrden,
			numeroItem: numeroItem,
			fecha: nuevaFecha
		};
		$.jPopUp.close();
		funciones.guardar(funciones.controllerUrl('editar'), obj, function() {
			var idComb = this.data.idOrdenDeCompra + '_' + this.data.numeroDeItem;
			var row = $('#row_' + idComb);
			row.find('.fechaEntrega').text(this.data.fechaEntrega);
			row.shine();
		}, null, null, false);
	}

	function cambiarModo(modo){
		funciones.cambiarModo(modo);
		switch (modo){
			case 'inicio':
				break;
			case 'buscar':
				break;
		}
	}
</script>

<div id='programaTitulo'></div>
<div id='programaContenido' class='customScroll'>
	<div id='divReportePendientes' class='w100p customScroll'></div>
</div>
<div id='programaPie'>
	<div id='filtro' class='draggableDialog hidden'>
		<div>
			<label for='inputProveedor' class='filtroBuscar'>Proveedor:</label>
			<input id='inputProveedor' class='textbox autoSuggestBox filtroBuscar w200' name='Proveedor' />
		</div>
		<div>
			<label for='inputMaterial' class='filtroBuscar'>Artículo:</label>
			<input id='inputMaterial' class='textbox autoSuggestBox filtroBuscar w200' name='Material' />
		</div>
		<div>
			<label for='inputColor' class='filtroBuscar'>Color:</label>
			<input id='inputColor' class='textbox autoSuggestBox filtroBuscar w200' name='ColorMateriaPrima' linkedTo="inputMaterial,Material" />
		</div>
		<div>
			<label for='inputFechaDesde' class='filtroBuscar'>Fecha desde:</label>
			<input id='inputFechaDesde' class='textbox filtroBuscar w180' to='inputFechaDesde' validate='Fecha' />
		</div>
		<div>
			<label for='inputFechaHasta' class='filtroBuscar'>Fecha hasta:</label>
			<input id='inputFechaHasta' class='textbox filtroBuscar w180' from='inputFechaHasta'' validate='Fecha' />
		</div>
		<div>
			<label class='filtroBuscar'>Modo:</label>
			<select id='inputModo' class='textbox filtroBuscar w200'>
				<option value='1'>Por material</option>
				<option value='2'>Por proveedor</option>
			</select>
		</div>
		<div>
			<a id='btnMiniBuscar' class='boton' href='#' title='Buscar'><img src="/img/botones/25/buscar.gif" /></a>
		</div>
	</div>
	<div class='botonera'>
		<?php Html::echoBotonera(array('boton' => 'buscar', 'accion' => 'funciones.buscarClick();')); ?>
		<?php Html::echoBotonera(array('boton' => 'guardar', 'accion' => 'funciones.guardarClick();')); ?>
		<?php Html::echoBotonera(array('boton' => 'xls', 'accion' => 'xlsClick();')); ?>
		<?php Html::echoBotonera(array('boton' => 'cancelar', 'accion' => 'funciones.cancelarBuscarClick();', 'id' => 'btnCancelarBuscar')); ?>
	</div>
</div>