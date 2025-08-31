<?php

?>

<style>
	#divArticulos {
		height: 440px;
	}
	#divArticulos.alterHeight {
		height: 320px;
	}
	.pad {
		padding: 0 3px;
	}
	#divDatosNotaDeCredito {
		height: 100px;
	}
	#tituloInfoNotaDeCredito {
		height: 20px;
	}

	/* PopUp Editar */
	#tablePopUpEditar {
		border-spacing: 1px;
	}
	#tablePopUpEditar th, #tablePopUpEditar .curva td {
		padding: 10px 20px;
	}
	#tablePopUpEditar .rowFinita td {
		padding: 2px 20px;
	}
</style>

<script type='text/javascript'>
	var articulos = {},
		objGlobal = new Array();

	$(document).ready(function(){
		tituloPrograma = 'Generación de notas de crédito (por devolución)';
		$('#btnBuscarAyudaArticulos').click(function(){funciones.delay('buscarAyudaArticulos();');});
		cambiarModo('inicio');
	});

	function limpiarScreen(){
		articulos = {};
		$('#divArticulos').html('<table id="tablaArticulos" class="w100p" cellspacing="1" border="0"></table>');
	}

	function escapeIdC(idC){
		return idC.replace(/\./g, '\\\.');
	}

	function buscarAyudaArticulos() {
		var idCli = $('#inputCliente_selectedValue').val(),
			idAlm = $('#inputAyudaAlmacen_selectedValue').val(),
			idArt = $('#inputAyudaArticulo_selectedValue').val(),
			idColor = $('#inputAyudaColor_selectedValue').val();
		if (idAlm != '' && idArt != '' && idColor != '') {
			//Me fijo si ya lo tengo en el objeto global. Si ya lo tengo, actualizo el que ya está
			var idCombinado = idCli + '_' + idAlm + '_' + idArt + '_' + idColor;
			if (!objGlobal[idCombinado]) {
				//Si no lo tengo, GETJSON! y lo agrego al global
				$.getJSON('/content/comercial/notas_de_credito/generacion/devolucion/getInfoArticulo.php?idCliente=' + idCli + '&idAlmacen=' + idAlm + '&idArticulo=' + idArt + '&idColor=' + idColor, function(json){
					switch (funciones.getJSONType(json)) {
						case funciones.jsonObject:
							//Lo agrego al global
							agregarGlobal(json.data);
							editarFila(idCombinado);
							break;
						default:
							$.error(funciones.getJSONMsg(json));
							//$.error('El artículo-color que intentó buscar no existe o no está disponible');
							break;
					}
				});
			} else {
				editarFila(idCombinado);
			}
		} else
			$('#inputAyudaAlmacen').focus();
	}

	function agregarGlobal(obj){
		var idC;
		var auxObj = {};
		idC = escapeIdC(obj.idCliente + '_' + obj.idAlmacen + '_' + obj.articulo.id + '_' + obj.color.id);
		auxObj.idCliente = obj.idCliente;
		auxObj.idAlmacen = obj.idAlmacen;
		auxObj.articulo = obj.articulo;
		auxObj.color = obj.color;
		auxObj.precios = obj.precios;
		auxObj.precio = 0;
		auxObj.cantidad = new Array();
		auxObj.idCombinado = idC;
		auxObj.cantidadTotal = function(){
			return funciones.sumaArray(this.cantidad);
		};
		auxObj.setTotalPares = function(){
			$(this.tr).find('.posicion_0').text(this.cantidadTotal);
		};
		auxObj.cantPos = 0;
		for(i in obj.articulo.rangoTalle.posicion) {
			if (obj.articulo.rangoTalle.posicion[i])
				auxObj.cantPos++;
		}
		auxObj.tr = false;
		auxObj.posiciones = false;
		objGlobal[idC] = auxObj;
	}

	function crearCombobox(arrayPrecios) {
		var opciones = '';
		for (var precio in arrayPrecios) {
			//Meto la opción al principio
			opciones = '<option value="' + arrayPrecios[precio] + '">' + funciones.formatearMoneda(precio) + '</option>' + opciones;
		}
		return '<select id="cboPrecios">' + opciones + '</select>';
	}

	function editarFila(idCombinado){
		idCombinado = escapeIdC(idCombinado);
		var div = '';
		var o = objGlobal[idCombinado];
		var cantPos = o.cantPos;
		div += '<div id="divPopUpEditar">';
		div += '<div>';
		div += '<div class="p10 fLeft">';
		div += '<label class="bold">Precios: </label>';
		div += crearCombobox(o.precios);
		div += '</div>';
		div += '<div class="p10 fRight">';
		div += '<label class="bold">';
		div += o.articulo.nombre + ' - ' + o.color.nombre;
		div += '</label>';
		div += ' (' + o.articulo.id + ' - ' + o.color.id + ')';
		div += '</div>';
		div += '</div>';
		div += '<table id="tablePopUpEditar">';
		div += '<thead>';
		div += '<tr class="tableHeader">';
		div += '<th></th>';
		for (var j = 1; j <= cantPos; j++)
			div += '<th>' + o.articulo.rangoTalle.posicion[j] + '</th>';
		div += '</tr>';
		div += '</thead>';
		div += '<tbody>';
		div += '<tr class="tableRow bGray curva">';
		div += '<td id="l_curva"></td>';
		for (var j = 1; j <= cantPos; j++){
			div += '<td id="l_p' + j + '" class="aCenter">';
			div += '<input id="input_l_p' + j + '" class="parcial textbox w25 aCenter" maxlength="3" type="text" onblur="ponerTotal(this, ' + j + ')" value="0" />';
			div += '</td>';
		}
		div += '</tr>';
		div += '</tbody>';
		div += '<tfoot>';
		div += '<tr class="tableRow bWhite rowFinita">';
		div += '<td id="tot_empty1 aCenter">Total</td>';
		for (var j = 1; j <= cantPos; j++)
			div += '<td id="tot_p' + j + '" class="aCenter bold totalDetalle" posicion="' + j + '">0</td>';
		div += '<td><label id="labelTotalPares" class="bold">0</label></td>';
		div += '</tr>';
		div += '</foot>';
		div += '</table>';
		div += '</div>';
		div = $(div);
		var botones = [{value: 'Guardar', action: function(){popUpGuardar(idCombinado);}}, {value: 'Cancelar', action: function(){$.jPopUp.close();}}];
		$.jPopUp.show(div, botones, function(){vaciarAyuda();});
	}

	function popUpGuardar(idCombinado){
		var contador = 0;
		var o = objGlobal[idCombinado];
		$('.totalDetalle').each(function(){
			var pos = funciones.toInt($(this).attr('posicion')),
				cant = funciones.toInt($(this).text());
			contador += cant;
			o.cantidad[pos] = cant;
		});
		if (contador > 0) { //Como tiene más de un item, agrego las cantidades al objeto a guardar y agrego la fila 
			articulos[idCombinado] = {
				almacen: o.idAlmacen,
				articulo: o.articulo.id,
				color: o.color.id,
				cantidades: o.cantidad,
				precio: $('#cboPrecios').val()
			};
			objGlobal[idCombinado].precio = funciones.toFloat($('#cboPrecios').selectedText());
			agregarFila(idCombinado);
		}
		calculaTotales();
		$.jPopUp.close(function(){
			$('#tr_' + idCombinado).animate({opacity: '0.05'}, 300, function(){$(this).animate({opacity: '1'}, 300);});
		});
	}

	function ponerTotal(input, nroTotal) {
		var paresActuales = 0;
		$.each($('.parcial'), function(index, value) {
			paresActuales += funciones.toInt($(value).val());
		});
		$('#labelTotalPares').html(paresActuales);

		if (!funciones.esNatural(input.value))
			input.value = '0';
		$('#tot_p' + nroTotal).text(input.value);
	}

	function agregarFila(idC) {
		var obj = objGlobal[idC];
		var fotoUrl = 'http://www.spiralshoes.com/zapatillas/' + obj.articulo.id + obj.color.id + '.png';
		var tr =
			$('<tr>').attr('id', 'tr_' + idC).addClass('tableRow').append(
				$('<td>').addClass('aCenter').attr('colspan', '1').append(
						$('<img>').addClass('cPointer').attr('onclick', 'ampliarFoto(this)').attr('src', fotoUrl).width(110).height(55)
					),
				$('<td>').addClass('aCenter bBottomDarkGray').attr('colspan', '1').append(
					$('<div>').addClass('bold fLeft').append(
						$('<label>').text(obj.articulo.id + ' ' + obj.color.id + ' (' + obj.idAlmacen + ') - ' + obj.articulo.nombre + ' ' + obj.color.nombre)
					),
					$('<div>').addClass('fRight pRight10').append(
						$('<label>').text(funciones.formatearMoneda(obj.precio) + ' x ' + obj.cantidadTotal() + ' = '),
						$('<label>').text(funciones.formatearMoneda(obj.precio * obj.cantidadTotal()))
					)
				),
				$('<td>').addClass('aCenter vaBottom table-cell bBottomDarkGray').attr('colspan', '1').append(tablaDetalle(obj)),
				$('<td>').addClass('aCenter').attr('colspan', '1').append(
					$('<label>').addClass('underline blue cPointer').attr('onclick', 'editarFila("' + idC + '")').text('Editar'),
					$('<br>'),
					$('<label>').addClass('underline blue cPointer').attr('onclick', 'quitarFila("' + idC + '")').text('Quitar')
				)
			);
		if (objGlobal[idC].tr)
			objGlobal[idC].tr.replaceWith(tr);
		else
			$('#tablaArticulos').append(tr);
		objGlobal[idC].tr = $('#tr_' + idC);
		objGlobal[idC].posiciones = new Array();
		for (var i = 1; i <= objGlobal[idC].cantPos; i++) {
			objGlobal[idC].posiciones[i] = $('#tr_' + idC).find('.posicion_' + i);
			objGlobal[idC].posiciones[i].text(funciones.toInt(objGlobal[idC].cantidad[i]));
		}
		$('#tr_' + idC).find('.posicion_0').text(funciones.toInt(funciones.sumaArray(objGlobal[idC].cantidad)));
	}

	function setTotalPares(){
		var pares = 0;
		$('.posicion_0').each(function(){
			pares += funciones.toInt($(this).text());
		});
		$('#labelDatosPares').text(pares);
	}

	function calculaTotales(){
		$(objGlobal).each(function(){
			this.setTotalPares();
		});
		setTotalPares();
	}

	function newTd(numero, esTitulo) {
		var td = $('<td>').addClass('aCenter');
		if (esTitulo) {
			td.addClass('aCenter bold bRightWhite pad white').append($('<label>').text((numero == 0 ? 'Total' : numero)));
		} else {
			td.addClass('aCenter bRightDarkGray').append($('<label>').addClass('posicion_' + numero).text((numero == 0 ? '' : '')));
		}
		return td;
	}

	function tablaDetalle(obj) {
		var tr1 = $('<tr>').addClass('bDarkGray');
		var tr2 = $('<tr>').addClass('tableRow');
		for (var i = 1; i < 9; i++) {
			tr1.append(newTd(i, true));
		}
		tr1.append(newTd(0, true));
		for (var i = 1; i < 9; i++) {
			tr2.append(newTd(i, false));
		}
		tr2.append(newTd(0, false));
		var tabla = $('<table>').addClass('w100p').attr('border', '0').append($('<tbody>').append(tr1, tr2));
		return tabla;
	}

	function quitarFila(idC){
		objGlobal[idC].tr.remove();
		delete objGlobal[idC];
		delete articulos[idC];
		calculaTotales();
	}

	function vaciarAyuda(){
		$('#inputAyudaAlmacen').limpiarAutoSuggestBox();
		$('#inputAyudaArticulo').limpiarAutoSuggestBox();
		$('#inputAyudaColor').limpiarAutoSuggestBox();
		$('#inputAyudaAlmacen').focus();
	}

	function hayErrorGuardar(){
		if ($('#inputCliente_selectedValue').val() == '')
			return 'Debe seleccionar un cliente';
		if ($('#inputCausa_selectedValue').val() == '')
			return 'Debe seleccionar una causa';
		if (funciones.objectLength(articulos) == 0)
			return 'Debe ingresar algún artículo';
		return false;
	}

	function guardar(){
		var url = '/content/comercial/notas_de_credito/generacion/devolucion/agregar.php?';
		funciones.guardar(url, armoObjetoGuardar());
	}

	function armoObjetoGuardar(){
		return {
			idCliente: $('#inputCliente_selectedValue').val(),
			idCausa: $('#inputCausa_selectedValue').val(),
			observaciones: $('#inputObservaciones').val(),
			articulos: articulos
		};
	}

	function ampliarFoto(obj){
		$.jPopUp.show($('<div class="w400 h200 vaBottom table-cell"><img src="' + obj.src + '" width="400" height="200" />'), [{value: 'Cerrar', action: function(){$.jPopUp.close();}}]);
	}

	function ocultarDatos(click){
		if (typeof click === 'undefined')
			click = false;
		if ((!click) || ($('#inputCliente_selectedValue').val() != '')) {
			$('#divDatosTitulo').show();
			$('#labelMostrarDatos').show();
			$('#labelOcultarDatos').hide();
			$('#divArticulos').removeClass('alterHeight');
			$('#divDatosNotaDeCredito').slideUp();
			funciones.delay('ponerDatos();');
			if ((click) && (!$('#divArticulos').isVisible())) {
				$('#divArticulos').slideDown();
				$('#btnBuscar').show().focus();
			}
		} else {
			$.error('Debe seleccionar un cliente y una causa antes de continuar');
		}
	}

	function ponerDatos(){
		$('#labelDatosCliente').text($('#inputCliente_selectedValue').val() + ' - ' + $('#inputCliente_selectedName').val());
		$('.labelDatosSeparador').text(' | ');
		setTotalPares();
	}

	function mostrarDatos(){
		$('#divDatosTitulo').hide();
		$('#labelMostrarDatos').hide();
		$('#labelOcultarDatos').show();
		$('#divArticulos').addClass('alterHeight');
		$('#divDatosNotaDeCredito').slideDown();
		$('#divArticulos').slideUp();
		$('#filtro').draggableDialogHide();
		$('#btnBuscar').hide();
	}

	function cambiarModo(modo){
		funciones.cambiarModo(modo);
		switch (modo){
			case 'inicio':
				$('#inputBuscarCliente').enable();
				$('#btnBuscar').hide();
				$('#divInfoNotaDeCredito').slideUp();
				$('#divArticulos').slideUp();
				ocultarDatos();
				break;
			case 'buscar':
				break;
			case 'editar':
				break;
			case 'agregar':
				$('#inputBuscarCliente').disable();
				$('#btnBuscar').show();
				$('#divInfoNotaDeCredito').slideDown();
				$('#divDatosEditarGuardar').show();
				$('#divArticulos').slideUp();
				mostrarDatos();
				$('#inputCliente').focus();
				break;
		}
	}
