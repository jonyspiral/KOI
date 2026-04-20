<?php

?>

<script type='text/javascript'>
	$(document).ready(function(){
		tituloPrograma = 'Recibos';
		//eventos click
		$('#rdCliente').click(function(){
			$('.trOtro').hide();
			$('.trCliente').show();
			$('.trImputacionLabel').show();
			$('.trImputacionInput').hide();
			$('#inputImputacion').val('1131100').autoComplete();
		});
		$('#rdOtro').click(function(){
			$('.trOtro').show();
			$('.trCliente').hide();
			$('.trImputacionLabel').hide();
			$('.trImputacionInput').show();
		});
		$('.pluginImportes').importes({height: '250px', entradaSalida: 'E', idInputCaja: 'inputCaja', botones: ['E', 'C', 'T', 'S'], saveCallback: refreshImportes, removeCallback: refreshImportes});
		$('#inputCliente').blur(function(){funciones.delay('getInfoCliente();');});
		$('#inputObservaciones').blur(function(){
			$('.pluginImportes').importes('show').find('.btn-dropdown .btn:first').focus();
		});
		cambiarModo('agregar');
	});

	function limpiarScreen(){
		$('.pluginImportes').importes('clean');
		$('#importeNeto').text(funciones.formatearMoneda(0));
		$('#importeTotal').text(funciones.formatearMoneda(0));
	}

	function refreshImportes() {
		var neto = $('.pluginImportes').importes('getImporte');
		$('#importeNeto').text(funciones.formatearMoneda(neto));
		$('#importeTotal').text(funciones.formatearMoneda(neto));
	}

	function getInfoCliente(){
		if ($('#inputCliente_selectedValue').val() != '') {
			$.getJSON('/content/administracion/cobranzas/ingresos/recibos/getInfoCliente.php?idCliente=' + $('#inputCliente_selectedValue').val(), function(json){
				$('.pluginImportes').importes('config', {cuitLibrador: json.data.cuit, nombreLibrador: json.data.nombre});
			});
		}
	}

	function hayErrorGuardar(){
		if($('#inputCaja').val() == '' && $('#inputBuscar_selectedValue').val() == '')
			return 'Debe seleccionar una caja para operar';

		if ($('#inputNumeroReciboProvisorio').val() == '')
			return 'Debe ingresar un número de recibo provisorio';

		if($('#radioTipoRecibo').radioVal() == 'CD'){
			if ($('#inputCliente').val() == '')
				return 'Debe ingresar un cliente';
		}

		if($('#radioTipoRecibo').radioVal() == 'OI'){
			if ($('#inputRecibidoDe').val() == '')
				return 'Debe ingresar recibido de';
			if ($('#inputImputacion_selectedValue').val() == '')
				return 'Debe ingresar una imputación';
		}

		return false;
	}

	function guardar(){
		var aux = ($('#inputBuscar_selectedValue').val() != '' ? 'editar' : 'agregar');
		var url = '/content/administracion/cobranzas/ingresos/recibos/' + aux + '.php?';
		try {
			funciones.guardar(url, armoObjetoGuardar(), function() {
				funciones.limpiarScreen();
				cambiarModo('buscar');
				$('#inputBuscar, #inputBuscar_selectedValue').val(this.data.numero).blur();
				funciones.newWindow('/administracion/cobranzas/aplicacion/?idCliente=' + this.data.cliente.id);
			});
		} catch (ex) {
			$.error(ex);
		}
	}

	function buscar(idBuscar) {
		funciones.limpiarScreen();
		if (typeof idBuscar !== 'undefined'){
			$('#inputBuscar').val(idBuscar).autoComplete();
			return $('#inputBuscar').val(idBuscar).blur();
		}
		if ($('#inputBuscar_selectedValue').val() == '')
			return $('#inputBuscar').val('');
		var url = '/content/administracion/cobranzas/ingresos/recibos/buscar.php?id=' + $('#inputBuscar_selectedValue').val(),
			msgError = 'El Recibo "' + $('#inputBuscar_selectedName').val() + '" no existe.',
			cbSuccess = function(json){
				$('#tablaDatos').loadJSON(json);
				$('#inputImputacion').val(json.imputacion.id).autoComplete();
				$('.pluginImportes').importes('load', json.importePorOperacion.detalle);
				getInfoCliente();
				refreshImportes();
			};
		funciones.buscar(url, cbSuccess, msgError);
	}

	function cambiarModo(modo){
		funciones.cambiarModo(modo);
		$('.pluginImportes').importes('cambiarModo', modo);
		switch (modo){
			case 'inicio':
				break;
			case 'buscar':
				$('.trCaja').hide();
				break;
			case 'editar':
				$('.trCaja').hide();
				$('#radioGroupTipoRecibo').focus();
				break;
			case 'agregar':
				$('.trCaja').show();
				$('#inputFechaDocumento').val(funciones.hoy());
				$('#inputImputacion').val('1131100').autoComplete();
				$('#inputCaja').focus();
				break;
		}
	}

	function armoObjetoGuardar(){
		return {
			datos: {
				tipoRecibo: $('#radioTipoRecibo').radioVal(),
				idCliente: $('#inputCliente_selectedValue').val(),
				idImputacion: $('#inputImputacion_selectedValue').val(),
				recibidoDe: $('#inputRecibidoDe').val(),
				numeroReciboProvisorio: $('#inputNumeroReciboProvisorio').val(),
				observaciones: $('#inputObservaciones').val(),
				idCaja_E: $('#inputCaja_selectedValue').val(),
				idRecibo: $('#inputBuscar_selectedValue').val()
			},
			importes: $('.pluginImportes').importes('getJson')
		};
	}

	function borrar(){
		var msg = '¿Está seguro que desea borrar el recibo número "' + $('#inputBuscar_selectedValue').val() + '"?',
			url = '/content/administracion/cobranzas/ingresos/recibos/borrar.php';
		funciones.borrar(msg, url, armoObjetoBorrar());
	}

	function armoObjetoBorrar(){
		return {idRecibo: $('#inputBuscar_selectedValue').val()};
	}

	function pdfClick(){
		var url = '/content/administracion/cobranzas/ingresos/recibos/getPdf.php';
		url += '?id=' + $('#inputBuscar_selectedValue').val();
		funciones.pdfClick(url);
	}
