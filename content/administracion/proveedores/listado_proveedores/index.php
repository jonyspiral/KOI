<?php
?>

<style>
	td {
		font-size: 11px !important;
	}
</style>

<script type='text/javascript'>
	$(document).ready(function() {
		tituloPrograma = 'Listado proveedores';
		cambiarModo('inicio');
	});

	function limpiarScreen() {
		funciones.cambiarTitulo();
		$('#divListadoProveedores').html('');
	}

	function getParams() {
		return {
			cuit: $('#inputCuit').val(),
			idVendedor: $('#inputVendedor_selectedValue').val(),
			idPais: $('#inputPais_selectedValue').val(),
			idProvincia: $('#inputProvincia_selectedValue').val(),
			idLocalidad: $('#inputLocalidad_selectedValue').val(),
			calle: $('#inputCalle').val(),
			numero: $('#inputNumero').val(),
			orderBy: $('#inputOrderBy').val()
		};
	}

	function buscar() {
		funciones.limpiarScreen();
		funciones.load($('#divListadoProveedores'), funciones.controllerUrl('buscar', getParams()), function() {
			$('#divListadoProveedores').fixedHeader({target: 'table'});
			$('.proveedor').hover(function() {
				$(this).stop(true, true).css('font-weight', 'bold');
			}, function() {
				$(this).stop(true, true).css('font-weight', 'normal');
			});
			$('.proveedor').click(function(e) {
				funciones.newWindow('/abm/proveedores/?id=' + $(e.target).parents('tr').attr('id'));
			});
		});
	}

	function xlsClick() {
		funciones.xlsClick(urlToExport('xls'));
	}

	function pdfClick() {
		funciones.xlsClick(urlToExport('pdf'));
	}

	function urlToExport(tipo) {
		return funciones.controllerUrl('get' + (tipo == 'xls' ? 'Xls' : 'Pdf'), getParams());
	}

	function cambiarModo(modo) {
		funciones.cambiarModo(modo);
		switch (modo) {
			case 'inicio':
				$('#checkboxFAC').check();
				$('#checkboxNDB').check();
				$('#checkboxNCR').check();
				break;
			case 'buscar':
				break;
		}
	}
</script>

<div id='programaTitulo'></div>
<div id='programaContenido' class='customScroll'>
	<div id='divListadoProveedores' class='w100p customScroll'></div>
</div>
<div id='programaPie'>
	<div id='filtro' class='draggableDialog hidden'>
		<div>
			<label for='inputCuit' class='filtroBuscar'>CUIT:</label>
			<input id='inputCuit' class='textbox filtroBuscar w200' />
		</div>
		<div>
			<label for='inputPais' class='filtroBuscar'>Pais:</label>
			<input id='inputPais' class='textbox autoSuggestBox filtroBuscar w200' name='Pais' />
		</div>
		<div>
			<label for='inputProvincia' class='filtroBuscar'>Provincia:</label>
			<input id='inputProvincia' class='textbox autoSuggestBox filtroBuscar w200' name='Provincia' linkedTo='inputPais,Pais' />
		</div>
		<div>
			<label for='inputLocalidad' class='filtroBuscar'>Localidad:</label>
			<input id='inputLocalidad' class='textbox autoSuggestBox filtroBuscar w200' name='Localidad' linkedTo='inputPais,Pais;inputProvincia,Provincia' />
		</div>
		<div>
			<label for='inputCalle' class='filtroBuscar'>Calle:</label>
			<input id='inputCalle' class='textbox filtroBuscar w200' />
		</div>
		<div>
			<label for='inputNumero' class='filtroBuscar'>Número:</label>
			<input id='inputNumero' class='textbox filtroBuscar w200' />
		</div>
		<div>
			<label for='inputOrderBy' class='filtroBuscar'>Ordenar por:</label>
			<select id='inputOrderBy' class='textbox filtroBuscar w200'>
				<option value='0'>Razón social</option>
				<option value='1'>Cod. proveedor</option>
				<option value='2'>Pais</option>
				<option value='3'>Provincia</option>
				<option value='4'>Localidad</option>
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
		<?php Html::echoBotonera(array('boton' => 'xls', 'accion' => 'xlsClick();')); ?>
		<?php Html::echoBotonera(array('boton' => 'cancelar', 'accion' => 'funciones.cancelarBuscarClick();', 'id' => 'btnCancelarBuscar')); ?>
	</div>
</div>