</script>

<div id='programaTitulo'></div>
<div id='programaContenido'>
	<div id='divInfoNotaDeCredito' class='w100p mBottom10 corner10 bAllOrange bWhite hidden'>
		<div id='divDatosNotaDeCredito' class='hidden'>
			<div class='fLeft'>
				<?php
					$tabla = new HtmlTable(array('cantRows' => 3, 'cantCols' => 2, 'id' => 'tablaDatos', 'cellSpacing' => 10));
					$tabla->getRowCellArray($rows, $cells);

					$cells[0][0]->content = '<label>Cliente:</label>';
					$cells[0][0]->style->width = '165px';
					$cells[0][1]->content = '<input id="inputCliente" class="textbox autoSuggestBox obligatorio inputForm noEditable w230" name="ClienteTodos" rel="cliente" />';
					$cells[0][1]->style->width = '260px';
					$cells[1][0]->content = '<label>Causa:</label>';
					$cells[1][1]->content = '<input id="inputCausa" class="textbox autoSuggestBox obligatorio inputForm w230" name="CausaNotaDeCredito" rel="causa" />';

					$tabla->create();
				?>
			</div>
			<div class='fRight'>
				<?php
					$tabla = new HtmlTable(array('cantRows' => 1, 'cantCols' => 2, 'id' => 'tablaDatos', 'cellSpacing' => 10));
					$tabla->getRowCellArray($rows, $cells);

					$cells[0][0]->content = '<label>Observaciones:</label>';
					$cells[0][0]->style->width = '165px';
					$cells[0][1]->content = '<textarea id="inputObservaciones" onblur="$(\'#labelOcultarDatos\').parent().focus();" class="textbox inputForm w230" rel="observaciones"></textarea>';
					$cells[0][1]->style->width = '260px';

					$tabla->create();
				?>
			</div>
		</div>
		<div id='tituloInfoNotaDeCredito' class='bold white bLightOrange corner10 p5'>
			<div id='divDatosTitulo' class='fLeft'>
				<label id='labelDatosCliente' title='Cliente'></label>
				<label class='labelDatosSeparador'></label>
				<label id='labelDatosPares' title='Pares'></label><label> pares</label>
			</div>
			<div id='divDatosEditarGuardar' class='fRight pRight15'>
				<a href='#' onclick='mostrarDatos();'><label id='labelMostrarDatos' class='cPointer s19'>+</label></a>
				<a class='borderFocusDarkOrange' href='#' onclick='ocultarDatos(true);'><label id='labelOcultarDatos' class='cPointer hidden p5'>Siguiente</label></a>
			</div>
		</div>
	</div>
	<div id='divArticulos' class='fLeft w100p customScroll acordeon hidden'>
		<?php // TABLOTA ?>
	</div>