</script>

<div id='programaTitulo'></div>
<div id='programaContenido'>
	<div id='divDatosRecibo' class='fLeft pantalla'>
		<?php
		$tabla = new HtmlTable(array('cantRows' => 8, 'cantCols' => 2, 'id' => 'tablaDatos', 'cellSpacing' => 10));
		$tabla->getRowCellArray($rows, $cells);

		$rows[1]->class = 'trCaja';
		$rows[2]->class = 'trCliente';
		$rows[3]->class = 'trOtro';
		$rows[4]->class = 'trImputacionInput';
		$rows[5]->class = 'trImputacionLabel';

		$cells[0][0]->style->width = '150px';
		$cells[0][0]->content = '<label>Tipo Recibo:</label>';
		$cells[0][1]->style->width = '250px';
		$cells[0][1]->content = '<div id="radioTipoRecibo" class="customRadio" default="rdCliente">
									<input id="rdCliente" class="textbox inputTipoRecibo" type="radio" name="radioGroupTipoRecibo" value="CD" rel="tipoOperacion" /><label for="rdCliente">Cliente</label>' .
								'<input id="rdOtro" class="textbox inputTipoRecibo" type="radio" name="radioGroupTipoRecibo" value="OI" rel="tipoOperacion" /><label for="rdOtro">Otro</label>';

		$cells[1][0]->content = '<label>Caja destino:</label>';
		$cells[1][1]->content = '<input id="inputCaja" class="textbox obligatorio autoSuggestBox inputForm w230" name="CajaPorUsuario" rel="caja" />';

		$cells[2][0]->content = '<label>Cliente:</label>';
		$cells[2][1]->content = '<input id="inputCliente" class="textbox obligatorio autoSuggestBox inputForm w230" name="ClienteTodos" rel="cliente" />';

		$cells[3][0]->content = '<label>Recibido de:</label>';
		$cells[3][1]->content = '<input id="inputRecibidoDe" class="textbox obligatorio inputForm w230" rel="recibidoDe" />';

		$cells[4][0]->content = '<label>Imputación:</label>';
		$cells[4][1]->content = '<input id="inputImputacion" class="textbox obligatorio autoSuggestBox inputForm w230" name="Imputacion" rel="imputacion" />';

		$cells[5][0]->content = '<label>Imputación:</label>';
		$cells[5][1]->content = '<label>1131100 - Deudores por Ventas</label>';

		$cells[6][0]->content = '<label>Nro recibo provisorio:</label>';
		$cells[6][1]->content = '<input id="inputNumeroReciboProvisorio" class="textbox obligatorio inputForm w230" validate="Entero" maxlength="8" rel="numeroReciboProvisorio" />';

		$cells[7][0]->content = '<label>Observaciones:</label>';
		$cells[7][1]->content = '<textarea id="inputObservaciones" class="textbox inputForm w230" rel="observaciones" ></textarea>';

		$tabla->create();//impresion
		?>
	</div>
	<div class='fRight pantalla w50p'>
		<div class='well'>
			<?php
			$tabla = new HtmlTable(array('cantRows' => 2, 'cantCols' => 2, 'id' => 'tablaTotales', 'class' => 'w100p', 'cellSpacing' => 10));
			$tabla->getRowCellArray($rows, $cells);

			$cells[0][0]->class = 'w50p';
			$cells[0][0]->content = '<label>Importe neto:</label>';
			$cells[0][1]->class = 'w50p aRight';
			$cells[0][1]->content = '<label id="importeNeto">$ 0.00</label>';

			$cells[1][0]->class = 'w50p';
			$cells[1][0]->content = '<label class="bold">Importe total:</label>';
			$cells[1][1]->class = 'w50p aRight';
			$cells[1][1]->content = '<label id="importeTotal" class="bold">$ 0.00</label>';

			$tabla->create();
			?>
		</div>
	</div>
	<div class="pluginImportes"></div>
