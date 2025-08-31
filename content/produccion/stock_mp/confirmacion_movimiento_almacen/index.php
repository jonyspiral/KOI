<?php
?>

<script type='text/javascript'>
	$(document).ready(function(){
		tituloPrograma = 'Confirmación de movimientos de almacén MP';
		cambiarModo('inicio');
	});

	function buscar() {
		funciones.limpiarScreen();
		var url = funciones.controllerUrl('buscar', {
			fechaDesde: $('#inputBuscarFechaDesde').val(),
			fechaHasta: $('#inputBuscarFechaHasta').val(),
			idAlmacen: $('#inputBuscarAlmacen_selectedValue').val(),
			idMaterial: $('#inputBuscarMaterial_selectedValue').val(),
			idColor: $('#inputBuscarColorMateriaPrima_selectedValue').val(),
			mostrar: $('#inputMostrar').val(),
			orden: $('#inputOrden').val()
		});
		funciones.load($('#divConfirmacionMovimientoAlmacen'), url, function() {
			$('#divConfirmacionMovimientoAlmacen').fixedHeader({target: 'table'});
			$('.btnConfirmar').click(guardar);
			$('.btnRechazar').click(borrar);
			cambiarModo('agregar');
		});
	}

	function guardar(e){
		var obj = armoObjetoGuardar((e.target.tagName == 'IMG') ? $(e.target).parents('a') : $(e.target));
		$.confirm('¿Está seguro que desea confirmar el movimiento de ' + obj.cantidadTotal + ' pares del almacén ' + obj.idAlmacenOrigen + ' al ' + obj.idAlmacenDestino + '?', function(r) {
			if (r == funciones.si) {
				var url = funciones.controllerUrl('agregar');
				funciones.guardar(url, obj, function() {
					$.success(this.responseMsg);
					funciones.delay('$.showLoading()', 200);
					buscar();
				}, null, null, false);
			}
		});
	}

	function armoObjetoGuardar(obj){
		return {
			idConfirmacion: obj.data('idconfirmacion'),
			idAlmacenOrigen: obj.data('idalmacenorigen'),
			idAlmacenDestino: obj.data('idalmacendestino'),
			cantidadTotal: obj.data('cantidadtotal')
		};
	}

	function borrar(e){
		var obj = armoObjetoGuardar((e.target.tagName == 'IMG') ? $(e.target).parents('a') : $(e.target));
		$.confirm('¿Está seguro que desea rechazar el movimiento de ' + obj.cantidadTotal + ' pares del almacén ' + obj.idAlmacenOrigen + ' al ' + obj.idAlmacenDestino + '?', function(r) {
			if (r == funciones.si) {
				var url = funciones.controllerUrl('borrar');
				funciones.guardar(url, obj, function() {
					$.success(this.responseMsg);
					funciones.delay('$.showLoading()', 200);
					buscar();
				}, null, null, false);
			}
		});
	}

	function cambiarModo(modo){
		funciones.cambiarModo(modo);
		switch (modo){
			case 'inicio':
				$('#divConfirmacionMovimientoAlmacen').html('');
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
	<div id='divConfirmacionMovimientoAlmacen' class='w100p customScroll acordeon h480'>
		<?php // TABLOTA ?>
	</div>
</div>
<div id='programaPie'>
	<div id='filtro' class='draggableDialog hidden'>
		<div>
			<label for='inputBuscarFechaDesde' class='filtroBuscar'>Rango fecha mov.:</label>
			<input id='inputBuscarFechaDesde' class='textbox filtroBuscar w80' to='inputBuscarFechaHasta' validate='Fecha' />
			<input id='inputBuscarFechaHasta' class='textbox filtroBuscar w80' from='inputBuscarFechaDesde' validate='Fecha' />
		</div>
		<div>
			<label for='inputBuscarAlmacen' class='filtroBuscar'>Almacén:</label>
			<input id='inputBuscarAlmacen' class='textbox autoSuggestBox filtroBuscar w220' name='UsuarioPorAlmacen' alt='&idUsuario=<?php echo Usuario::logueado()->id; ?>' />
		</div>
		<div>
			<label for='inputBuscarMaterial' class='filtroBuscar'>Material:</label>
			<input id='inputBuscarMaterial' class='textbox autoSuggestBox filtroBuscar w220' name='Material' alt='' />
		</div>
		<div>
			<label for='inputBuscarColorMateriaPrima' class='filtroBuscar'>Color:</label>
			<input id='inputBuscarColorMateriaPrima' class='textbox autoSuggestBox filtroBuscar w220' name='ColorMateriaPrima' linkedTo='inputBuscarMaterial,Material' alt='' />
		</div>
		<div>
			<label for="inputMostrar" class='filtroBuscar'>Mostrar:</label>
			<select id='inputMostrar' class='textbox filtroBuscar w220'>
				<option value='0'>Pendientes</option>
				<option value='1'>Confirmados</option>
				<option value='2'>Rechazados</option>
				<option value='3'>Todos</option>
			</select>
		</div>
		<div>
			<label for="inputOrden" class='filtroBuscar'>Orden:</label>
			<select id='inputOrden' class='textbox filtroBuscar w220'>
				<option value='0'>Por estado (pendientes primero)</option>
				<option value='1'>Por fecha ascendente</option>
				<option value='2'>Por fecha descendente</option>
			</select>
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
