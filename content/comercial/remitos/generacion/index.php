<?php
?>
<script type='text/javascript'>
	$(document).ready(function(){
		tituloPrograma = 'Generación de remitos';
		cambiarModo('inicio');
	});

	function buscar(idBuscar) {
		funciones.limpiarScreen();
		var url = '/content/comercial/remitos/generacion/buscar.php?' +
					'idCliente=' + $('#inputBuscarCliente_selectedValue').val() +
					'&almacen=' + $('#radioGroupAlmacen').radioVal() +
					'&idArticulo=' + $('#inputBuscarArticulo_selectedValue').val() +
					'&idColor=' + $('#inputBuscarColor_selectedValue').val();
		$.showLoading();
		$.post(url, function(result) {
			try {
				var json = $.parseJSON(result);
				switch (funciones.getJSONType(json)) {
					case funciones.jsonNull:
					case funciones.jsonError:
						$.error('Ocurrió un error al intentar realizar la consulta');
						break;
					case funciones.jsonInfo:
						$.info(funciones.getJSONMsg(json));
						break;
				}
			} catch (ex) {
				$('#divGeneracionRemitos').html(result);
				$('.acordeon').acordeon({fixedHeight: false});
				$('.toggleTablita').click(toggleTablita);
				$('.chkTodos').click(chkTodosClick);
				$('.chkUno').click(chkUnoClick);
				cambiarModo('agregar');
			}
			$.hideLoading();
		});
	}

	function toggleTablita(){
		var obj = $(this);
		var comb = obj.parents('tr:first').attr('class');
		if (obj.text() == '+') {
			obj.parents('.contenidoSeccion:first').animate({height: '+=50'}, function() {
				$('#divTablita_' + comb).slideDown('fast');
			});
			obj.text('-');
		} else {
			$('#divTablita_' + comb).slideUp('fast');
			obj.parents('.contenidoSeccion:first').animate({height: '-=50'});
			obj.text('+');
		}
	}

	function chkTodosClick(){
		var obj = $(this);
		var otherClass = obj.attr('id').split('_')[1];
		var pares = 0;
		$('.' + otherClass).each(function(){
			if ($(obj).isChecked()) {
				$(this).check();
				pares += funciones.toInt($('#pares_' + $(this).attr('id')).text());
			} else
				$(this).uncheck();
		});
		//Actualizo el total de pares del título
		$('#spanCantidadParesRemitir_' + otherClass).text(pares);
	}

	function chkUnoClick(){
		var obj = $(this);
		var otherClass = obj.attr('class').replace('chkUno', '').trim();
		//Sumo la cantidad de pares y veo si están todos checkeados
		var pares = 0;
		var all = true;
		$('.' + otherClass).each(function(){
			if (!$(this).isChecked())
				all = false;
			else
				pares += funciones.toInt($('#pares_' + $(this).attr('id')).text());
		});
		//Actualizo el total de pares del título
		$('#spanCantidadParesRemitir_' + otherClass).text(pares);
		//Verifico si debo poner el tilde de todo checkeado o sacarlo
		var chkTodos = $('#chkTodos_' + otherClass);
		if (all)
			chkTodos.check();
		else
			chkTodos.uncheck();
	}

	function hayErrorGuardar(){
		var chks = $('.chkUno:checked');
		if (chks.length == 0)
			return 'Debe seleccionar algo para remitir';
		return false;
	}

	function guardar(){
		var url = '/content/comercial/remitos/generacion/agregar.php?';
		funciones.guardar(url, armoObjetoGuardar());
	}

	function armoObjetoGuardar(){
		var rems = {};
		rems.remitos = [];
		var divRemitos = $('.divAcordeon');
		divRemitos.each(function(){
			var remito = {};
			var divRemito = $(this);
			var checks = divRemito.find('.chkUno:checked');
			if (checks.length > 0) {
				remito.despachos = [];
				checks.each(function(){
					var arrDespacho = $(this).attr('id').split('_')[1].split('-');
					if (arrDespacho.length == 3) { //Esto es por si el ID del despacho era negativo, una negrada
						arrDespacho[0] = funciones.toInt(arrDespacho[1]) * -1;
						arrDespacho[1] = arrDespacho[2];
					}
					var cant = remito.despachos.length;
					remito.despachos[cant] = {};
					remito.despachos[cant].despachoNumero = funciones.toInt(arrDespacho[0]);
					remito.despachos[cant].numeroDeItem = funciones.toInt(arrDespacho[1]);
				});
				var arrCombinado = divRemito.attr('id').split('_')[1].split('-');
				remito.cliente = funciones.toInt(arrCombinado[0]);
				remito.sucursal = funciones.toInt(arrCombinado[1]);
				remito.observaciones = divRemito.find('#observaciones_' + arrCombinado.join('-')).val();
				remito.bultos = funciones.toInt(divRemito.find('#bultos_' + arrCombinado.join('-')).val());
				//Agrego el remito a la lista
				rems.remitos[rems.remitos.length] = remito;
			}
		});
		return rems;
	}

	function cambiarModo(modo){
		funciones.cambiarModo(modo);
		$('#radioGroupAlmacen').enableRadioGroup();
		switch (modo){
			case 'inicio':
				$('#divGeneracionRemitos').html('');
				break;
			case 'buscar':
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
	<div id='divGeneracionRemitos' class='w100p customScroll acordeon h480'>
		<?php // TABLOTA ?>
	</div>
</div>
<div id='programaPie'>
	<div id='filtro' class='draggableDialog hidden'>
		<div>
			<label class='filtroBuscar'>Cliente:</label>
			<input id='inputBuscarCliente' class='textbox autoSuggestBox filtroBuscar w190' name='Cliente' alt='' />
		</div>
		<div>
			<label class='filtroBuscar'>Almacén:</label>
			<div id='radioGroupAlmacen' class='customRadio w180 inline-block'>
				<input id='rdAlmacen_0' type='radio' name='radioGroupAlmacen' value='0' /><label for='rdAlmacen_0'>Ambos</label>
				<input id='rdAlmacen_1' type='radio' name='radioGroupAlmacen' value='1' /><label for='rdAlmacen_1'>1</label>
				<input id='rdAlmacen_2' type='radio' name='radioGroupAlmacen' value='2' /><label for='rdAlmacen_2'>2</label>
			</div>
		</div>
		<div>
			<label class='filtroBuscar'>Artículo:</label>
			<input id='inputBuscarArticulo' class='textbox autoSuggestBox filtroBuscar w190' name='Articulo' alt='' />
		</div>
		<div>
			<label class='filtroBuscar'>Color:</label>
			<input id='inputBuscarColor' class='textbox autoSuggestBox filtroBuscar w190' name='ColorPorArticulo' linkedTo='inputBuscarArticulo,Articulo' alt='' />
		</div>
		<div>
			<a id='btnMiniBuscar' class='boton' href='#' title='Buscar'><img src="/img/botones/25/buscar.gif" /></a>
		</div>
	</div>
	<div class='botonera'>
		<?php Html::echoBotonera(array('boton' => 'buscar', 'accion' => 'funciones.buscarClick();')); ?>
		<?php Html::echoBotonera(array('boton' => 'guardar', 'accion' => 'funciones.guardarClick();')); ?>
		<?php Html::echoBotonera(array('boton' => 'cancelar', 'accion' => 'funciones.cancelarBuscarClick();', 'id' => 'btnCancelarBuscar')); ?>
	</div>
</div>
