<?php
?>
<script type='text/javascript'>
	$(document).ready(function(){
		tituloPrograma = 'Generación de despachos';
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
			idCliente: $('#inputBuscarCliente_selectedValue').val(),
			almacen: $('#radioGroupAlmacen').radioVal(),
			idArticulo: $('#inputBuscarArticulo_selectedValue').val(),
			idColor: $('#inputBuscarColor_selectedValue').val()
		});
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
				$('#divGeneracionDespachos').html(result);
				$('.acordeon').acordeon({fixedHeight: false});
				$('.chkTodos').click(chkTodosClick);
				$('.chkUno').click(chkUnoClick);
				cambiarModo('agregar');
			}
			$.hideLoading();
		});
	}

	function blurPar(obj) {
		if (funciones.toInt(obj.val()) > funciones.toInt(obj.attr('maxCant')) || funciones.toInt(obj.val()) < 0 || obj.val() == '')
			obj.val(obj.attr('maxCant'));
		var arrId = obj.attr('id').split('_')[1].split('-');
		var idComb = arrId[0] + '-' + arrId[1];
		var sum = 0;
		$('.par_' + idComb).each(function(){
			sum += funciones.toInt($(this).val());
		});
		$('#pares_chk_' + idComb).text(sum);
		//Si está checkeado, uncheckeo y checkeo así se actualiza el título
		if ($('#chk_' + idComb).isChecked())
			chkUnoClick($('#chk_' + idComb));
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
		$('#spanCantidadParesDespachar_' + otherClass).text(pares);
	}

	function chkUnoClick(o){
		var obj = (typeof o.currentTarget === 'undefined' ? o : $(this)); //Esto es porque cuando viene del evento, en O viene el evento
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
		$('#spanCantidadParesDespachar_' + otherClass).text(pares);
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
			return 'Debe seleccionar algo para despachar';
		return false;
	}

	function guardar(){
		var url = funciones.controllerUrl('agregar');
		funciones.guardar(url, armoObjetoGuardar());
	}

	function facturaRapida() {
		var url = funciones.controllerUrl('agregar');
		var objGuardar = armoObjetoGuardar();
		objGuardar.facturar = '1';
		funciones.guardar(url, objGuardar, buscar);
	}

	function remitoRapido() {
		var url = funciones.controllerUrl('agregar');
		var objGuardar = armoObjetoGuardar();
		objGuardar.remitir = '1';
		funciones.guardar(url, objGuardar, buscar);
	}

	function armoObjetoGuardar(){
		var desp = {};
		desp.despachos = [];
		var divDespachos = $('.divAcordeon');
		divDespachos.each(function(){
			var despacho = {};
			var divDespacho = $(this);
			var checks = divDespacho.find('.chkUno:checked');
			if (checks.length > 0) {
				despacho.predespachos = [];
				checks.each(function(){
					var arrPred = $(this).attr('id').split('_')[1].split('-');
					var cant = despacho.predespachos.length;
					despacho.predespachos[cant] = {};
					despacho.predespachos[cant].pedidoNumero = funciones.toInt(arrPred[0]);
					despacho.predespachos[cant].pedidoNumeroDeItem = funciones.toInt(arrPred[1]);
					despacho.predespachos[cant].cant = [];
					for (var i = 1; i <= 8; i++) {
						despacho.predespachos[cant].cant[i] = funciones.toInt($('#par_' + arrPred[0] + '-' + arrPred[1] + '-' + i).val());
					}
				});
				var arrCombinado = divDespacho.attr('id').split('_')[1].split('-');
				despacho.cliente = funciones.toInt(arrCombinado[0]);
				despacho.sucursal = funciones.toInt(arrCombinado[1]);
				despacho.observaciones = divDespacho.find('#observaciones_' + arrCombinado.join('-')).val();
				//Agrego el despacho a la lista
				desp.despachos[desp.despachos.length] = despacho;
			}
		});
		return desp;
	}

	function cambiarModo(modo){
		funciones.cambiarModo(modo);
		$('#radioGroupAlmacen').enableRadioGroup();
		switch (modo){
			case 'inicio':
				$('#btnFac, #btnRem').hide();
				$('#divGeneracionDespachos').html('');
				break;
			case 'buscar':
				break;
			case 'editar':
				break;
			case 'agregar':
				$('#btnFac, #btnRem').show();
				break;
		}
	}
</script>

<div id='programaTitulo'></div>
<div id='programaContenido' class='customScroll'>
	<div id='divGeneracionDespachos' class='w100p customScroll acordeon h480'>
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
		<?php //Html::echoBotonera(array('boton' => 'fac', 'accion' => 'facturaRapida();', 'permiso' => 'comercial/despachos/generacion/agregar/')); ?>
		<?php Html::echoBotonera(array('boton' => 'rem', 'accion' => 'remitoRapido();', 'permiso' => 'comercial/despachos/generacion/agregar/')); ?>
		<?php Html::echoBotonera(array('boton' => 'guardar', 'accion' => 'funciones.guardarClick();')); ?>
		<?php Html::echoBotonera(array('boton' => 'cancelar', 'accion' => 'funciones.cancelarBuscarClick();', 'id' => 'btnCancelarBuscar')); ?>
	</div>
</div>