</div>
<div id='programaPie'>
	<div id='filtro' class='draggableDialog hidden'>
		<div>
			<label for='inputBuscarCliente' class='filtroBuscar'>Cliente:</label>
			<input id='inputBuscarCliente' class='textbox autoSuggestBox filtroBuscar w200' name='ClienteTodos' />
		</div>
		<div>
			<label for='inputBuscar' class='filtroBuscar'>Recibo:</label>
			<input id='inputBuscar' class='textbox autoSuggestBox filtroBuscar w200' name='Recibo' linkedTo='inputBuscarCliente,Cliente' />
		</div>
		<div>
			<a id='btnMiniBuscar' class='boton' href='#' title='Buscar'><img src="/img/botones/25/buscar.gif" /></a>
		</div>
	</div>
	<div class='botonera'>
		<?php Html::echoBotonera(array('boton' => 'buscar', 'accion' => 'funciones.buscarClick();')); ?>
		<?php Html::echoBotonera(array('boton' => 'editar', 'accion' => 'funciones.editarClick();', 'permiso' => 'administracion/cobranzas/ingresos/recibos/editar/')); ?>
		<?php Html::echoBotonera(array('boton' => 'agregar', 'accion' => 'funciones.agregarClick();', 'permiso' => 'administracion/cobranzas/ingresos/recibos/agregar/')); ?>
		<?php Html::echoBotonera(array('boton' => 'pdf', 'accion' => 'pdfClick();')); ?>
		<?php Html::echoBotonera(array('boton' => 'borrar', 'accion' => 'funciones.borrarClick();', 'permiso' => 'administracion/cobranzas/ingresos/recibos/borrar/')); ?>
		<?php Html::echoBotonera(array('boton' => 'guardar', 'accion' => 'funciones.guardarClick();')); ?>
		<?php Html::echoBotonera(array('boton' => 'cancelar', 'accion' => 'funciones.cancelarBuscarClick();', 'id' => 'btnCancelarBuscar')); ?>
		<?php Html::echoBotonera(array('boton' => 'cancelar', 'accion' => 'funciones.cancelarEditarClick();', 'id' => 'btnCancelarEditar')); ?>
	</div>
</div>
