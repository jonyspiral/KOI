<?php
?>

<style>
	.tableFontSize td, th{
		font-size: 13px !important;
	}
</style>

<script type='text/javascript'>
	$(document).ready(function(){
		tituloPrograma = 'Reporte de pendientes en órdenes de compra';
		cambiarModo('inicio');
	});

	function limpiarScreen(){
		funciones.cambiarTitulo();
		$('#divPendienteOrdenesDeCompra').html('');
	}

	function buscar() {
		funciones.limpiarScreen();
		var url = funciones.controllerUrl('buscar', getFiltros());
		$.showLoading();
		$.get(url, function(result) {
			try {
				var json = $.parseJSON(result);
				switch (funciones.getJSONType(json)) {
					case funciones.jsonNull:
					case funciones.jsonError:
						$.error('Ocurrió un error al intentar realizar la consulta');
						break;
					case funciones.jsonInfo:
						$.info(funciones.getJSONMsg(json));
						break;
				}
			} catch (ex) {
				$('#divPendienteOrdenesDeCompra').html(result);
				$('.acordeon').acordeon({fixedHeight: false});
				cambiarModo('buscar');
			}
			$.hideLoading();
		});
	}

	function pdfClick(){
		funciones.pdfClick(funciones.controllerUrl('getPdf', getFiltros()));
	}

	function getFiltros(){
		return {
			fechaDesde: $('#inputFechaDesde').val(),
			fechaHasta: $('#inputFechaHasta').val(),
			orderBy: $('#inputOrdenarPor').val(),
			idProveedor: $('#inputProveedor_selectedValue').val(),
			idLoteDeProduccion: $('#inputLoteDeProduccion_selectedValue').val()
		}
	}

	function cambiarModo(modo){
		funciones.cambiarModo(modo);
		switch (modo){
			case 'inicio':
			case 'buscar':
				break;
		}
	}
</script>

<div id='programaTitulo'></div>
<div id='programaContenido' class='customScroll'>
	<div class="pantalla">
		<table cellspacing="1" border="0" style="width: 99%">
			<thead class="tableHeader">
				<tr class="tableRow">
					<th class="tableHeader" title="Fecha de emisión" style="width: 10%; ">F. emisión</th>
					<th class="tableHeader" title="Número de órden de compra" style="width: 10%; ">Número</th>
					<th class="tableHeader" title="Proveedor" style="width: 60%; ">Proveedor</th>
					<th class="tableHeader" title="Cantidad detalles" style="width: 10%; ">Cant. detalles</th>
					<th class="tableHeader" title="Importe" style="width: 10%; ">Importe</th>
				</tr>
			</thead>
		</table>
		<div id='divPendienteOrdenesDeCompra' class='w100p customScroll acordeon h480'>
			<?php // TABLOTA ?>
		</div>
	</div>
</div>
<div id='programaPie'>
	<div id='filtro' class='draggableDialog hidden'>
		<div>
			<label for='inputProveedor' class='filtroBuscar'>Proveedor:</label>
			<input id='inputProveedor' class='textbox autoSuggestBox filtroBuscar w200' name='Proveedor' />
		</div>
		<div>
			<label for='inputLoteDeProduccion' class='filtroBuscar'>Lote:</label>
			<input id='inputLoteDeProduccion' class='textbox autoSuggestBox filtroBuscar w200' name='LoteDeProduccion' />
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
			<label for='inputOrdenarPor' class='filtroBuscar'>Ordenar por:</label>
			<select id='inputOrdenarPor' class='textbox filtroBuscar w200'>
				<option value='fecha_emision'>Fecha emisión</option>
				<option value='cod_proveedor'>Proveedor</option>
			</select>
		</div>
		<div>
			<a id='btnMiniBuscar' class='boton' href='#' title='Buscar'><img src="/img/botones/25/buscar.gif" /></a>
		</div>
	</div>
	<div class='botonera'>
		<?php Html::echoBotonera(array('boton' => 'buscar', 'accion' => 'funciones.buscarClick();')); ?>
		<?php Html::echoBotonera(array('boton' => 'guardar', 'accion' => 'funciones.guardarClick();')); ?>
		<?php Html::echoBotonera(array('boton' => 'pdf', 'accion' => 'pdfClick();')); ?>
		<?php Html::echoBotonera(array('boton' => 'cancelar', 'accion' => 'funciones.cancelarBuscarClick();', 'id' => 'btnCancelarBuscar')); ?>
	</div>
</div>