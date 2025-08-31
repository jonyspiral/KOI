<?php

?>

<script type='text/javascript'>
	var retieneGanancias = false;

	$(document).ready(function(){
		tituloPrograma = 'Retiro de Socio';
		$('.pluginImportes').importes({height: '250px', entradaSalida: 'S', idInputCaja: 'inputCaja', botones: ['E', 'C', 'T', 'G']});
		//eventos click
		$('#rdProveedor').click(function(){
			$('#inputProveedor').val('');
			$('#inputProveedor_selectedValue').val('');
		});
		$('#rdOtro').click(function(){
			$('#rdRetieneGananciasN').click();
		});
		$('#btnToggleAyuda').click(function(){
			$('#toggleAyuda').slideToggle();
		});
		$('#inputObservaciones').blur(function(){
			$('.pluginImportes').importes('show').find('.btn-dropdown .btn:first').focus();
		});
		cambiarModo('inicio');
	});

	function limpiarScreen(){
		$('.pluginImportes').importes('clean');
		$('#importeNeto').text(funciones.formatearMoneda(0));
		$('#importeTotal').text(funciones.formatearMoneda(0));
		$('#divImputacion').text('');
	}

	function hayErrorGuardar(){
		if ($('#inputCaja_selectedValue').val() == '')
			return 'Debe seleccionar una caja para operar';

		if ($('#inputSocio_selectedValue').val() == '')
			return 'Debe ingresar un socio';

		if ($('#inputConcepto').val() == '')
			return 'Debe ingresar un concepto';

		return false;
	}

	function guardar(){
		var aux = ($('#inputBuscar_selectedValue').val() != '' ? 'editar' : 'agregar');
		var url = '/content/administracion/tesoreria/egresos/retiro_socios/' + aux + '.php?';
		try {
			funciones.guardar(url, armoObjetoGuardar(), function() {
				funciones.limpiarScreen();
				cambiarModo('buscar');
				$('#inputBuscar, #inputBuscar_selectedValue').val(this.data.numero).blur();
				funciones.pdfClick('/administracion/proveedores/aplicacion/?idProveedor=' + this.data.proveedor.id);
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
		var url = '/content/administracion/tesoreria/egresos/retiro_socios/buscar.php?idRetiro=' + $('#inputBuscar_selectedValue').val(),
			msgError = 'El retiro de pago "' + $('#inputBuscar_selectedName').val() + '" no existe.',
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
				observaciones: $('#inputObservaciones').val(),
				idCaja_S: $('#inputCaja_selectedValue').val(),
				concepto: $('#inputConcepto').val(),
				idRetiro: $('#inputBuscar_selectedValue').val()
			},
			importes: $('.pluginImportes').importes('getJson')
		};
	}

	function borrar(){
		var msg = '¿Está seguro que desea borrar el retiro de socio número "' + $('#inputBuscar_selectedValue').val() + '"?',
			url = '/content/administracion/tesoreria/egresos/retiro_socios/borrar.php';
		funciones.borrar(msg, url, armoObjetoBorrar());
	}

	function armoObjetoBorrar(){
		return {idRetiro: $('#inputBuscar_selectedValue').val()};
	}

	function pdfClick(){
		var url = '/content/administracion/tesoreria/egresos/retiro_socios/getPdf.php';
		url += '?idRetiro=' + $('#inputBuscar_selectedValue').val();
		funciones.pdfClick(url);
	}
</script>

<div id='programaTitulo'></div>
<div id='programaContenido'>
	<div class='fLeft pantalla'>
		<?php
			$tabla = new HtmlTable(array('cantRows' => 4, 'cantCols' => 2, 'id' => 'tablaDatos', 'cellSpacing' => 10));
			$tabla->getRowCellArray($rows, $cells);

			$rows[0]->class = 'trCaja';

			$cells[0][0]->style->width = '150px';
			$cells[0][0]->content = '<label>Caja a utilizar:</label>';
			$cells[0][1]->style->width = '250px';
			$cells[0][1]->content = '<span rel="importePorOperacion"><input id="inputCaja" class="textbox obligatorio autoSuggestBox inputForm w230" name="CajaPorUsuario" rel="caja" /></span>';

			$cells[1][0]->content = '<label>Socio:</label>';
			$cells[1][1]->content = '<input id="inputSocio" class="textbox obligatorio autoSuggestBox inputForm w230" name="Socio" rel="socio" />';

			$cells[2][0]->content = '<label>En concepto de:</label>';
			$cells[2][1]->content = '<input id="inputConcepto" class="textbox obligatorio inputForm w230" rel="concepto" />';

			$cells[3][0]->content = '<label>Observaciones:</label>';
			$cells[3][1]->content = '<textarea id="inputObservaciones" class="textbox inputForm w230" rel="observaciones"></textarea>';

			$tabla->create();//impresion
		?>
	</div>
	<div class="pluginImportes"></div>
</div>
<div id='programaPie'>
	<div id='filtro' class='draggableDialog hidden'>
		<div>
			<label for='inputBuscar' class='filtroBuscar'>Retiro de socio:</label>
			<input id='inputBuscar' class='textbox autoSuggestBox filtroBuscar w200' name='RetiroSocio' />
		</div>
		<div>
			<a id='btnMiniBuscar' class='boton' href='#' title='Buscar'><img src="/img/botones/25/buscar.gif" /></a>
		</div>
	</div>
	<div class='botonera'>
		<?php Html::echoBotonera(array('boton' => 'buscar', 'accion' => 'funciones.buscarClick();')); ?>
		<?php Html::echoBotonera(array('boton' => 'editar', 'accion' => 'funciones.editarClick();', 'permiso' => 'administracion/tesoreria/egresos/retiro_socios/editar/')); ?>
		<?php Html::echoBotonera(array('boton' => 'agregar', 'accion' => 'funciones.agregarClick();', 'permiso' => 'administracion/tesoreria/egresos/retiro_socios/agregar/')); ?>
		<?php Html::echoBotonera(array('boton' => 'pdf', 'accion' => 'pdfClick();')); ?>
		<?php Html::echoBotonera(array('boton' => 'borrar', 'accion' => 'funciones.borrarClick();', 'permiso' => 'administracion/tesoreria/egresos/retiro_socios/borrar/')); ?>
		<?php Html::echoBotonera(array('boton' => 'guardar', 'accion' => 'funciones.guardarClick();')); ?>
		<?php Html::echoBotonera(array('boton' => 'cancelar', 'accion' => 'funciones.cancelarBuscarClick();', 'id' => 'btnCancelarBuscar')); ?>
		<?php Html::echoBotonera(array('boton' => 'cancelar', 'accion' => 'funciones.cancelarEditarClick();', 'id' => 'btnCancelarEditar')); ?>
	</div>
</div>
