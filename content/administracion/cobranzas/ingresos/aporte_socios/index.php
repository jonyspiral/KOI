<?php

?>

<script type='text/javascript'>
	$(document).ready(function(){
		tituloPrograma = 'Aporte de socios';
		$('.pluginImportes').importes({height: '250px', entradaSalida: 'E', botones: ['E', 'C', 'T']});
		$('#inputSocio').blur(function(){funciones.delay('getInfoSocio();');});
		cambiarModo('inicio');
	});

	function limpiarScreen(){
	}

	function getInfoSocio(){
		if ($('#inputSocio_selectedValue').val() != '') {
			$.getJSON('/content/administracion/cobranzas/ingresos/aporte_socios/getInfoSocio.php?idSocio=' + $('#inputSocio_selectedValue').val(), function(json){
				$('.pluginImportes').importes('config', {cuitLibrador: json.data.cuit, nombreLibrador: json.data.nombre});
			});
		}
	}

	function hayErrorGuardar(){
		if($('#inputCaja').val() == '' && $('#inputBuscar_selectedValue').val() == '')
			return 'Debe seleccionar una caja para operar';

		if ($('#inputSocio_selectedValue').val() == '')
			return 'Debe seleccionar un socio';

		if ($('#inputConcepto').val() == '')
			return 'Debe ingresar el concepto';

		return false;
	}

	function guardar(){
		var aux = ($('#inputBuscar_selectedValue').val() != '' ? 'editar' : 'agregar');
		var url = '/content/administracion/cobranzas/ingresos/aporte_socios/' + aux + '.php?';
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
		var url = '/content/administracion/cobranzas/ingresos/aporte_socios/buscar.php?id=' + $('#inputBuscar_selectedValue').val(),
			msgError = 'El aporte de socio "' + $('#inputBuscar_selectedName').val() + '" no existe.',
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
				break;
		}
	}

	function armoObjetoGuardar(){
		return {
			datos: {
				idSocio: $('#inputSocio_selectedValue').val(),
				concepto: $('#inputConcepto').val(),
				observaciones: $('#inputObservaciones').val(),
				idCaja_E: $('#inputCaja_selectedValue').val(),
				idAporte: $('#inputBuscar_selectedValue').val()
			},
			importes: $('.pluginImportes').importes('getJson')
		};
	}

	function pdfClick(){
		var url = '/content/administracion/cobranzas/ingresos/aporte_socios/getPdf.php';
		url += '?idAporte=' + $('#inputBuscar_selectedValue').val();
		funciones.pdfClick(url);
	}

	function borrar(){
		var msg = '¿Está seguro que desea borrar el aporte de socio número "' + $('#inputBuscar_selectedValue').val() + '"?',
			url = '/content/administracion/cobranzas/ingresos/aporte_socios/borrar.php';
		funciones.borrar(msg, url, armoObjetoBorrar());
	}

	function armoObjetoBorrar(){
		return {idAporte: $('#inputBuscar_selectedValue').val()};
	}
</script>

<div id='programaTitulo'></div>
<div id='programaContenido'>
	<div id='divDatosRecibo' class='pantalla customScroll'>
		<?php
		$tabla = new HtmlTable(array('cantRows' => 4, 'cantCols' => 2, 'id' => 'tablaDatos', 'cellSpacing' => 10));
		$tabla->getRowCellArray($rows, $cells);

		$rows[0]->class = 'trCaja';

		$cells[0][0]->style->width = '150px';
		$cells[0][0]->content = '<label>Caja destino:</label>';
		$cells[0][1]->style->width = '250px';
		$cells[0][1]->content = '<input id="inputCaja" class="textbox obligatorio autoSuggestBox inputForm w230" name="CajaPorUsuario" rel="caja" />';

		$cells[1][0]->content = '<label>Socio:</label>';
		$cells[1][1]->content = '<input id="inputSocio" class="textbox obligatorio autoSuggestBox inputForm w230" name="Socio" rel="socio" />';

		$cells[2][0]->content = '<label>En concepto de:</label>';
		$cells[2][1]->content = '<input id="inputConcepto" class="textbox obligatorio inputForm w230" rel="concepto" />';

		$cells[3][0]->content = '<label>Observaciones:</label>';
		$cells[3][1]->content = '<textarea id="inputObservaciones" class="textbox inputForm w230" rel="observaciones" ></textarea>';

		$tabla->create();
		?>
	</div>
	<div class="pluginImportes"></div>
</div>
<div id='programaPie'>
	<div id='filtro' class='draggableDialog hidden'>
		<div>
			<label for='inputBuscar' class='filtroBuscar'>Aporte de socio:</label>
			<input id='inputBuscar' class='textbox autoSuggestBox filtroBuscar w200' name='AporteSocio' />
			<div>
				<a id='btnMiniBuscar' class='boton' href='#' title='Buscar'><img src="/img/botones/25/buscar.gif" /></a>
			</div>
		</div>

	</div>
	<div class='botonera'>
		<?php Html::echoBotonera(array('boton' => 'buscar', 'accion' => 'funciones.buscarClick();')); ?>
		<?php Html::echoBotonera(array('boton' => 'editar', 'accion' => 'funciones.editarClick();', 'permiso' => 'administracion/cobranzas/ingresos/aporte_socios/editar/')); ?>
		<?php Html::echoBotonera(array('boton' => 'agregar', 'accion' => 'funciones.agregarClick();', 'permiso' => 'administracion/cobranzas/ingresos/aporte_socios/agregar/')); ?>
		<?php Html::echoBotonera(array('boton' => 'pdf', 'accion' => 'pdfClick();')); ?>
		<?php Html::echoBotonera(array('boton' => 'borrar', 'accion' => 'funciones.borrarClick();', 'permiso' => 'administracion/cobranzas/ingresos/aporte_socios/borrar/')); ?>
		<?php Html::echoBotonera(array('boton' => 'guardar', 'accion' => 'funciones.guardarClick();')); ?>
		<?php Html::echoBotonera(array('boton' => 'cancelar', 'accion' => 'funciones.cancelarBuscarClick();', 'id' => 'btnCancelarBuscar')); ?>
		<?php Html::echoBotonera(array('boton' => 'cancelar', 'accion' => 'funciones.cancelarEditarClick();', 'id' => 'btnCancelarEditar')); ?>
	</div>
</div>
