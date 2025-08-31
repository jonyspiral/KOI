<?php
?>

<script type='text/javascript'>
	$(document).ready(function(){
		tituloPrograma = 'Descontar pendiente';
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
		var url = funciones.controllerUrl('buscar', {
			idProveedor: $('#inputBuscarProveedor_selectedValue').val(),
			fechaDesde: $('#inputBuscarFechaDesde').val(),
			fechaHasta: $('#inputBuscarFechaHasta').val(),
			idOrdenDeCompra: $('#inputBuscarNumero').val(),
			one: '0'
		});
		funciones.load($('#divDescontarPendiente'), url, function() {
			$('#divDescontarPendiente').fixedHeader({target: 'table'});
			$('.btnConfirmar').click(guardar);
			cambiarModo('agregar');
		});
	}

	function blurPar(obj) {
		var idComb = obj.data('idordendecompra') + '_' + obj.data('numerodeitem');
		var esEntero = $('.detalle_' + idComb).length != 1;
		var sum = 0;
		var condicion;

		if(esEntero){
			condicion = funciones.toInt(obj.val()) > funciones.toInt(obj.data('maxcant')) || funciones.toInt(obj.val()) < 0 || obj.val() == '';
		} else {
			condicion = funciones.toInt(obj.val()) > funciones.toFloat(obj.data('maxcant')) || funciones.toFloat(obj.val()) < 0 || obj.val() == '';
		}

		if (condicion)
			obj.val(obj.data('maxcant'));

		$('.detalle_' + idComb).each(function(){
			sum += (esEntero ? funciones.toInt($(this).val()) : funciones.toFloat($(this).val()));
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
			$.confirm('¿Está seguro que desea descontar del pendiente ' + obj.cantidadTotal + ' unidades, correspondientes a la órden de compra ' + obj.idOrdenDeCompra + ' ítem ' + obj.numeroDeItem + '?', function(r) {
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
		var detalles = $('.detalle_' + obj.data('idordendecompra') + '_' + obj.data('numerodeitem'));
		var i = 1;
		var sum = 0;
		var confirmacion = {};
		confirmacion.idOrdenDeCompra = obj.data('idordendecompra');
		confirmacion.numeroDeItem = obj.data('numerodeitem');

		if(detalles.length == 1){
			confirmacion.cantidad = detalles.first().val();
			confirmacion.cantidadTotal = confirmacion.cantidad;
		} else {
			confirmacion.cantidad = [];
			detalles.each(function(){
				confirmacion.cantidad[i] = funciones.toInt($(this).val());
				sum += confirmacion.cantidad[i];
				i++;
			});
			confirmacion.cantidadTotal = sum;
		}

		return confirmacion;
	}

	function refreshOne(obj) {
		var url = funciones.controllerUrl('buscar', {
				idOrdenDeCompra: obj.idOrdenDeCompra,
				numeroDeItem: obj.numeroDeItem,
				one: '1'
		}), msgError = 'Ocurrió un error al intentar actualizar la lista. Por favor, actualice la página e inténtelo nuevamente',
			cbSuccess = function(json){
				var idComb = json.idOrdenDeCompra + '_' + json.numeroDeItem;
				var row = $('#row_' + idComb);
				var detalles = $('.detalle_' + idComb);
				if (json.pendiente > 0) {
					if(json.usaRango == 'S'){
						detalles.each($.proxy(function(a, b){
							$(b).val(funciones.toInt(this.pendientes[a + 1]));
						}, json));
						blurPar($(row).find('.inputPar:last'));
						$(row).find('.inputPar:first').focus();
					} else {
						detalles.first().val(json.pendiente);
					}
				} else {
					row.remove();
					$('#divDescontarPendiente .inputPar:first').focus();
				}
			};
		funciones.buscar(url, cbSuccess, msgError);
	}

	function cambiarModo(modo){
		funciones.cambiarModo(modo);
		switch (modo){
			case 'inicio':
				$('#divDescontarPendiente').html('');
				break;
			case 'buscar':
				funciones.cambiarTitulo();
				break;
			case 'editar':
				break;
			case 'agregar':
				break;
		}
	}
</script>

<div id='programaTitulo'></div>
<div id='programaContenido' class='customScroll'>
	<div id='divDescontarPendiente' class='w100p customScroll acordeon h480'>
		<?php // TABLOTA ?>
	</div>
</div>
<div id='programaPie'>
	<div id='filtro' class='draggableDialog hidden'>
		<div>
			<label for='inputBuscarProveedor' class='filtroBuscar'>Proveedor:</label>
			<input id='inputBuscarProveedor' class='textbox autoSuggestBox filtroBuscar w220' name='Proveedor' />
		</div>
		<div>
			<label for='inputBuscarFechaDesde' class='filtroBuscar'>Rango fecha:</label>
			<input id='inputBuscarFechaDesde' class='textbox filtroBuscar w80' to='inputBuscarFechaHasta' validate='Fecha' />
			<input id='inputBuscarFechaHasta' class='textbox filtroBuscar w80' from='inputBuscarFechaDesde' validate='Fecha' />
		</div>
		<div>
			<label for='inputBuscarNumero' class='filtroBuscar'>Numero:</label>
			<input id='inputBuscarNumero' class='textbox filtroBuscar w220' validate='Entero' />
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
