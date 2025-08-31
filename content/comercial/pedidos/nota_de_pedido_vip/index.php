<?php

/**
 * Valores que se editan de la tabla:
 * 
 * La cantidad total de pares de la categoría			#spanCantidadPares_IDCAT
 * El precio total de la categoria						#spanTotal_IDCAT
 * 
 * La cantidad total de un articulo						#nombre_IDART
 * El id del artículo (saber en cuál hacen click)		.idArticulo (IDART)
 * La foto de un articulo								#foto_IDART
 * El color de un articulo								#color_IDART
 * El precio al público de un articulo					#precioPub_IDART
 * El precio de facturación de un articulo				#precioFac_IDART
 * El tipo de curva de un articulo						#curva_IDART).attr('tipo')
 * La fecha de disponibilidad de un articulo			#fechaDisp_IDART
 * Una cantidad de un talle de un articulo				#tablaPosiciones_IDART.posicion_NROPOSICION
 * La cantidad total de un articulo						#cantidad_IDART
 * El precio total de un articulo						#total_IDART
 * 
 * Todas las cantidades									.cantidadArt
 * Todos los totales									.totalArt
 * Todas las cantidades de Categoría					.spanCantidadPares
 * Todos los totales de Categoría						.spanTotal
 * 
 */


function generoFdp(){
	$formasDePago = '';
	foreach (Factory::getInstance()->getListObject('FormaDePago', 'anulado = \'N\'') as $fdp) {
		$formasDePago .= '<input id="rdFormaDePago_' . $fdp->id . '" class="textbox inputFormaDePago" ';
		$formasDePago .= 'type="radio" name="radioGroupFormaDePago" value="' . $fdp->id . '" rel="id" />';
		$formasDePago .= '<label for="rdFormaDePago_' . $fdp->id . '">' . $fdp->nombre . '</label>';
	}
	return $formasDePago;
}
?>