</div>
<div id='programaPie'>
	<div id='filtro' class='hidden draggableDialog'>
		<div>
			<label class='ayudaArticulos'>Almacén:</label>
			<input id='inputAyudaAlmacen' class='textbox autoSuggestBox ayudaArticulos w200' name='Almacen' alt='' />
		</div>
		<div>
			<label class='ayudaArticulos'>Artículo:</label>
			<input id='inputAyudaArticulo' class='textbox autoSuggestBox ayudaArticulos w200' name='Articulo' alt='' />
		</div>
		<div>
			<label class='ayudaArticulos'>Color:</label>
			<input id='inputAyudaColor' class='textbox autoSuggestBox ayudaArticulos w200' name='ColorPorArticulo' linkedTo='inputAyudaArticulo,Articulo' alt='' />
		</div>
		<div>
			<a id='btnBuscarAyudaArticulos' class='boton' href='#' title='Buscar'><img src="/img/botones/25/buscar.gif" /></a>
		</div>
	</div>
	<div class='botonera'>
		<?php Html::echoBotonera(array('boton' => 'buscar', 'accion' => 'funciones.buscarClick();')); ?>
		<?php Html::echoBotonera(array('boton' => 'agregar', 'accion' => 'funciones.agregarClick();', 'permiso' => 'comercial/notas_de_credito/generacion/devolucion/agregar/')); ?>
		<?php Html::echoBotonera(array('boton' => 'guardar', 'accion' => 'funciones.guardarClick();')); ?>
		<?php Html::echoBotonera(array('boton' => 'cancelar', 'accion' => 'funciones.cancelarBuscarClick();', 'id' => 'btnCancelarBuscar')); ?>
	</div>
</div>
