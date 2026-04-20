<?php

?>

<script type='text/javascript'>
	$(document).ready(function(){
		tituloPrograma = 'Prestamo';
		$('.pluginImportes').importes({height: '250px', entradaSalida: 'E', botones: ['E']});
		cambiarModo('inicio');
	});

	function limpiarScreen(){
	}

	function hayErrorGuardar(){
		if($('#inputCaja').val() == '' && $('#inputBuscar_selectedValue').val() == '')
			return 'Debe seleccionar una caja para operar';

		if($('#inputFecha').val() == '')
			return 'Debe seleccionar una caja para operar';

		return false;
	}

	function guardar(){
		var aux = ($('#inputBuscar_selectedValue').val() != '' ? 'editar' : 'agregar');
		var url = '/content/administracion/cobranzas/ingresos/prestamo/' + aux + '.php?';
		try {
			funciones.guardar(url, armoObjetoGuardar());
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
		var url = '/content/administracion/cobranzas/ingresos/prestamo/buscar.php?idPrestamo=' + $('#inputBuscar_selectedValue').val(),
			msgError = 'El prestamo "' + $('#inputBuscar_selectedName').val() + '" no existe.',
			cbSuccess = function(json){
				$('#tablaDatos').loadJSON(json);
				$('.pluginImportes').importes('load', json.importePorOperacion.detalle);
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
				break;
			case 'agregar':
				$('.trCaja').show();
				$('#inputCaja').focus();
				$('#inputFecha').val(funciones.hoy());
				break;
		}
	}

	function armoObjetoGuardar(){
		return {
			datos: {
				fechaDocumento: $('#inputFecha').val(),
				observaciones: $('#inputObservaciones').val(),
				idCaja_E: $('#inputCaja_selectedValue').val(),
				idPrestamo: $('#inputBuscar_selectedValue').val()
			},
			importes: $('.pluginImportes').importes('getJson')
		};
	}

	function borrar(){
		var msg = '¿Está seguro que desea borrar el prestamo número "' + $('#inputBuscar_selectedValue').val() + '"?',
			url = '/content/administracion/cobranzas/ingresos/prestamo/borrar.php';
		funciones.borrar(msg, url, armoObjetoBorrar());
	}

	function armoObjetoBorrar(){
		return {idPrestamo: $('#inputBuscar_selectedValue').val()};
	}
</script>

<div id='programaTitulo'></div>
<div id='programaContenido'>
	<div id='divDatosRecibo' class='pantalla customScroll'>
		<?php
		$tabla = new HtmlTable(array('cantRows' => 3, 'cantCols' => 2, 'id' => 'tablaDatos', 'cellSpacing' => 10));
		$tabla->getRowCellArray($rows, $cells);

		$rows[0]->class = 'trCaja';

		$cells[0][0]->style->width = '150px';
		$cells[0][0]->content = '<label>Caja destino:</label>';
		$cells[0][1]->style->width = '250px';
		$cells[0][1]->content = '<input id="inputCaja" class="textbox obligatorio autoSuggestBox inputForm w230" name="CajaBanco" rel="caja" />';

		$cells[1][0]->content = '<label>Fecha:</label>';
		$cells[1][1]->content = '<input id="inputFecha" class="textbox obligatorio inputForm w210" rel="fecha" validate="Fecha" />';

		$cells[2][0]->content = '<label>Observaciones:</label>';
		$cells[2][1]->content = '<textarea id="inputObservaciones" class="textbox inputForm w230" rel="observaciones" ></textarea>';

		$tabla->create();
		?>
	</div>
	<div class="pluginImportes"></div>
</div>
<div id='programaPie'>
	<div id='filtro' class='draggableDialog hidden'>
		<div>
			<label for='inputBuscar' class='filtroBuscar'>Prestamo:</label>
			<input id='inputBuscar' class='textbox autoSuggestBox filtroBuscar w200' name='Prestamo' />
			<div>
				<a id='btnMiniBuscar' class='boton' href='#' title='Buscar'><img src="/img/botones/25/buscar.gif" /></a>
			</div>
		</div>

	</div>
	<div class='botonera'>
		<?php Html::echoBotonera(array('boton' => 'buscar', 'accion' => 'funciones.buscarClick();')); ?>
		<?php Html::echoBotonera(array('boton' => 'editar', 'accion' => 'funciones.editarClick();', 'permiso' => 'administracion/cobranzas/ingresos/prestamo/editar/')); ?>
		<?php Html::echoBotonera(array('boton' => 'agregar', 'accion' => 'funciones.agregarClick();', 'permiso' => 'administracion/cobranzas/ingresos/prestamo/agregar/')); ?>
		<?php Html::echoBotonera(array('boton' => 'borrar', 'accion' => 'funciones.borrarClick();', 'permiso' => 'administracion/cobranzas/ingresos/prestamo/borrar/')); ?>
		<?php Html::echoBotonera(array('boton' => 'guardar', 'accion' => 'funciones.guardarClick();')); ?>
		<?php Html::echoBotonera(array('boton' => 'cancelar', 'accion' => 'funciones.cancelarBuscarClick();', 'id' => 'btnCancelarBuscar')); ?>
		<?php Html::echoBotonera(array('boton' => 'cancelar', 'accion' => 'funciones.cancelarEditarClick();', 'id' => 'btnCancelarEditar')); ?>
	</div>
</div>
