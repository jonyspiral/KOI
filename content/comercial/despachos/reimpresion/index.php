<?php

?>
<script type='text/javascript'>
	$(document).ready(function(){
		tituloPrograma = 'Reimpresión de despachos';
		cambiarModo('inicio');
	});

	function buscar(idBuscar) {
		funciones.limpiarScreen();
		var url = '/content/comercial/despachos/reimpresion/buscar.php?';
			url += 'idCliente=' + $('#inputBuscarCliente_selectedValue').val();
			url += '&desde=' + $('#inputBuscarDesde').val();
			url += '&hasta=' + $('#inputBuscarHasta').val();
			url += '&almacen=' + $('#radioGroupAlmacen').radioVal();
			url += '&idArticulo=' + $('#inputBuscarArticulo_selectedValue').val();
			url += '&idColor=' + $('#inputBuscarColor_selectedValue').val();
			url += '&remitido=' + $('#radioGroupRemitido').radioVal();
			url += '&numero=' + $('#inputBuscarNumero').val();
		var msgError = 'No hay despachos con ese filtro',
			cbSuccess = function(json){
				llenarPantalla(json);
			};
		funciones.buscar(url, cbSuccess, msgError);
	}

	function divDatos(o) {
		var table = $('<table>').attr('class', 'w100p').attr('border', '0').append($('<tbody>'));
		table.append(
			$('<tr>').addClass('tableRow').append(
				$('<td>').addClass('bold aLeft').append(
					$('<label>').text(o.idCliente + ' - ' + o.razonSocialCliente + ' - Artículo: ' + o.articulo + ' - ' + o.color)
				)
			),
			$('<tr>').addClass('tableRow').append(
				$('<td>').addClass('aLeft').append(
					$('<label>').text('Fecha: ' + o.fecha + ' (despacho Nº ' + o.numeroDespacho + ' - ' + o.numeroItem + ')'),
					$('<label>').addClass('fRight').text('Importe: ' + funciones.formatearMoneda(o.importe) + ' - Pares: ' + o.cantidad)
				)
			)
		);
		return table;
	}

	function divEstado(o) {
		var div = $('<div>').addClass('aLeft');
		if (o.remitido == 'S')
			div.append(
				$('<img>').addClass('pLeft10').attr('src', '/img/varias/remitido.png')
			);
		return div;
	}

	function divBotones(o) {
		var div = $('<div>').addClass('botonera aCenter');
		var btn1;
		btn1 = $('<a>').addClass('boton').attr('href', '#').attr('title', (o.remitido == 'S' ? 'Ya está remitido' : 'Borrar'))
						.attr('onclick', (o.remitido == 'S' ? '' : 'borrar(' + o.numeroDespacho + ', ' + o.numeroItem + ')'))
						.append($('<img>').attr('src', '/img/botones/40/borrar' + (o.remitido == 'S' ? '_off' : '') + '.gif'));
		div.append(btn1);
		return div;
	}

	function returnTr(o) {
		return $('<tr>').attr('id', 'tr_' + o.numeroDespacho + '-' + o.numeroItem).append(
			$('<td>').addClass('w87p').append(divDatos(o)),
			$('<td>').addClass('w7p').append(divEstado(o)),
			$('<td>').addClass('w6p').append(divBotones(o))
		);
	}

	function llenarPantalla(json) {
		var div = $('#divReimpresionDespachos');
        var table = $('<table>').attr('id', 'tablaDespachos').attr('class', 'registrosAlternados w100p');
		for (var i = 0; i < json.length; i++) {
			table.append(returnTr(json[i]));
        }
        div.append(table);
	}

	function refrescarDespacho(numeroDespacho, numeroItem) {
		//Sólo sirve para sacar el despacho de la lista cuando se borra
		var o = $('#tr_' + numeroDespacho + '-' + numeroItem);
		var before = o.prev();
		if (before.length < 1)
			o.remove();
		else
			before.next().remove();
	}

	function borrar(numeroDespacho, numeroItem){
		var msg = '¿Está seguro que desea borrar el despacho Nº ' + numeroDespacho + ', item Nº ' + numeroItem + '?',
			url = '/content/comercial/despachos/reimpresion/borrar.php';
		$.confirm(msg, function(r){
			if (r == funciones.si){
				$.showLoading();
				$.postJSON(url, armoObjetoBorrar(numeroDespacho, numeroItem), function(json){
					$.hideLoading();
					switch (funciones.getJSONType(json)){
						case funciones.jsonNull:
						case funciones.jsonEmpty:
							$.error('Ocurrió un error');
							break;
						case funciones.jsonError:
							$.error(funciones.getJSONMsg(json));
							break;
						case funciones.jsonSuccess:
							$.success('El despacho se ha borrado correctamente', function(){
								refrescarDespacho(json.data.numeroDespacho, json.data.numeroItem);
							});
							break;
					}
				});
			}
		});
	}

	function armoObjetoBorrar(nroDespacho, nroItem){
		return {
				numeroDespacho: nroDespacho,
				numeroItem: nroItem
			};
	}

	function cambiarModo(modo){
		funciones.cambiarModo(modo);
		switch (modo){
			case 'inicio':
				$('.customRadio').enableRadioGroup();
				$('#divReimpresionDespachos').html('');
				break;
			case 'buscar':
				funciones.cambiarTitulo(tituloPrograma);
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
	<div id='divReimpresionDespachos' class='w100p customScroll acordeon h480'>
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
			<label class='filtroBuscar'>Desde:</label>
			<input id='inputBuscarDesde' class='textbox filtroBuscar w170' to='inputBuscarHasta' validate='Fecha' />
		</div>
		<div>
			<label class='filtroBuscar'>Hasta:</label>
			<input id='inputBuscarHasta' class='textbox filtroBuscar w170' from='inputBuscarDesde' validate='Fecha' />
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
			<label class='filtroBuscar'>Remitido:</label>
			<div id='radioGroupRemitido' class='customRadio w180 inline-block' default='rdRemitido_A'>
				<input id='rdRemitido_A' type='radio' name='radioGroupRemitido' value='A' /><label for='rdRemitido_A'>Ambas</label>
				<input id='rdRemitido_S' type='radio' name='radioGroupRemitido' value='S' /><label for='rdRemitido_S'>S</label>
				<input id='rdRemitido_N' type='radio' name='radioGroupRemitido' value='N' /><label for='rdRemitido_N'>N</label>
			</div>
		</div>
		<div>
			<label class='filtroBuscar'>Numero:</label>
			<input id='inputBuscarNumero' class='textbox filtroBuscar w190' validate='Entero' />
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
