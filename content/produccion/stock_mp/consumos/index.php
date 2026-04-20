<?php
?>

<script type='text/javascript'>
	$(document).ready(function(){
        tituloPrograma = 'Consumos de materia prima';
		//noinspection JSCheckFunctionSignatures
		$('.inputPar').livequery(function(){
			$(this).blur(function() {
				blurPar($(this));
			});
		});
		cambiarModo('inicio');
	});

	function buscar() {
		if (!$('#inputBuscarAlmacen_selectedValue').val()) {
			$.error('Debe elegir el almacén del cual quiere realizar consumos');
			return;
		}
		funciones.limpiarScreen();
		var url = funciones.controllerUrl('buscar', {
			idAlmacen: $('#inputBuscarAlmacen_selectedValue').val(),
			idMaterial: $('#inputBuscarMaterial_selectedValue').val(),
			idColor: $('#inputBuscarColorMateriaPrima_selectedValue').val(),
			one: '0',
			orden: $('#inputOrden').val()
		});
		funciones.load($('#divConsumos'), url, function() {
			$('#divConsumos').fixedHeader({target: 'table'});
			$('.btnConfirmar').click(guardar);
			cambiarModo('agregar');
		});
	}

	function blurPar(obj) {
		if (funciones.toFloat(obj.val()) > funciones.toFloat(obj.data('maxcant')) || funciones.toFloat(obj.val()) < 0 || obj.val() == '')
			obj.val(obj.data('maxcant'));
		var idComb = funciones.padLeft(obj.data('idalmacen').toString(), 2) + '-' + funciones.padLeft(obj.data('idmaterial').toString(), 4) + '-' + obj.data('idcolormateriaprima');
		var sum = 0;
		$('.material_' + idComb).each(function(){
			sum += funciones.toFloat($(this).val());
		});
		$('#total_' + idComb).text(sum);
	}

	function hayErrorGuardar(obj){
		if (!obj.cantidadTotal) {
			return 'No puede hacer un consumo vacío (todas las columnas están en cero)';
		}
		return false;
	}

	function guardar(e){
		var obj = armoObjetoGuardar((e.target.tagName == 'IMG') ? $(e.target).parents('a') : $(e.target));
		var error = hayErrorGuardar(obj);
		if (!error) {
			$.confirm('¿Está seguro que desea consumir ' + obj.cantidadTotal + ' pares del almacén ' + obj.idAlmacen + '?', function(r) {
				if (r == funciones.si) {
					var url = funciones.controllerUrl('agregar');
					funciones.guardar(url, obj, function() {
						$.info(this.responseMsg);
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
		var idComb = funciones.padLeft(obj.data('idalmacen').toString(), 2) + '-' + funciones.padLeft(obj.data('idmaterial').toString(), 4) + '-' + obj.data('idcolormateriaprima');
		var i = 1;
		var sum = 0;
		var consumo = {};
		consumo.idAlmacen = funciones.padLeft(obj.data('idalmacen').toString(), 2);
		consumo.idMaterial = funciones.padLeft(obj.data('idmaterial').toString(), 4);
		consumo.idColor = obj.data('idcolormateriaprima');
		consumo.cantidad = [];
		$('.material_' + idComb).each(function(){
			consumo.cantidad[i] = funciones.toFloat($(this).val());
			sum += consumo.cantidad[i];
			i++;
		});
		consumo.cantidadTotal = sum;
		return consumo;
	}

	function refreshOne(obj) {
		var url = funciones.controllerUrl('buscar', {
				idAlmacen: obj.idAlmacen,
				idMaterial: obj.idMaterial,
				idColor: obj.idColor,
				one: '1'
			}),
			msgError = 'Ocurrió un error al intentar actualizar la lista. Por favor, actualice la página e inténtelo nuevamente',
			cbSuccess = function(json){
				var idComb = json.idAlmacen + '-' + json.idMaterial + '-' + json.idColor;
				$('.stock_' + idComb).each($.proxy(function(a, b){
					$(b).text(!funciones.toFloat(this.cantidad[a + 1]) ? '0' : funciones.formatearDecimales(funciones.toFloat(this.cantidad[a + 1]), 4, '.'));
				}, json));
				$('.material_' + idComb).val(0);
				var row = $('#row_' + idComb);
				if (json.cantidadTotal > 0) {
					blurPar($(row).find('.inputPar:last'));
					$(row).find('.inputPar:first').focus();
				} else {
					row.remove();
					$('#divConsumos .inputPar:first').focus();
				}
			};
		funciones.buscar(url, cbSuccess, msgError);
	}

	function cambiarModo(modo){
		funciones.cambiarModo(modo);
		switch (modo){
			case 'inicio':
				$('#divConsumos').html('');
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
	<div id='divConsumos' class='w100p customScroll acordeon h480'>
		<?php // TABLOTA ?>
	</div>
</div>
<div id='programaPie'>
	<div id='filtro' class='draggableDialog hidden'>
		<div>
			<label for='inputBuscarAlmacen' class='filtroBuscar'>Almacén:</label>
			<input id='inputBuscarAlmacen' class='textbox autoSuggestBox obligatorio filtroBuscar w220' name='UsuarioPorAlmacen' alt='&idUsuario=<?php echo Usuario::logueado()->id; ?>' />
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
			<label for="inputOrden" class='filtroBuscar'>Orden:</label>
			<select id='inputOrden' class='textbox filtroBuscar w220'>
				<option value='0'>Nº de material/color ascendente</option>
				<option value='1'>Nº de material/color descendente</option>
				<option value='2'>Cantidad stock descendente</option>
				<option value='3'>Cantidad stock ascendente</option>
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