<style>
	#divInicial {
		margin-top: 170px;
	}
	#divCampos1 {
		height: 440px;
	}
	#divCampos1.alterHeight {
		height: 140px;
	}
	.pad {
		padding: 0 3px;
	}
	.pad2 {
		padding: 5px 0;
	}
	#divAutorizaciones {
		height: 40px;
	}
	#divDatosNotaDePedido {
		height: 260px;
	}
	#tituloInfoNotaDePedido {
		height: 20px;
	}

	.agotado {
		position: relative;
		-webkit-transform: rotate(-30deg);
		font-size: 20px;
		font-weight: bold;
		background-color: red;
		color: white;
		z-index: 2;
	}
	.agotado.top {
		top: 25px;
	}

	.notop {
		position: relative;
		top: -10px;
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
	var notaDePedido = {},
		objGlobal = [],
		listaAplicable = 'N',
		buscarAlCargar = false,
		buscarAlCargarAsignado = false;

	$(document).ready(function(){
		tituloPrograma = 'Nota de pedido VIP';
		$('#botonAgregarGrande').hover(function(){
			$(this).css('position', 'relative').animate({
				height: '+=25px',
				top: '-=25px'
			});
		}, function(){
			$(this).css('position', 'none').animate({
				height: '-=25px',
				top: '+=25px'
			});
			
		});
		$.showLoading();
		$.get('/content/comercial/pedidos/nota_de_pedido_vip/getTablaArticulos.php', function(html){
			$('#divCampos1').html(html);
			$.hideLoading();
		});
		$('#inputSucursal').focus(function(){
			$(this).attr('alt', 'idCliente=' + $('#inputCliente_selectedValue').val());
		});
		$('#inputCliente').blur(function(){funciones.delay('ponerVendedor();');});
		$('#btnBuscarAyudaArticulos').click(function(){funciones.delay('buscarAyudaArticulos();');});
		cambiarModo('inicio');
	});

	function loadMe(obj) {
		var idCategoria = obj.attr('idcategoria');
		loadCategoria(idCategoria);
	}

	function loadCategoria(idCategoria, fromRestantes) {
		var div = $('#divCategoria_' + idCategoria);
		if (div.attr('loaded'))
			return;
		if (!fromRestantes)
			$.showLoading();
		$.get('/content/comercial/pedidos/nota_de_pedido_vip/getTablaArticulos.php?idCategoria=' + idCategoria, function(html){
			div.html(html);
			$('#divCategoria_' + idCategoria + ' .tablaCategoria').each(function(){
				var trs = $('#' + this.id + '>tbody>tr');
				for (var i = 0; i < trs.length; i++){
					tr = trs[i];
					var idCombinado = escapeIdC($(tr).find('.idArticulo').text() + '_' + $(tr).find('.idColor').text());
					crearObjetoGlobal(idCombinado);
				}
				div.attr('loaded', '1');
				if (!fromRestantes) $.hideLoading();
			});
		});
	}

	function escapeIdC(idC){
		return idC.replace(/\./g, '\\\.');
	}

	function crearObjetoGlobal(idC){
		var auxObj = {};
		auxObj.idArticulo = $('#articulo_' + idC).text();
		auxObj.idColor = $('#color_' + idC).text();
		auxObj.color = $('#color_' + idC).attr('title');
		auxObj.idCombinado = escapeIdC(auxObj.idArticulo + '_' + auxObj.idColor);
		auxObj.idCategoria = $('#tr_' + idC).parents('.tablaCategoria:first').attr('id').split('_')[1];
		auxObj.nombre = $('#nombre_' + idC).text();
		auxObj.precioPub = $('#precioPub_' + idC).text();
		auxObj.precioFac = function(){
			return $('#precioFac_' + this.idCombinado + ' label').not('.hidden').text();
		};
		auxObj.curva = {};
		auxObj.curva.tipo = $('#curva_' + idC).attr('tipo');
		auxObj.curva.cantPos = funciones.toInt($('#curva_' + idC).attr('cantPos'));
		auxObj.talles = [];
		$('#tablaPosiciones_' + idC + ' .talle').each(function(i){
			auxObj.talles[i + 1] = $(this).text();
		});
		auxObj.posiciones = [];
		$('#tablaPosiciones_' + idC + ' .posicion').each(function(i){
			auxObj.posiciones[i + 1] = $(this);
		});
		auxObj.cantidad = function(){
			var temp = 0;
			$(this.posiciones).each(function(){
				temp += funciones.toInt($(this).text());
			});
			$('#cantidad_' + idC).text(temp);
			return temp;
		};
		auxObj.total = $('#total_' + idC);
		auxObj.vaciar = function(){
			$('#tablaPosiciones_' + this.idCombinado + ' .posicion').text(0);
			this.cantidad();
		};
		objGlobal[idC] = auxObj;
	}

	function corregirPrecios() {
		if (listaAplicable == 'D'){
			$('.precioN').addClass('hidden');
			$('.precioD').removeClass('hidden');
		} else {
			$('.precioD').addClass('hidden');
			$('.precioN').removeClass('hidden');
		}
	}

	function buscarAyudaArticulos() {
		var idArt = $('#inputAyudaArticulo_selectedValue').val(),
			idColor = $('#inputAyudaColor_selectedValue').val();
		if (idArt != '' && idColor != '') {
			var idCombinado = idArt + '_' + idColor;
			if (objGlobal[idCombinado])
				editarFila(idArt + '_' + idColor);
			else
				$.error('El artículo-color que intentó buscar no es parte de la nota de pedido');
		} else
			$('#inputAyudaArticulo').focus();
	}

	function ponerVendedor(){
		$.postJSON('/content/comercial/pedidos/nota_de_pedido_vip/getInfoCliente.php?idCliente=' + $('#inputCliente_selectedValue').val(), function(json){
			$('#inputVendedor').autoComplete(json.data.vendedor);
			listaAplicable = json.data.listaAplicable;
		});
	}

	function manejarCustomRadios(modo){
		switch(modo){
			case 'inicio':
				//$('#radioGroupFormaDePago').disableRadioGroup();
				break;
			case 'agregar':
				//$("#radioGroupFormaDePago").enableRadioGroup();
				break;
		}
	}

	function mostrarTodo(){
		$('.solapas').solapas({fixedHeight: 400, heightSolapas: 47, precall: loadMe, selectedItem: 0});
		$('.tablaCategoria tr').show();
	}

	function ocultarDatos(click){
		if (typeof click === 'undefined')
			click = false;
		if ((!click) || ($('#inputCliente_selectedValue').val() != '' && $('#inputSucursal_selectedValue').val() != '' && $('#inputAlmacen_selectedValue').val() != '' && $('#inputTemporada_selectedValue').val() != '')) {
			$('#divDatosTitulo').show();
			$('#labelMostrarDatos').show();
			$('#labelOcultarDatos').hide();
			$('#divCampos1').removeClass('alterHeight');
			$('#divDatosNotaDePedido').slideUp();
			funciones.delay('ponerDatos();');
			if ((click) && (!$('#divCampos1').isVisible())) {
				$('#divCampos1').slideDown();
				$('.ayudaArticulos').show();
				$('#btnBuscar').show().focus();
			}
			corregirPrecios();
		} else {
			$.error('Debe seleccionar un cliente, una sucursal, un almacén, y la temporada antes de continuar');
		}
	}

	function ponerDatos(){
		$('#labelDatosCliente').text($('#inputCliente_selectedName').val());
		$('#labelDatosSucursal').text($('#inputSucursal_selectedName').val());
		$('#labelDatosEstado').text($('#labelEstado').text());
		var desc = funciones.toFloat($('#inputDescuento').val());
		var rec = funciones.toFloat($('#inputRecargo').val());
		setTotalTotal();
		setTotalPares();
		$('#labelDatosTotal').text($('#labelTotal').text());
		$('#labelDatosDescuento').text(((desc > 0) ? ' (' + (desc) + '% descuento)' : '') + ((rec > 0) ? ' (' + (rec) + '% recargo)' : ''));
		$('.labelDatosSeparador').text(' | ');
	}

	function mostrarDatos(){
		if ($('#inputAlmacen_selectedValue').val() == '')
			$('#inputAlmacen').val('01').autoComplete();
		if ($('#inputTemporada_selectedValue').val() == '')
			$('#inputTemporada').val('9').autoComplete();
		$('#divDatosTitulo').hide();
		$('#labelMostrarDatos').hide();
		$('#labelOcultarDatos').show();
		$('#divCampos1').addClass('alterHeight');
		$('#divDatosNotaDePedido').slideDown();
		$('#divCampos1').slideUp();
		$('.ayudaArticulos').hide();
		$('#btnBuscar').hide();
	}

	function limpiarScreen(){
		notaDePedido = {};
		$('.labelForm').text('');
		$('#divAutorizaciones').html('');

		$('.posicion').text('0');
		$('.cantidadArt').text('0');
		$('.totalArt').text(funciones.formatearMoneda('0'));
		$('.spanCantidadPares').text('0');
		$('.spanTotal').text(funciones.formatearMoneda('0'));
	}

	function ampliarFoto(link){
		$.jPopUp.show($('<div class="w600 h300 vaBottom table-cell aCenter"><img src="' + link + '" height="300" />'), [{value: 'Cerrar', action: function(){$.jPopUp.close();}}]);
	}

	function getCantidadCat(idCategoria){
		var cantidad = 0;
		$('#tablaCategoria_' + idCategoria + ' .cantidadArt').each(function(){
			cantidad += funciones.toInt($(this).text());
		});
		return cantidad;
	}
	function setCantidadCat(idCategoria){
		var cantidad = getCantidadCat(idCategoria);
		return $('#spanCantidadPares_' + idCategoria).text(cantidad);
	}

	function getTotalCat(idCategoria){
		var total = 0;
		$('#tablaCategoria_' + idCategoria + ' .totalArt').each(function(){
			total += funciones.toFloat($(this).text());
		});
		return total;
	}
	function setTotalCat(idCategoria){
		var total = getTotalCat(idCategoria);
		return $('#spanTotal_' + idCategoria).text(funciones.formatearMoneda(total));
	}

	function setTotalPares(){
		var pares = 0;
		$('.spanCantidadPares').each(function(){
			pares += funciones.toInt($(this).text());
		});
		$('#labelDatosPares').text(pares);
	}

	function setTotalTotal(){
		var total = 0;
		$('.spanTotal').each(function(){
			total += funciones.toFloat($(this).text());
		});
		var newTotal = total;
		newTotal -= total * (funciones.toFloat($('#inputDescuento').val()) / 100);
		newTotal += total * (funciones.toFloat($('#inputRecargo').val()) / 100);
		$('#labelTotal').text(funciones.formatearMoneda(newTotal));
		$('#labelDatosTotal').text(funciones.formatearMoneda(newTotal));
	}

	function calculaTotales(categoria){
		var selector = '#tablaCategoria_' + categoria;
		if (typeof categoria === 'undefined')
			selector = '.tablaCategoria';
		$(selector).each(function(){
			var idCategoria = this.id.split('_')[1];
			var trs = $('#' + this.id + '>tbody>tr');
			for (var i = 0; i < trs.length; i++){
				tr = trs[i];
				var idCombinado = escapeIdC($(tr).find('.idArticulo').text() + '_' + $(tr).find('.idColor').text());
				objGlobal[idCombinado].cantidad(); //Recalcula la cantidad y la pone
				objGlobal[idCombinado].total.text(funciones.formatearMoneda(funciones.toInt(objGlobal[idCombinado].cantidad()) * funciones.toFloat(objGlobal[idCombinado].precioFac())));
			}
			setCantidadCat(idCategoria);
			setTotalCat(idCategoria);
		});
		setTotalPares();
		setTotalTotal();
	}

	function vaciarFila(idCombinado){
		delete notaDePedido[idCombinado];
		objGlobal[idCombinado].vaciar();
		return calculaTotales(objGlobal[idCombinado].idCategoria);
	}

	function editarFila(idCombinado){
		idCombinado = escapeIdC(idCombinado);
		var j;
		var div = '';
		var cantPos = objGlobal[idCombinado].curva.cantPos;
		div += '<div id="divPopUpEditar">';
		div += '<div>';
		div += '<div class="p10 aRight">';
		div += '<label class="bold">';
		div += objGlobal[idCombinado].nombre + ' - ' + objGlobal[idCombinado].color;
		div += '</label>';
		div += ' (' + objGlobal[idCombinado].idArticulo + ' - ' + objGlobal[idCombinado].idColor + ')';
		div += '</div>';
		div += '</div>';
		div += '<table id="tablePopUpEditar">';
		div += '<thead>';
		div += '<tr class="tableHeader">';
		div += '<th></th>';
		for (j = 1; j <= cantPos; j++)
			div += '<th>' + objGlobal[idCombinado].talles[j] + '</th>';
		div += '</tr>';
		div += '</thead>';
		div += '<tbody>';
		div += '<tr class="tableRow bGray curva">';
		div += '<td id="l_curva"></td>';
		for (j = 1; j <= cantPos; j++){
			div += '<td id="l_p' + j + '" class="aCenter">';
			div += '<input id="input_l_p' + j + '" class="textbox w25 aCenter" maxlength="3" type="text" onblur="ponerTotal(this, ' + j + ')" value="0" />';
			div += '</td>';
		}
		div += '</tr>';
		div += '</tbody>';
		div += '<tfoot>';
		div += '<tr class="tableRow bWhite rowFinita">';
		div += '<td id="tot_empty1 aCenter">Total</td>';
		for (j = 1; j <= cantPos; j++)
			div += '<td id="tot_p' + j + '" class="aCenter bold totalDetalle" posicion="' + j + '">0</td>';
		div += '</tr>';
		div += '</foot>';
		div += '</table>';
		div += '</div>';
		div = $(div);
		var botones = [{value: 'Guardar', action: function(){popUpGuardar(idCombinado);}}, {value: 'Cancelar', action: function(){$.jPopUp.close();}}];
		$.jPopUp.show(div, botones, function(){vaciarAyuda();});
	}

	function vaciarAyuda(){
		$('#inputAyudaArticulo').limpiarAutoSuggestBox();
		$('#inputAyudaColor').limpiarAutoSuggestBox();
		$('#inputAyudaArticulo').focus();
	}

	function popUpGuardar(idCombinado){
		notaDePedido[idCombinado] = {};
		var aux = '';
		var contador = 0;
		$('.totalDetalle').each(function(){
			var cant = funciones.toInt($(this).text());
			contador += cant;
			aux += cant + '-';
		});
		aux = aux.substr(0, aux.length - 1);
		if (contador > 0)
			notaDePedido[idCombinado]['L'] = aux;
		$('.totalDetalle').each(function(){
			objGlobal[idCombinado].posiciones[funciones.toInt($(this).attr('posicion'))].text($(this).text());
		});
		calculaTotales(objGlobal[idCombinado].idCategoria);
		$.jPopUp.close(function(){
			$('#tr_' + idCombinado).animate({opacity: '0.05'}, 300, function(){$(this).animate({opacity: '1'}, 300);});
		});
	}

	function ponerTotal(input, nroTotal) {
		if (!funciones.esNatural(input.value))
			input.value = '0';
		$('#tot_p' + nroTotal).text(input.value);
	}

	function hayErrorGuardar(){
		if ($('#inputCliente_selectedValue').val() == '')
			return 'Debe seleccionar un cliente';
		if ($('#inputSucursal_selectedValue').val() == '')
			return 'Debe seleccionar una sucursal';
		if ($('#inputTemporada_selectedValue').val() == '')
			return 'Debe seleccionar la temporada del pedido';
		if (funciones.objectLength(notaDePedido) == 0)
			return 'Debe elegir algún artículo';
		return false;
	}

	function guardar(){
		var url = '/content/comercial/pedidos/nota_de_pedido_vip/agregar.php?';
		funciones.guardar(url, armoObjetoGuardar());
	}

	function armoObjetoGuardar(){
		return {
			idNotaDePedido: $('#inputBuscar_selectedValue').val(),
			idCliente: $('#inputCliente_selectedValue').val(),
			idSucursal: $('#inputSucursal_selectedValue').val(),
			idAlmacen: $('#inputAlmacen_selectedValue').val(),
			observaciones: $('#inputObservaciones').val(),
			/*formaDePago: $('#radioGroupFormaDePago').radioVal(),*/
			idTemporada: $('#inputTemporada_selectedValue').val(),
			idVendedor: $('#inputVendedor_selectedValue').val(),
			descuento: funciones.toFloat($('#inputDescuento').val()),
			recargo: funciones.toFloat($('#inputRecargo').val()),
			notaDePedido: notaDePedido
		};
	}

	function cambiarModo(modo){
		funciones.cambiarModo(modo);
		switch (modo){
			case 'inicio':
				$('.ayudaArticulos').hide();
				$('.solapas label[onclick]').hide();
				manejarCustomRadios(modo);
				$('#divInicial').slideDown();
				$('#divInfoNotaDePedido').slideUp();
				$('#divCampos1').slideUp();
				mostrarTodo();
				ocultarDatos();
				$('.solapas').solapas({fixedHeight: 400, heightSolapas: 47, precall: loadMe}).restart();
				break;
			case 'agregar':
				$('.ayudaArticulos').show();
				$('.solapas label[onclick]').show();
				manejarCustomRadios(modo);
				$('#divInicial').slideUp();
				$('#divInfoNotaDePedido').slideDown();
				$('#divDatosEditarGuardar').show();
				$('#divCampos1').slideUp();
				mostrarTodo();
				mostrarDatos();
				$('#inputCliente').focus();
				break;
		}
	}
</script>

<div id='programaTitulo'></div>
<div id='programaContenido'>
	<div id='divInicial' class='aCenter'>
		<a id='botonAgregarGrande' class='boton' href='#' title='Agregar' onclick='funciones.agregarClick()'><img class='p15' src='/img/botones/personales/mas.jpg' /></a>
	</div>
	<div id='divInfoNotaDePedido' class='w100p mBottom10 corner10 bAllOrange bWhite hidden'>
		<div id='divDatosNotaDePedido' class='hidden'>
			<div id='divCampos21' class='fLeft w50p'>
				<?php
					$tabla = new HtmlTable(array('cantRows' => 7, 'cantCols' => 2, 'id' => 'tablaDatos', 'cellSpacing' => 10));
					$tabla->getRowCellArray($rows, $cells);

					$cells[0][0]->content = '<label>Estado:</label>';
					$cells[0][0]->style->width = '165px';
					$cells[0][1]->content = '<label id="labelEstado" class="labelForm" rel="estado"></label>';
					$cells[0][1]->style->width = '260px';
					$cells[1][0]->content = '<label>Cliente:</label>';
					$cells[1][1]->content = '<input id="inputCliente" class="textbox autoSuggestBox obligatorio ' . (Usuario::logueado()->esCliente() ? '' : 'inputForm') . ' noEditable w230" name="ClienteTodos" rel="cliente" />';
					$cells[2][0]->content = '<label>Sucursal:</label>';
					$cells[2][1]->content = '<input id="inputSucursal" class="textbox autoSuggestBox obligatorio inputForm noEditable w230" name="Sucursal" linkedTo="inputCliente,Cliente" rel="sucursal" />';
					$cells[3][0]->content = '<label>Almacén:</label>';
					$cells[3][1]->content = '<input id="inputAlmacen" class="textbox autoSuggestBox obligatorio inputForm noEditable w230" name="Almacen" rel="almacen" value="01" />';
					$cells[4][0]->content = '<label>Total:</label>';
					$cells[4][1]->content = '<label id="labelTotal" class="labelForm"></label>';
					$cells[5][0]->content = '<label>Observaciones:</label>';
					$cells[5][1]->content = '<textarea id="inputObservaciones" onblur="$(\'#labelOcultarDatos\').parent().focus();" class="textbox inputForm w230" rel="observaciones"></textarea>';
					$cells[6][0]->content = '<label>Usuario alta:</label>';
					$cells[6][1]->content = '<label id="labelUsuario" class="labelForm"></label>';

					$tabla->create();
				?>
			</div>
			<div id='divCampos22' class='fRight w50p'>
				<?php
					$tabla = new HtmlTable(array('cantRows' => 6, 'cantCols' => 2, 'id' => 'tablaDatos2', 'cellSpacing' => 10));
					$tabla->getRowCellArray($rows, $cells);

					$cells[0][0]->content = '<label>Vendedor:</label>';
					$cells[0][0]->style->width = '135px';
					$cells[0][1]->content = '<input id="inputVendedor" class="textbox autoSuggestBox ' . (Usuario::logueado()->esVendedor() ? '' : 'inputForm') . ' w230" name="Vendedor" rel="vendedor" />';
					$cells[0][1]->style->width = '260px';
					$cells[1][0]->content = '<label>Temporada:</label>';
					$cells[1][1]->content = '<input id="inputTemporada" class="textbox autoSuggestBox obligatorio w230" name="Temporada" rel="temporada" />';
					$cells[2][0]->content = '<label>Descuento:</label>';
					$cells[2][1]->content = '<input id="inputDescuento" class="textbox ' . (Usuario::logueado()->esVendedor() ? '' : 'inputForm') . ' w230" validate="Porcentaje" />';
					$cells[3][0]->content = '<label>Recargo:</label>';
					$cells[3][1]->content = '<input id="inputRecargo" class="textbox ' . (Usuario::logueado()->esVendedor() ? '' : 'inputForm') . ' w230" validate="Porcentaje" />';					$cells[4][0]->content = (!Usuario::logueado()->esCliente()) ? '<label>Autorizaciones</label>' : '';
					$cells[4][1]->content = ' ';
					$cells[5][0]->colspan = 2;
					$cells[5][0]->content = '<div id="divAutorizaciones" class="customScroll pRight10"></div>';

					$tabla->create();
				?>
			</div>
		</div>
		<div id='tituloInfoNotaDePedido' class='bold white bLightOrange corner10 p5'>
			<div id='divDatosTitulo' class='fLeft'>
				<label id='labelDatosCliente' title='Cliente'></label>
				<label class='labelDatosSeparador'></label>
				<label id='labelDatosSucursal' title='Sucursal'></label>
				<label class='labelDatosSeparador'></label>
				<label id='labelDatosEstado' title='Estado'></label>
				<label class='labelDatosSeparador'></label>
				<label id='labelDatosPares' title='Pares'></label><label> pares</label>
				<label class='labelDatosSeparador'></label>
				<label id='labelDatosTotal' title='Total'></label>
				<label id='labelDatosDescuento' title='Descuento'></label>
			</div>
			<div id='divDatosEditarGuardar' class='fRight pRight15'>
				<a href='#' onclick='mostrarDatos();'><label id='labelMostrarDatos' class='cPointer s19'>+</label></a>
				<a class='borderFocusDarkOrange' href='#' onclick='ocultarDatos(true);'><label id='labelOcultarDatos' class='cPointer hidden p5'>Siguiente</label></a>
			</div>
		</div>
	</div>
	<div id='divCampos1' class='fLeft w100p hidden solapas'>
		<?php // TABLOTA ?>
	</div>
</div>
<div id='programaPie'>
	<div id='filtro' class='hidden draggableDialog'>
		<div>
			<label class='ayudaArticulos'>Artículo:</label>
			<input id='inputAyudaArticulo' class='textbox autoSuggestBox ayudaArticulos w200' name='Articulo' alt='' />
		</div>
		<div>
			<label class='ayudaArticulos'>Color:</label>
			<input id='inputAyudaColor' class='textbox autoSuggestBox ayudaArticulos w140' name='ColorPorArticulo' linkedTo='inputAyudaArticulo,Articulo' alt='' />
		</div>
		<div>
			<a id='btnBuscarAyudaArticulos' class='boton ayudaArticulos' href='#' title='Buscar'><img src="/img/botones/25/buscar.gif" /></a>
		</div>
	</div>
	<div class='botonera'>
		<?php Html::echoBotonera(array('boton' => 'buscar', 'accion' => 'funciones.buscarClick();')); ?>
		<?php Html::echoBotonera(array('boton' => 'agregar', 'accion' => 'funciones.agregarClick();', 'permiso' => 'comercial/pedidos/nota_de_pedido_vip/agregar/')); ?>
		<?php Html::echoBotonera(array('boton' => 'guardar', 'accion' => 'funciones.guardarClick();')); ?>
		<?php Html::echoBotonera(array('boton' => 'cancelar', 'accion' => 'funciones.cancelarEditarClick();', 'id' => 'btnCancelarEditar')); ?>
	</div>
</div>
