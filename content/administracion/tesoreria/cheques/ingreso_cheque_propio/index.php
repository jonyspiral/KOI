<?php

?>

<script type='text/javascript'>

	$(document).ready(function(){
		tituloPrograma = 'Ingreso cheque propio';
		$('.pluginImportes').importes({height: '250px', entradaSalida: 'E', botones: ['C'], chequePropio: true});
		$('#inputObservaciones').blur(function(){
			$('.pluginImportes').importes('show').find('.btn-dropdown .btn:first').focus();
		});
		cambiarModo('inicio');
	});

	function hayErrorGuardar(){
		if($('#inputCaja_selectedValue').val() == '') {
			return 'Debe seleccionar una caja para operar';
		}

		return false;
	}

	function guardar(){
		var aux = ($('#inputBuscarNumero_selectedValue').val() != '' ? 'editar' : 'agregar');
		var url = '/content/administracion/tesoreria/cheques/ingreso_cheque_propio/' + aux + '.php?';
		try {
			funciones.guardar(url, armoObjetoGuardar());
		} catch (ex) {
			$.error(ex);
		}
	}

	function cambiarModo(modo){
		funciones.cambiarModo(modo);
		$('.pluginImportes').importes('cambiarModo', modo);
		switch (modo){
			case 'inicio':
				$('#inputCaja').focus();
				funciones.agregarClick();
				break;
			case 'agregar':
				$('#inputCaja').focus();
				break;
		}
	}

	function armoObjetoGuardar(){
		return {
			datos: {
				observaciones: $('#inputObservaciones').val(),
				idCaja_E: $('#inputCaja_selectedValue').val()
			},
			importes: $('.pluginImportes').importes('getJson')
		};
	}
</script>

<div id='programaTitulo'></div>
<div id='programaContenido'>
	<div id='divDatosRecibo' class='pantalla customScroll'>
		<?php
			$tabla = new HtmlTable(array('cantRows' => 2, 'cantCols' => 2, 'id' => 'tablaDatos2', 'cellSpacing' => 10));
			$tabla->getRowCellArray($rows, $cells);

			$cells[0][0]->style->width = '150px';
			$cells[0][0]->content = '<label>Caja destino:</label>';
			$cells[0][1]->style->width = '250px';
			$cells[0][1]->content = '<input id="inputCaja" class="textbox obligatorio autoSuggestBox inputForm w230" name="CajaPorUsuario" rel="caja" />';

			$cells[1][0]->content = '<label>Observaciones:</label>';
			$cells[1][1]->content = '<textarea id="inputObservaciones" class="textbox inputForm w230" ></textarea>';

			$tabla->create();
		?>
	</div>
	<div class="pluginImportes"></div>
</div>
<div id='programaPie'>
	<div id='filtro' class='draggableDialog hidden'>
		<div><!--campos de busqueda  -->
			<label for='inputBuscarNumero' class='filtroBuscar'>Cheque:</label>
			<input id='inputBuscarNumero' class='textbox autoSuggestBox filtroBuscar w200' name='Recibo' />
			<div>
				<a id='btnMiniBuscar' class='boton' href='#' title='Buscar'><img src="/img/botones/25/buscar.gif" /></a>
			</div>
		</div>

	</div>
	<div class='botonera'>
		<?php Html::echoBotonera(array('boton' => 'agregar', 'accion' => 'funciones.agregarClick();', 'permiso' => 'administracion/tesoreria/cheques/ingreso_cheque_propio/agregar/')); ?>
		<?php Html::echoBotonera(array('boton' => 'guardar', 'accion' => 'funciones.guardarClick();')); ?>
		<?php Html::echoBotonera(array('boton' => 'cancelar', 'accion' => 'funciones.cancelarBuscarClick();', 'id' => 'btnCancelarBuscar')); ?>
		<?php Html::echoBotonera(array('boton' => 'cancelar', 'accion' => 'funciones.cancelarEditarClick();', 'id' => 'btnCancelarEditar')); ?>
	</div>
</div>
