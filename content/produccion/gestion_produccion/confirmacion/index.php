<?php
?>

<script type='text/javascript'>
	$(document).ready(function(){
		tituloPrograma = 'Confirmación de stock';
		//noinspection JSCheckFunctionSignatures
		$('.inputPar').livequery(function(){
			$(this).blur(function() {
				blurPar($(this));
			});
		});
		cambiarModo('inicio');
	});

	function buscar() {
		funciones.limpiarScreen();
		var url = funciones.controllerUrl('buscar', getParams());
		funciones.load($('#divConfirmacionStock'), url, function() {
			$('#divConfirmacionStock').fixedHeader({target: 'table'});
			$('.btnConfirmar').click(guardar);
			cambiarModo('agregar');
		});
	}

	function blurPar(obj) {
		if (funciones.toInt(obj.val()) > funciones.toInt(obj.data('maxcant')) || funciones.toInt(obj.val()) < 0 || obj.val() == '')
			obj.val(obj.data('maxcant'));
		var idComb = obj.data('idordendefabricacion') + '-' + obj.data('numerotarea');
		var sum = 0;
		$('.tarea_' + idComb).each(function(){
			sum += funciones.toInt($(this).val());
		});
		$('#total_' + idComb).text(sum);
	}

	function hayErrorGuardar(obj){
		if (!obj.cantidadTotal) {
			return 'No puede hacer una confirmación vacía (todas las columnas están en cero)';
		}
		return false;
	}

	function guardar(e){
		var obj = armoObjetoGuardar((e.target.tagName == 'IMG') ? $(e.target).parents('a') : $(e.target));
		var error = hayErrorGuardar(obj);
		if (!error) {
			$.confirm('¿Está seguro que desea ingresar a stock ' + obj.cantidadTotal + ' pares, correspondientes a la tarea ' + obj.idOrdenDeFabricacion + ' - ' + obj.numeroTarea + '?', function(r) {
				if (r == funciones.si) {
					var url = funciones.controllerUrl('agregar');
					funciones.guardar(url, obj, function() {
						funciones.delay('$.showLoading()', 200);
						refreshOne(obj);
					}, null, null, false);
				}
			});
		} else {
			$.error(error);
		}
	}

	function armoObjetoGuardar(obj){
		var idComb = obj.data('idordendefabricacion') + '-' + obj.data('numerotarea');
		var i = 1;
		var sum = 0;
		var confirmacion = {};
		confirmacion.idOrdenDeFabricacion = obj.data('idordendefabricacion');
		confirmacion.numeroTarea = obj.data('numerotarea');
		confirmacion.cantidad = [];
		$('.tarea_' + idComb).each(function(){
			confirmacion.cantidad[i] = funciones.toInt($(this).val());
			sum += confirmacion.cantidad[i];
			i++;
		});
		confirmacion.cantidadTotal = sum;
		return confirmacion;
	}

	function refreshOne(obj) {
		var url = funciones.controllerUrl('buscar', {
				numeroOrdenFabricacion: obj.idOrdenDeFabricacion,
				numeroTarea: obj.numeroTarea,
				one: '1'
		}), msgError = 'Ocurrió un error al intentar actualizar la lista. Por favor, actualice la página e inténtelo nuevamente',
			cbSuccess = function(json){
				var idComb = json.idOrdenDeFabricacion + '-' + json.numeroTarea;
				var row = $('#row_' + idComb);
				if (json.cantidad > 0) {
					$('.tarea_' + idComb).each($.proxy(function(a, b){
						$(b).val(funciones.toInt(this.pendiente[a + 1]));
					}, json));
					blurPar($(row).find('.inputPar:last'));
					$(row).find('.inputPar:first').focus();
				} else {
					row.remove();
					$('#divConfirmacionStock .inputPar:first').focus();
				}
			};
		funciones.buscar(url, cbSuccess, msgError);
	}

	function getParams(){
		return {
				fechaDesde: $('#inputBuscarFechaDesde').val(),
				fechaHasta: $('#inputBuscarFechaHasta').val(),
				idAlmacen: $('#inputBuscarAlmacen_selectedValue').val(),
				idArticulo: $('#inputBuscarArticulo_selectedValue').val(),
				idColorArticulo: $('#inputBuscarColorArticulo_selectedValue').val(),
				numeroOrdenFabricacion: $('#inputBuscarNumeroOrdenFabricacion').val(),
				one: '0',
				orden: $('#inputOrden').val()
		}
	}

	function pdfClick(){
		funciones.pdfClick(
			funciones.controllerUrl('getPdf', getParams()));
	}

	function xlsClick(){
		funciones.pdfClick(
			funciones.controllerUrl('getXls', getParams()));
	}

	function cambiarModo(modo){
		funciones.cambiarModo(modo);
		switch (modo){
			case 'inicio':
				$('#divConfirmacionStock').html('');
				break;
			case 'buscar':
				funciones.cambiarTitulo();
				break;
			case 'editar':
				break;
			case 'agregar':
				$('#btnPdf').show();
				break;
		}
	}
</script>

<div id='programaTitulo'></div>
<div id='programaContenido' class='customScroll'>
	<div id='divConfirmacionStock' class='w100p customScroll acordeon h480'>
		<?php // TABLOTA ?>
	</div>
</div>
<div id='programaPie'>
	<div id='filtro' class='draggableDialog hidden'>
		<div>
			<label for='inputBuscarFechaDesde' class='filtroBuscar'>Rango fecha cumplido:</label>
			<input id='inputBuscarFechaDesde' class='textbox filtroBuscar w80' to='inputBuscarFechaHasta' validate='Fecha' />
			<input id='inputBuscarFechaHasta' class='textbox filtroBuscar w80' from='inputBuscarFechaDesde' validate='Fecha' />
		</div>
		<div>
			<label for='inputBuscarAlmacen' class='filtroBuscar'>Almacén:</label>
			<input id='inputBuscarAlmacen' class='textbox autoSuggestBox filtroBuscar w220' name='Almacen' alt='' />
		</div>
		<div>
			<label for='inputBuscarArticulo' class='filtroBuscar'>Artículo:</label>
			<input id='inputBuscarArticulo' class='textbox autoSuggestBox filtroBuscar w220' name='Articulo' alt='' />
		</div>
		<div>
			<label for='inputBuscarColorArticulo' class='filtroBuscar'>Color:</label>
			<input id='inputBuscarColorArticulo' class='textbox autoSuggestBox filtroBuscar w220' name='ColorPorArticulo' linkedTo='inputBuscarArticulo,Articulo' alt='' />
		</div>
		<div>
			<label for='inputBuscarNumeroOrdenFabricacion' class='filtroBuscar'>Nº órden fabricación:</label>
			<input id='inputBuscarNumeroOrdenFabricacion' class='textbox filtroBuscar w220' />
		</div>
		<div>
			<label for="inputOrden" class='filtroBuscar'>Orden:</label>
			<select id='inputOrden' class='textbox filtroBuscar w220'>
				<option value='3'>Nº de tarea ascendente</option>
				<option value='2'>Nº de tarea descendente</option>
				<option value='0'>Fecha cumplido descendente</option>
				<option value='1'>Fecha cumplido ascendente</option>
			</select>
		</div>
		<div>
			<a id='btnMiniBuscar' class='boton' href='#' title='Buscar'><img src="/img/botones/25/buscar.gif" /></a>
		</div>
	</div>
	<div class='botonera'>
		<?php Html::echoBotonera(array('boton' => 'buscar', 'accion' => 'funciones.buscarClick();')); ?>
		<?php Html::echoBotonera(array('boton' => 'pdf', 'accion' => 'pdfClick();')); ?>
		<?php Html::echoBotonera(array('boton' => 'cancelar', 'accion' => 'funciones.cancelarBuscarClick();', 'id' => 'btnCancelarBuscar')); ?>
	</div>
</div>
